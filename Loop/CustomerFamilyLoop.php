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

use CustomerFamily\Model\Base\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\AlphaNumStringListType;
use Thelia\Type\TypeCollection;

/**
 * Class CustomerFamilyLoop
 * @package CustomerFamily\Loop
 */
class CustomerFamilyLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            new Argument(
                'code',
                new TypeCollection(
                    new AlphaNumStringListType()
                )
            ),
            Argument::createIntListTypeArgument('exclude_id'),
            Argument::createBooleanTypeArgument('is_default')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria(): ModelCriteria
    {
        $search = CustomerFamilyQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE'));

        if (null !== $id = $this->getId()) {
            $search->filterById($id, Criteria::IN);
        }

        if (null !== $code = $this->getCode()) {
            $search->filterByCode($code, Criteria::IN);
        }

        if (null !== $excludeId = $this->getExcludeId()) {
            $search->filterById($excludeId, Criteria::NOT_IN);
        }

        if (null !== $isDefault = $this->getIsDefault()) {
            $search->filterByIsDefault($isDefault);
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *`
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult): LoopResult
    {
        foreach ($loopResult->getResultDataCollection() as $customerFamily) {
            /** @var CustomerFamily $customerFamily */
            $loopResultRow = new LoopResultRow($customerFamily);
            $loopResultRow
                ->set("CUSTOMER_FAMILY_ID", $customerFamily->getId())
                ->set("CODE", $customerFamily->getCode())
                ->set("TITLE_CUSTOMER_FAMILY", $customerFamily->getVirtualColumn('i18n_TITLE'))
                ->set("IS_DEFAULT", $customerFamily->getIsDefault())
                ->set("CATEGORY_RESTRICTION_ENABLED", $customerFamily->getCategoryRestrictionEnabled())
                ->set("BRAND_RESTRICTION_ENABLED", $customerFamily->getBrandRestrictionEnabled());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
