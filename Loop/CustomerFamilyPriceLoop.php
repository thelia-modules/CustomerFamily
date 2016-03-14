<?php

namespace CustomerFamily\Loop;

use CustomerFamily\Model\CustomerFamilyPriceQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class CustomerFamilyPriceLoop
 * @package CustomerFamily\Loop
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('customer_family_id'),
            Argument::createBooleanTypeArgument('promo'),
            Argument::createBooleanTypeArgument('use_equation')
        );
    }

    /**
     * @return CustomerFamilyPriceQuery
     */
    public function buildModelCriteria()
    {
        $search = CustomerFamilyPriceQuery::create();

        if (null !== $customerFamilyId = $this->getCustomerFamilyId()) {
            $search->filterByCustomerFamilyId($customerFamilyId, Criteria::IN);
        }

        if (null !== $promo = $this->getPromo()) {
            $search->filterByPromo($promo);
        }

        if (null !== $useEquation = $this->getUseEquation()) {
            $search->filterByUseEquation($useEquation);
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CustomerFamily\Model\CustomerFamilyPrice $customerFamilyPrice */
        foreach ($loopResult->getResultDataCollection() as $customerFamilyPrice) {
            $loopResultRow = new LoopResultRow($customerFamilyPrice);

            $loopResultRow
                ->set('CUSTOMER_FAMILY_ID', $customerFamilyPrice->getCustomerFamilyId())
                ->set('PROMO', $customerFamilyPrice->getPromo())
                ->set('USE_EQUATION', $customerFamilyPrice->getUseEquation())
                ->set('AMOUNT_ADDED_BEFORE', $customerFamilyPrice->getAmountAddedBefore())
                ->set('AMOUNT_ADDED_AFTER', $customerFamilyPrice->getAmountAddedAfter())
                ->set('COEFFICIENT', $customerFamilyPrice->getMultiplicationCoefficient())
                ->set('IS_TAXED', $customerFamilyPrice->getIsTaxed());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}