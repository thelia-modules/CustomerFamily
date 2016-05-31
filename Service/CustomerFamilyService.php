<?php

namespace CustomerFamily\Service;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Thelia\Core\Security\SecurityContext;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\Currency;
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

    public function __construct(SecurityContext $securityContext, TaxEngine $taxEngine)
    {
        $this->securityContext = $securityContext;
        $this->taxEngine = $taxEngine;
    }

    /**
     * @param null $customerId
     * @return mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getCustomerCustomerFamilyId($customerId = null)
    {
        $customerFamilyId = null;

        // Get given customer's family, else logged customer's one
        if ($customerId !== null) {
            $customerFamilyId = CustomerCustomerFamilyQuery::create()
                ->filterByCustomerId($customerId)
                ->select(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID)
                ->findOne();
        } elseif ($this->securityContext->hasCustomerUser()) {
            $customerFamilyId = CustomerCustomerFamilyQuery::create()
                ->filterByCustomerId($this->securityContext->getCustomerUser()->getId())
                ->select(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID)
                ->findOne();
        }

        // If no family found, get default one
        if ($customerFamilyId === null) {
            $customerFamilyId = CustomerFamilyQuery::create()
                ->filterByIsDefault(1)
                ->select(CustomerFamilyTableMap::ID)
                ->findOne();
        }

        return $customerFamilyId;
    }

    /**
     * @param $pseId
     * @param $currencyId
     * @return \CustomerFamily\Model\ProductPurchasePrice
     */
    public function getPseProductPurchasePrice($pseId, $currencyId)
    {
        return ProductPurchasePriceQuery::create()
            ->filterByCurrencyId($currencyId)
            ->findOneByProductSaleElementsId($pseId);
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

    /**
     * @param \Thelia\Model\ProductSaleElements $pse
     * @param $customerFamilyId
     * @param null $currencyId
     * @return array
     */
    public function calculateCustomerFamilyPsePrice($pse, $customerFamilyId, $currencyId = null)
    {
        $taxCountry = $this->taxEngine->getDeliveryCountry();

        // Get default currency if no one is given
        if ($currencyId === null) {
            $currencyId = Currency::getDefaultCurrency()->getId();
        }

        // If the purchase price & its price exist
        if (null !== $productPurchasePrice = $this->getPseProductPurchasePrice($pse->getId(), $currencyId)) {
            if (null !== $productPurchasePricePrice = $productPurchasePrice->getPurchasePrice()) {
                // Initialize prices
                $price = $taxedPrice = $promoPrice = $taxedPromoPrice = null;

                // Standard price
                if (null !== $customerFamilyPrice = $this->getCustomerFamilyPrice($customerFamilyId, 0, 1)) {
                    // Calculate price
                    $price = round(
                        ($productPurchasePrice->getPurchasePrice() + $customerFamilyPrice->getAmountAddedBefore())
                        * $customerFamilyPrice->getMultiplicationCoefficient()
                        + $customerFamilyPrice->getAmountAddedAfter(),
                        2
                    );

                    $pse->setVirtualColumn('CUSTOMER_FAMILY_PRICE', $price);

                    // Tax
                    try {
                        $taxedPrice = $pse->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PRICE');
                    } catch (TaxEngineException $e) {
                        $taxedPrice = null;
                    }
                }

                // Promo price
                if (null !== $customerFamilyPromoPrice = $this->getCustomerFamilyPrice($customerFamilyId, 1, 1)) {
                    // Calculate promo price
                    $promoPrice = round(
                        ($productPurchasePrice->getPurchasePrice() + $customerFamilyPromoPrice->getAmountAddedBefore())
                        * $customerFamilyPromoPrice->getMultiplicationCoefficient()
                        + $customerFamilyPromoPrice->getAmountAddedAfter(),
                        2
                    );

                    $pse->setVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE', $promoPrice);

                    // Tax
                    try {
                        $taxedPromoPrice = $pse->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PROMO_PRICE');
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

        return null;
    }

    /**
     * @param \Thelia\Model\ProductSaleElements $pse
     * @param null $customerId
     * @param null $currencyId
     * @return array
     */
    public function calculateCustomerPsePrice($pse, $customerId = null, $currencyId = null)
    {
        // Get customer's family
        $customerFamilyId = $this->getCustomerCustomerFamilyId($customerId);

        return $this->calculateCustomerFamilyPsePrice($pse, $customerFamilyId, $currencyId);
    }
}