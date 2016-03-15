<?php

namespace CustomerFamily\Service;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Thelia\Core\Security\SecurityContext;
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
    public function getCustomerFamilyId($customerId = null)
    {
        if ($customerId !== null) {
            return CustomerCustomerFamilyQuery::create()
                ->filterByCustomerId($customerId)
                ->select(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID)
                ->findOne();
        }

        $securityContext = $this->securityContext;

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

    public function getPsePurchasePrice($pseId, $currencyId)
    {
        return ProductPurchasePriceQuery::create()
            ->filterByCurrencyId($currencyId)
            ->findOneByProductSaleElementsId($pseId);
    }

    public function getCustomerFamilyPrice($customerFamilyId, $isPromo)
    {
        return CustomerFamilyPriceQuery::create()
            ->filterByPromo($isPromo)
            ->findOneByCustomerFamilyId($customerFamilyId);
    }

    public function getUsingEquationCustomerFamilyPrice($customerFamilyId, $isPromo)
    {
        return CustomerFamilyPriceQuery::create()
            ->filterByPromo($isPromo)
            ->filterByUseEquation(1)
            ->findOneByCustomerFamilyId($customerFamilyId);
    }

    /**
     * @param   \Thelia\Model\ProductSaleElements   $pse
     * @param   null    $customerId
     * @return array
     */
    public function calculateCustomerProductPrice($pse, $customerId = null)
    {
        $currencyId = Currency::getDefaultCurrency()->getId();

        $purchasePrice = $this->getPsePurchasePrice($pse->getId(), $currencyId);

        $customerFamilyPrice = $this->getCustomerFamilyPrice($this->getCustomerFamilyId($customerId), $pse->getPromo());

        $price = ($purchasePrice->getPurchasePrice() + $customerFamilyPrice->getAmountAddedBefore())
            * $customerFamilyPrice->getMultiplicationCoefficient()
            + $customerFamilyPrice->getAmountAddedAfter();

        $pse->setVirtualColumn('CUSTOMER_FAMILY_PRICE', $price);

        $taxCountry = $this->taxEngine->getDeliveryCountry();
        $taxedPrice = $price;

        if ($customerFamilyPrice->getIsTaxed()) {
            $taxedPrice = $pse->getTaxedPrice($taxCountry, 'CUSTOMER_FAMILY_PRICE');
        }

        return [$price, $taxedPrice];
    }
}