<?php

namespace CustomerFamily\Service;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Thelia\Core\Security\SecurityContext;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\CartItem;
use Thelia\Model\Currency;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\TaxEngine\TaxEngine;

/**
 * Class CustomerFamilyService
 * @package CustomerFamily\Service
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyService
{
    protected $securityContext;
    protected $taxEngine;

    public function __construct(
        SecurityContext $securityContext,
        TaxEngine $taxEngine
    )
    {
        $this->securityContext = $securityContext;
        $this->taxEngine = $taxEngine;
    }

    public function getCustomerCustomerFamilyId(int $customerId = null):? int
    {
        $customerFamily = null;

        if (null === $customerId) {
            $customerId = $this->securityContext->getCustomerUser()?->getId();
        }

        if (null !== $customerId) {
            $customerFamily = CustomerFamilyQuery::create()
                ->useCustomerCustomerFamilyQuery()
                    ->filterByCustomerId($customerId)
                ->endUse()
                ->findOne();
        }

        // If no family found, get default one
        if ($customerFamily === null) {
            $customerFamily = CustomerFamilyQuery::create()
                ->filterByIsDefault(1)
                ->findOne();
        }

        return $customerFamily?->getId();
    }

    /**
     * @param $pseId
     * @param $currencyId
     * @return \CustomerFamily\Model\ProductPurchasePrice|\Thelia\Model\ProductPrice
     */
    public function getPseProductPurchasePrice($pseId, $currencyId)
    {
        return ProductPurchasePriceQuery::create()
            ->filterByCurrencyId($currencyId)
            ->findOneByProductSaleElementsId($pseId);
    }

    /**
     * @param $pseId
     * @param $currencyId
     * @return \Thelia\Model\ProductPrice
     */
    public function getPseProductPrice($pseId, $currencyId)
    {
        return ProductPriceQuery::create()
            ->filterByCurrencyId($currencyId)
            ->findOneByProductSaleElementsId($pseId);
    }

    /**
     * @param $pseId
     * @param $currencyId
     * @return string
     */
    public function getPurchasePrice($pseId, $currencyId)
    {
        $mode = CustomerFamily::getConfigValue('customer_family_price_mode', 0);
        if ($mode == 1) {
            $pseProductPrice = $this->getPseProductPrice($pseId, $currencyId);
            return $pseProductPrice !== null ? $pseProductPrice->getPrice() : null;
        }
        $pseProductPurchasePrice = $this->getPseProductPurchasePrice($pseId, $currencyId);
        return $pseProductPurchasePrice !== null  ? $pseProductPurchasePrice->getPurchasePrice() : null;
    }

    /**
     * @param $customerFamilyId
     * @param int $isPromo
     * @param null $useEquation
     * @return \CustomerFamily\Model\CustomerFamilyPrice
     */
    public function getCustomerFamilyPrice($customerFamilyId, $isPromo = 0, $useEquation = null)
    {
        $search = CustomerFamilyPriceQuery::create()
            ->filterByPromo($isPromo)
            ->filterByCustomerFamilyId($customerFamilyId);

        if ($useEquation !== null) {
            $search->filterByUseEquation($useEquation);
        }

        return $search->findOne();
    }

    public function setCustomerFamilyPriceToCartItem(
        CartItem $cartItem,
        $customerFamilyId = null,
        $currencyId = null
    ): CartItem
    {
        $productSaleElements = $cartItem->getProductSaleElements();

        $prices = $this->calculateCustomerFamilyPsePrice($productSaleElements, $customerFamilyId, $currencyId);

        if (isset($prices['promo'])) {
            $cartItem->setPromo($prices['promo']);
        }

        if (isset($prices['price'])) {
            $cartItem->setPrice($prices['price']);
        }

        if (isset($prices['promoPrice'])) {
            $cartItem->setPromoPrice($prices['promoPrice']);
        }

        return $cartItem;
    }

    public function calculateCustomerFamilyPsePrice(
        ProductSaleElements $productSaleElements,
        $customerFamilyId = null,
        $currencyId = null
    ): array
    {
        if (null === $customerFamilyId) {
            $customerFamilyId = $this->getCustomerCustomerFamilyId();
        }

        $taxCountry = $this->taxEngine->getDeliveryCountry();

        // Get default currency if no one is given
        if ($currencyId === null) {
            $currencyId = Currency::getDefaultCurrency()->getId();
        }

        $customerFamilyProductPrice = CustomerFamilyProductPriceQuery::create()
            ->filterByCustomerFamilyId($customerFamilyId)
            ->filterByProductSaleElementsId($productSaleElements->getId())
            ->findOne();

        if (null !== $customerFamilyProductPrice) {
            $productSaleElements->setVirtualColumn('CUSTOMER_FAMILY_PRICE', $customerFamilyProductPrice->getPrice());
            $productSaleElements->setVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE', $customerFamilyProductPrice->getPromoPrice());

            try {
                $taxedPrice = $productSaleElements->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PRICE');
                $taxedPromoPrice = $productSaleElements->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PROMO_PRICE');
            } catch (TaxEngineException $e) {
                $taxedPrice = $customerFamilyProductPrice->getPrice();
                $taxedPromoPrice = $customerFamilyProductPrice->getPromoPrice();
            }

            return [
                'promo' => $customerFamilyProductPrice->getPromo(),
                'price' => $customerFamilyProductPrice->getPrice(),
                'taxedPrice' => $taxedPrice,
                'promoPrice' => $customerFamilyProductPrice->getPromoPrice(),
                'taxedPromoPrice' => $taxedPromoPrice
            ];
        }

        $productPurchasePrice = $this->getPurchasePrice($productSaleElements->getId(), $currencyId);

        // If the purchase price & its price exist
        if (null === $productPurchasePrice) {
            return [];
        }

        $productPurchasePrice = (float)$productPurchasePrice;

        // Initialize prices
        $price = $taxedPrice = $promoPrice = $taxedPromoPrice = null;

        // Standard price
        if (null !== $customerFamilyPrice = $this->getCustomerFamilyPrice($customerFamilyId, 0, 1)) {
            // Calculate price
            $price = round(
                ($productPurchasePrice + $customerFamilyPrice->getAmountAddedBefore())
                * $customerFamilyPrice->getMultiplicationCoefficient()
                + $customerFamilyPrice->getAmountAddedAfter(),
                2
            );

            $productSaleElements->setVirtualColumn('CUSTOMER_FAMILY_PRICE', $price);

            // Tax
            try {
                $taxedPrice = $productSaleElements->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PRICE');
            } catch (TaxEngineException $e) {
                $taxedPrice = null;
            }
        }

        // Promo price
        if (null !== $customerFamilyPromoPrice = $this->getCustomerFamilyPrice($customerFamilyId, 1, 1)) {
            // Calculate promo price
            $promoPrice = round(
                ($productPurchasePrice + $customerFamilyPromoPrice->getAmountAddedBefore())
                * $customerFamilyPromoPrice->getMultiplicationCoefficient()
                + $customerFamilyPromoPrice->getAmountAddedAfter(),
                2
            );

            $productSaleElements->setVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE', $promoPrice);

            // Tax
            try {
                $taxedPromoPrice = $productSaleElements->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PROMO_PRICE');
            } catch (TaxEngineException $e) {
                $taxedPromoPrice = null;
            }
        }

        return [
            'price' => $price,
            'taxedPrice' => $taxedPrice,
            'promoPrice' => $promoPrice,
            'taxedPromoPrice' => $taxedPromoPrice
        ];
    }
}
