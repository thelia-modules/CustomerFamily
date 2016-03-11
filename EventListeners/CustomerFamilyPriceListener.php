<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use CustomerFamily\Model\Map\ProductPurchasePriceTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\Loop\LoopExtendsParseResultsEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\SecurityContext;
use Thelia\Exception\TaxEngineException;
use Thelia\Model\Currency;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\TaxEngine\TaxEngine;

/**
 * Class CustomerFamilyPriceListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceListener implements EventSubscriberInterface
{
    protected $securityContext;
    protected $taxEngine;

    public function __construct(SecurityContext $securityContext, TaxEngine $taxEngine)
    {
        $this->securityContext = $securityContext;
        $this->taxEngine = $taxEngine;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product') => ['extendProductModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_PARSE_RESULTS, 'product') => ['extendProductParseResult', 128]
        ];
    }

    /**
     * @param LoopExtendsBuildModelCriteriaEvent $event
     * @return mixed
     */
    public function extendProductModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        // Get customer's family
        if (null !== $customerFamilyId = $this->getCustomerFamilyId($this->securityContext)) {
            // Get associated prices
            $customerFamilyPrice = $this->getCustomerFamilyPrice($customerFamilyId);
            $customerFamilyPromoPrice = $this->getCustomerFamilyPromoPrice($customerFamilyId);

            if ($customerFamilyPrice !== null || $customerFamilyPromoPrice !== null) {
                // Get search & currency
                $search = $event->getModelCriteria();
                $currencyId = Currency::getDefaultCurrency()->getId();

                // Link each PSE with its corresponding purchase price, according to the PSE id
                $productPurchasePriceJoin = new Join();
                $productPurchasePriceJoin->addExplicitCondition(
                    ProductSaleElementsTableMap::TABLE_NAME,
                    'ID',
                    'pse',
                    ProductPurchasePriceTableMap::TABLE_NAME,
                    'PRODUCT_SALE_ELEMENTS_ID'
                );
                $productPurchasePriceJoin->setJoinType(Criteria::LEFT_JOIN);

                // Add the link to the search, and add a link condition based on the currency
                $search
                    ->addJoinObject($productPurchasePriceJoin, 'purchase_price_join')
                    ->addJoinCondition('purchase_price_join', ProductPurchasePriceTableMap::CURRENCY_ID.' = ?', $currencyId, null, \PDO::PARAM_INT);

                // Check if products' prices have to be changed depending on the customer's family
                if ($customerFamilyPrice !== null) {
                    $search
                        ->withColumn(
                            'IF (' . ProductPurchasePriceTableMap::PURCHASE_PRICE . ' IS NULL,
                                0,
                                (' .
                                    ProductPurchasePriceTableMap::PURCHASE_PRICE .
                                    '+' . $customerFamilyPrice->getAmountAddedBefore() .
                                ') * ' . $customerFamilyPrice->getMultiplicationCoefficient() .
                                ' + ' . $customerFamilyPrice->getAmountAddedAfter() .
                            ')',
                            'CUSTOMER_FAMILY_PRICE'
                        );
                }

                // Check if products' promo prices have to be changed depending on the customer's family
                if ($customerFamilyPromoPrice !== null) {
                    $search
                        ->withColumn(
                            'IF (' . ProductPurchasePriceTableMap::PURCHASE_PRICE . ' IS NULL,
                                0,
                                (' .
                                    ProductPurchasePriceTableMap::PURCHASE_PRICE .
                                    '+' . $customerFamilyPromoPrice->getAmountAddedBefore() .
                                ') * ' . $customerFamilyPromoPrice->getMultiplicationCoefficient() .
                                ' + ' . $customerFamilyPromoPrice->getAmountAddedAfter() .
                            ')',
                            'CUSTOMER_FAMILY_PROMO_PRICE'
                        );
                }
            }
        }
    }

    /**
     * @param LoopExtendsParseResultsEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function extendProductParseResult(LoopExtendsParseResultsEvent $event)
    {
        // Get customer's family
        if (null !== $customerFamilyId = $this->getCustomerFamilyId($this->securityContext)) {
            // Get associated prices
            $customerFamilyPrice = $this->getCustomerFamilyPrice($customerFamilyId);
            $customerFamilyPromoPrice = $this->getCustomerFamilyPromoPrice($customerFamilyId);

            if ($customerFamilyPrice !== null || $customerFamilyPromoPrice !== null) {
                // Get loop result, tax country & security context
                $loopResult = $event->getLoopResult();
                $taxCountry = $this->taxEngine->getDeliveryCountry();
                $securityContext = $this->securityContext;

                foreach ($loopResult as $loopResultRow) {
                    /** @var \Thelia\Model\Product $product */
                    $product = $loopResultRow->model;

                    if (!empty($product->getVirtualColumn('CUSTOMER_FAMILY_PRICE')) ||
                        !empty($product->getVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE'))
                    ) {
                        $this->changeProductPrice(
                            $product,
                            $loopResultRow,
                            $customerFamilyPrice,
                            $customerFamilyPromoPrice,
                            $taxCountry,
                            $securityContext
                        );
                    }
                }
            }
        }
    }

    /**
     * @param \Thelia\Core\Security\SecurityContext $securityContext
     * @return mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getCustomerFamilyId($securityContext)
    {
        // If there is a logged customer
        if ($securityContext->hasCustomerUser()) {
            $customerFamilyId = CustomerCustomerFamilyQuery::create()
                ->filterByCustomerId($securityContext->getCustomerUser()->getId())
                ->select(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID)
                ->findOne();
        } else {
            $customerFamilyId = CustomerFamilyQuery::create()
                ->filterByIsDefault(1)
                ->select(CustomerFamilyTableMap::ID)
                ->findOne();
        }

        return $customerFamilyId;
    }

    /**
     * @param $customerFamilyId
     * @return \CustomerFamily\Model\CustomerFamilyPrice
     */
    protected function getCustomerFamilyPrice($customerFamilyId)
    {
        return CustomerFamilyPriceQuery::create()
            ->filterByPromo(0)
            ->filterByUseEquation(1)
            ->findOneByCustomerFamilyId($customerFamilyId);
    }

    /**
     * @param $customerFamilyId
     * @return \CustomerFamily\Model\CustomerFamilyPrice
     */
    protected function getCustomerFamilyPromoPrice($customerFamilyId)
    {
        return CustomerFamilyPriceQuery::create()
            ->filterByPromo(1)
            ->filterByUseEquation(1)
            ->findOneByCustomerFamilyId($customerFamilyId);
    }

    /**
     * @param \Thelia\Model\Product                         $product
     * @param \Thelia\Core\Template\Element\LoopResultRow   $loopResultRow
     * @param \CustomerFamily\Model\CustomerFamilyPrice     $customerFamilyPrice
     * @param \CustomerFamily\Model\CustomerFamilyPrice     $customerFamilyPromoPrice
     * @param \Thelia\Model\Country                         $taxCountry
     * @param SecurityContext                               $securityContext
     */
    protected function changeProductPrice(
        $product,
        $loopResultRow,
        $customerFamilyPrice,
        $customerFamilyPromoPrice,
        $taxCountry,
        SecurityContext $securityContext
    ) {
        $price = $loopResultRow->get('PRICE');
        $priceTax = $loopResultRow->get('PRICE_TAX');
        $taxedPrice = $loopResultRow->get('TAXED_PRICE');
        $promoPrice = $loopResultRow->get('PROMO_PRICE');
        $promoPriceTax = $loopResultRow->get('PROMO_PRICE_TAX');
        $taxedPromoPrice = $loopResultRow->get('TAXED_PROMO_PRICE');

        // Replace price
        if (!empty($product->getVirtualColumn('CUSTOMER_FAMILY_PRICE'))) {
            $price = $product->getVirtualColumn('CUSTOMER_FAMILY_PRICE');

            // If the customer has permanent discount, apply it
            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $price = $price * (1 - ($securityContext->getCustomerUser()->getDiscount() / 100));
            }
            $taxedPrice = $price;

            // Tax price
            /** @var \CustomerFamily\Model\CustomerFamilyPrice $customerFamilyPrice */
            if ($customerFamilyPrice->getIsTaxed()) {
                try {
                    $taxedPrice = $product->getTaxedPrice($taxCountry, $price);
                } catch (TaxEngineException $e) {
                    $taxedPrice = null;
                }
            }

            $priceTax = $taxedPrice - $price;

            // Set new price & tax into the loop
            $loopResultRow
                ->set("PRICE", $price)
                ->set("PRICE_TAX", $priceTax)
                ->set("TAXED_PRICE", $taxedPrice);
        }

        // Replace promo price
        if (!empty($product->getVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE'))) {
            $promoPrice = $product->getVirtualColumn('CUSTOMER_FAMILY_PROMO_PRICE');

            // If the customer has permanent discount, apply it
            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $promoPrice = $promoPrice * (1 - ($securityContext->getCustomerUser()->getDiscount() / 100));
            }

            $taxedPromoPrice = $promoPrice;

            // Tax price
            /** @var \CustomerFamily\Model\CustomerFamilyPrice $customerFamilyPromoPrice */
            if ($customerFamilyPromoPrice->getIsTaxed()) {
                try {
                    $taxedPromoPrice = $product->getTaxedPromoPrice(
                        $taxCountry,
                        $promoPrice
                    );
                } catch (TaxEngineException $e) {
                    $taxedPromoPrice = null;
                }
            }

            $promoPriceTax = $taxedPromoPrice - $promoPrice;

            // Set new price & tax into the loop
            $loopResultRow
                ->set("PROMO_PRICE", $promoPrice)
                ->set("PROMO_PRICE_TAX", $promoPriceTax)
                ->set("TAXED_PROMO_PRICE", $taxedPromoPrice);
        }

        $loopResultRow
            ->set("BEST_PRICE", $promoPrice < $price ? $promoPrice : $price)
            ->set("BEST_PRICE_TAX", $promoPriceTax < $priceTax ? $promoPriceTax : $priceTax)
            ->set("BEST_TAXED_PRICE", $taxedPromoPrice < $taxedPrice ? $taxedPromoPrice : $taxedPrice);
    }
}