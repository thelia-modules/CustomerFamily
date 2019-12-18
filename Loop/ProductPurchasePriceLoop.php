<?php

namespace CustomerFamily\Loop;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Product;
use Thelia\Model\ProductPrice;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\ProductQuery;

/**
 * Class ProductPurchasePriceLoop
 * @package CustomerFamily\Loop
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class ProductPurchasePriceLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('pse_id', null, true),
            Argument::createIntTypeArgument('currency_id', null, true)
        );
    }

    /**
     * @return ProductPurchasePriceQuery|\Propel\Runtime\ActiveQuery\ModelCriteria|\Thelia\Model\ProductPrice
     */
    public function buildModelCriteria()
    {

        if(CustomerFamily::getConfigValue('customer_family_price_mode', null)){
            return $search = ProductPriceQuery::create()
                ->filterByCurrencyId($this->getCurrencyId())
                ->filterByProductSaleElementsId($this->getPseId());
        }
        return $search = ProductPurchasePriceQuery::create()
            ->filterByProductSaleElementsId($this->getPseId())
            ->filterByCurrencyId($this->getCurrencyId());

    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CustomerFamily\Model\ProductPurchasePrice $productPurchasePrice */
        foreach ($loopResult->getResultDataCollection() as $productPurchasePrice) {
            $loopResultRow = new LoopResultRow($productPurchasePrice);

            $loopResultRow
                ->set('PSE_ID', $this->getPseId())
                ->set('CURRENCY_ID', $this->getCurrencyId());

            if (is_a($productPurchasePrice, '\CustomerFamily\Model\ProductPurchasePrice')){
                $loopResultRow->set('PURCHASE_PRICE', $productPurchasePrice->getPurchasePrice());
            } else {
                /** @var ProductPrice  $productPurchasePrice*/
                $loopResultRow->set('PURCHASE_PRICE', $productPurchasePrice->getPrice());
            }

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

}