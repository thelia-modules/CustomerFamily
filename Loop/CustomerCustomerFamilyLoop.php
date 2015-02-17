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
use Propel\Runtime\ActiveQuery\Criteria;
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
                ->set("SIRET", $customerCustomerFamily->getSiret())
                ->set("VAT", $customerCustomerFamily->getVat())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('customer_id'),
            Argument::createIntListTypeArgument('customer_family_id')
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

        return $search;
    }
}
