<?php
/*************************************************************************************/
/*      This file is part of the module CustomerFamily                               */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerFamily\Loop;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class CustomerCustomerFamilyLoop
 * @package CustomerFamily\Loop
 */
class CustomerCustomerFamilyLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('customer_id'),
            Argument::createIntListTypeArgument('customer_family_id'),
            Argument::createAnyTypeArgument('customer_family_code')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = CustomerCustomerFamilyQuery::create();

        if (null !== $customerId = $this->getCustomerId()) {
            $search->filterByCustomerId($customerId, Criteria::IN);
        }

        if (null !== $customerFamilyId = $this->getCustomerFamilyId()) {
            $search->filterByCustomerFamilyId($customerFamilyId, Criteria::IN);
        }

        if (null !== $customerFamilyCode = $this->getCustomerFamilyCode()) {
            $join =  new Join(
                CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID,
                CustomerFamilyTableMap::ID,
                Criteria::INNER_JOIN
            );

            $search->addJoinObject($join, "customer_family_join")
                ->addJoinCondition("customer_family_join", CustomerFamilyTableMap::CODE." = '$customerFamilyCode'");
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
        foreach ($loopResult->getResultDataCollection() as $customerCustomerFamily) {
            /** @var \CustomerFamily\Model\CustomerCustomerFamily $customerCustomerFamily */
            $loopResultRow = new LoopResultRow($customerCustomerFamily);
            $loopResultRow
                ->set("CUSTOMER_FAMILY_ID", $customerCustomerFamily->getCustomerFamilyId())
                ->set("CUSTOMER_ID", $customerCustomerFamily->getCustomerId())
                ->set("COMPANY_NAME", $customerCustomerFamily->getCompanyName())
                ->set("SIRET", $customerCustomerFamily->getSiret())
                ->set("VAT", $customerCustomerFamily->getVat())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
