<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Model\Map\CustomerFamilyProductPriceTableMap;
use CustomerFamily\Model\Map\ProductPurchasePriceTableMap;
use CustomerFamily\Service\CustomerFamilyService;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\Loop\LoopExtendsParseResultsEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\SecurityContext;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\Currency;
use Thelia\Model\Map\ProductPriceTableMap;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\TaxEngine\TaxEngine;

class CustomerFamilyPriceListener implements EventSubscriberInterface
{
    protected $securityContext;
    protected $taxEngine;
    protected $customerFamilyService;

    public function __construct(SecurityContext $securityContext, TaxEngine $taxEngine, CustomerFamilyService $customerFamilyService)
    {
        $this->securityContext = $securityContext;
        $this->taxEngine = $taxEngine;
        $this->customerFamilyService = $customerFamilyService;
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product') => ['extendProductModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'product') => ['extendProductParseResult', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product_sale_elements') => ['extendProductModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'product_sale_elements') => ['extendProductParseResult', 128]
        ];
    }

    public function extendProductModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        if ($event->getLoop()->getBackendContext()) {
            return;
        }

        $customerFamilyId = $this->customerFamilyService->getCustomerCustomerFamilyId();

        if (null === $customerFamilyId) {
            return;
        }

        $this->addCustomerFamilyProductPriceColumns($event->getModelCriteria(), $customerFamilyId);

        // Get associated prices
        $customerFamilyPrice = $this->customerFamilyService->getCustomerFamilyPrice($customerFamilyId, 0, 1);
        $customerFamilyPromoPrice = $this->customerFamilyService->getCustomerFamilyPrice($customerFamilyId, 1, 1);
        $useProductPrice = CustomerFamily::getConfigValue('customer_family_price_mode', null);

        if ($customerFamilyPrice === null && $customerFamilyPromoPrice === null) {
            return;
        }

        // Get currency & search
        $currencyId = Currency::getDefaultCurrency()->getId();
        $search = $event->getModelCriteria();
        $searchType = $search instanceof ProductQuery ? 'pse' : null;

        $tableName = $useProductPrice ?  ProductPriceTableMap::TABLE_NAME : ProductPurchasePriceTableMap::TABLE_NAME;
        $colCurrencyId = $useProductPrice ? ProductPriceTableMap::COL_CURRENCY_ID : ProductPurchasePriceTableMap::COL_CURRENCY_ID;
        $colPrice = $useProductPrice ? ProductPriceTableMap::COL_PRICE : ProductPurchasePriceTableMap::COL_PURCHASE_PRICE;

        // Link each PSE with its corresponding purchase price, according to the PSE id
        $productPurchasePriceJoin = new Join();
        $productPurchasePriceJoin->addExplicitCondition(
            ProductSaleElementsTableMap::TABLE_NAME,
            'ID',
            $searchType,
            $tableName,
            'PRODUCT_SALE_ELEMENTS_ID'
        );
        $productPurchasePriceJoin->setJoinType(Criteria::LEFT_JOIN);

        // Add the link to the search, and add a link condition based on the currency
        $search
            ->addJoinObject($productPurchasePriceJoin, 'purchase_price_join')
            ->addJoinCondition('purchase_price_join', $colCurrencyId.' = ?', $currencyId, null, \PDO::PARAM_INT);

        // Add
        $this->addProductCalculatedPrice($customerFamilyPrice, $search, $colPrice);
        $this->addProductCalculatedPromoPrice($customerFamilyPromoPrice, $search, $colPrice);
    }

    public function extendProductParseResult(LoopExtendsParseResultsEvent $event)
    {
        if ($event->getLoop()->getBackendContext()) {
            return;
        }

        $customerFamilyId = $this->customerFamilyService->getCustomerCustomerFamilyId();

        if (null === $customerFamilyId) {
            return;
        }

        // Get loop result, tax country & security context
        $loopResult = $event->getLoopResult();
        $taxCountry = $this->taxEngine->getDeliveryCountry();
        $securityContext = $this->securityContext;

        foreach ($loopResult as $loopResultRow) {
            $product = $loopResultRow->model;
            $this->changeProductPrice(
                $product,
                $loopResultRow,
                $taxCountry,
                $securityContext
            );
        }
    }

    private function addProductCalculatedPrice($customerFamilyPrice, $search, $colPrice)
    {
        // Check if products' prices have to be changed depending on the customer's family
        if ($customerFamilyPrice !== null) {
            $search
                ->withColumn(
                    'IF (' . $colPrice . ' IS NULL,
                        NULL,
                        (' .
                            $colPrice .
                            '+' . $customerFamilyPrice->getAmountAddedBefore() .
                        ') * ' . $customerFamilyPrice->getMultiplicationCoefficient() .
                        ' + ' . $customerFamilyPrice->getAmountAddedAfter() .
                    ')',
                    'CUSTOMER_FAMILY_PRICE'
                );
        }
    }

    private function addProductCalculatedPromoPrice($customerFamilyPromoPrice, $search, $colPrice)
    {
        // Check if products' promo prices have to be changed depending on the customer's family
        if ($customerFamilyPromoPrice !== null) {
            $search
                ->withColumn(
                    'IF (' . $colPrice . ' IS NULL,
                        NULL,
                        (' .
                            $colPrice .
                            '+' . $customerFamilyPromoPrice->getAmountAddedBefore() .
                        ') * ' . $customerFamilyPromoPrice->getMultiplicationCoefficient() .
                        ' + ' . $customerFamilyPromoPrice->getAmountAddedAfter() .
                    ')',
                    'CUSTOMER_FAMILY_PROMO_PRICE'
                );
        }
    }

    private function addCustomerFamilyProductPriceColumns(ModelCriteria $query, int $customerFamilyId)
    {
        $pseAlias = $query instanceof ProductQuery ? 'pse' : null;

        $customerFamilyProductPriceJoin = new Join(null, null, Criteria::LEFT_JOIN);
        $customerFamilyProductPriceJoin->addExplicitCondition(
            ProductSaleElementsTableMap::TABLE_NAME,
            'ID',
            $pseAlias,
            CustomerFamilyProductPriceTableMap::TABLE_NAME,
            'product_sale_elements_id'
        );

        $query
            ->addJoinObject($customerFamilyProductPriceJoin, 'customer_family_product_price_join')
            ->addJoinCondition('customer_family_product_price_join', CustomerFamilyProductPriceTableMap::COL_CUSTOMER_FAMILY_ID.' = ?', $customerFamilyId, null, \PDO::PARAM_INT);

        $query
            ->withColumn(
                CustomerFamilyProductPriceTableMap::COL_PROMO,
                'CUSTOMER_FAMILY_PRODUCT_PROMO'
            )->withColumn(
                CustomerFamilyProductPriceTableMap::COL_PRICE,
                'CUSTOMER_FAMILY_PRODUCT_PRICE'
            )->withColumn(
                CustomerFamilyProductPriceTableMap::COL_PROMO_PRICE,
                'CUSTOMER_FAMILY_PRODUCT_PROMO_PRICE'
            );
    }

    private function changeProductPrice(
        ActiveRecordInterface $product,
        $loopResultRow,
        $taxCountry,
        SecurityContext $securityContext
    ) {
        $price = $loopResultRow->get('PRICE');
        $priceTax = $loopResultRow->get('PRICE_TAX');
        $taxedPrice = $loopResultRow->get('TAXED_PRICE');
        $promoPrice = $loopResultRow->get('PROMO_PRICE');
        $promoPriceTax = $loopResultRow->get('PROMO_PRICE_TAX');
        $taxedPromoPrice = $loopResultRow->get('TAXED_PROMO_PRICE');

        $newPrice = null;
        $priceVirtualColumn = null;

        // Fist check if the product has a virtual column for the price
        if ($product->hasVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PRICE') && !empty($product->getVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PRICE'))) {
            $newPrice = $product->getVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PRICE');
            $priceVirtualColumn = 'CUSTOMER_FAMILY_PRODUCT_PRICE';
        }
        // Else if the family as global price modification
        if (null === $newPrice && $product->hasVirtualColumn('CUSTOMER_FAMILY_PRICE') && !empty($product->getVirtualColumn('CUSTOMER_FAMILY_PRICE'))) {
            $newPrice = $product->getVirtualColumn('CUSTOMER_FAMILY_PRICE');
            $priceVirtualColumn = 'CUSTOMER_FAMILY_PRICE';
        }

        // Do same thing for promo price
        $newPromoPrice = null;
        $promoPriceVirtualColumn = null;
        if ($product->hasVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PROMO_PRICE') && !empty($product->getVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PROMO_PRICE'))) {
            $newPromoPrice = $product->getVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PROMO_PRICE');
            $promoPriceVirtualColumn = 'CUSTOMER_FAMILY_PRODUCT_PROMO_PRICE';
        }
        if (null === $newPrice && $product->hasVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE') && !empty($product->getVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE'))) {
            $newPromoPrice = $product->getVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE');
            $promoPriceVirtualColumn = 'CUSTOMER_FAMILY_PROMO_PRICE';
        }

        if (!empty($newPrice)) {
            $price = round($newPrice, 2);

            // If the customer has permanent discount, apply it
            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $price = $price * (1 - ($securityContext->getCustomerUser()->getDiscount() / 100));
            }

            // Tax price
            try {
                // If $product is a Product, getTaxedPrice() takes a Country and a price as arguments
                // Else if $product is a ProductSaleElements, getTaxedPrice() takes a Country and the price virtual column name as arguments
                if ($product instanceof Product) {
                    $taxedPrice = $product->getTaxedPrice($taxCountry, $price);
                } elseif ($product instanceof ProductSaleElements) {
                    $taxedPrice = $product->getTaxedPrice($taxCountry, $priceVirtualColumn);
                }
            } catch (TaxEngineException $e) {
                $taxedPrice = $price;
            }

            $priceTax = $taxedPrice - $price;

            // Set new price & tax into the loop
            $loopResultRow
                ->set("PRICE", $price)
                ->set("PRICE_TAX", $priceTax)
                ->set("TAXED_PRICE", $taxedPrice);
        }

        if (!empty($newPromoPrice)) {
            $promoPrice = round($newPromoPrice, 2);

            // If the customer has permanent discount, apply it
            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $promoPrice = $promoPrice * (1 - ($securityContext->getCustomerUser()->getDiscount() / 100));
            }

            // Tax promo price
            try {
                // If $product is a Product, getTaxedPrice() takes a Country and a price as arguments
                // Else if $product is a ProductSaleElements, getTaxedPrice() takes a Country and the price virtual column name as arguments
                if ($product instanceof Product) {
                    $taxedPromoPrice = $product->getTaxedPromoPrice($taxCountry, $promoPrice);
                } elseif ($product instanceof ProductSaleElements) {
                    $taxedPromoPrice = $product->getTaxedPromoPrice($taxCountry, $promoPriceVirtualColumn);
                }
            } catch (TaxEngineException $e) {
                $taxedPromoPrice = null;
            }

            $promoPriceTax = $taxedPromoPrice - $promoPrice;

            // Set new promo price & tax into the loop
            $loopResultRow
                ->set("PROMO_PRICE", $promoPrice)
                ->set("PROMO_PRICE_TAX", $promoPriceTax)
                ->set("TAXED_PROMO_PRICE", $taxedPromoPrice);
        }

        $isPromo = $product->hasVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PROMO')
            ? $product->getVirtualColumn('CUSTOMER_FAMILY_PRODUCT_PROMO')
            : null;

        if (empty($isPromo) && $product->hasVirtualColumn('is_promo')) {
            $isPromo = $product->getVirtualColumn('is_promo');
        }

        // If current row is a product
        if ($product instanceof Product) {
            $loopResultRow
                ->set("BEST_PRICE", $isPromo ? $promoPrice : $price)
                ->set("BEST_PRICE_TAX", $isPromo ? $promoPriceTax : $priceTax)
                ->set("BEST_TAXED_PRICE", $isPromo ? $taxedPromoPrice : $taxedPrice);
        }
    }
}