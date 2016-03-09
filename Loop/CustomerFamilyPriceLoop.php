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

class CustomerFamilyPriceLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(Argument::createIntListTypeArgument('customer_family_id'));
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
                ->set('AMOUNT_ADDED_BEFORE', $customerFamilyPrice->getAmountAddedBefore())
                ->set('AMOUNT_ADDED_AFTER', $customerFamilyPrice->getAmountAddedAfter())
                ->set('COEFFICIENT', $customerFamilyPrice->getMultiplicationCoefficient())
                ->set('IS_TAXED', $customerFamilyPrice->getIsTaxed());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}