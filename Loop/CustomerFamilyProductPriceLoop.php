<?php

namespace CustomerFamily\Loop;

use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Currency;
use Thelia\Model\ProductSaleElementsQuery;

/**
 * Class CustomerFamilyProductPriceLoop
 * @package CustomerFamily\Loop
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyProductPriceLoop extends BaseLoop implements ArraySearchLoopInterface
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
            Argument::createIntTypeArgument('currency_id', Currency::getDefaultCurrency()->getId()),
            Argument::createIntTypeArgument('customer_family_id', null, true)
        );
    }

    /**
     * this method returns an array
     *
     * @return array
     */
    public function buildArray()
    {
        $items = [];

        $items['pse_id'] = $this->getPseId();
        $items['currency_id'] = $this->getCurrencyId();
        $items['customerFamilyId'] = $this->getCustomerFamilyId();

        return $items;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        $items = $loopResult->getResultDataCollection();

        /** @var \CustomerFamily\Service\CustomerFamilyService $customerFamilyService */
        $customerFamilyService = $this->container->get('customer.family.service');

        $pse = ProductSaleElementsQuery::create()->findOneById($items['pse_id']);

        $prices = $customerFamilyService->calculateCustomerFamilyPsePrice($pse, $items['customerFamilyId'], $items['currency_id']);

        $loopResultRow = new LoopResultRow();
        
        $loopResultRow->set("CALCULATED_PRICE", $prices['price'] ?? null);
        $loopResultRow->set("CALCULATED_TAXED_PRICE", $prices['taxedPrice'] ?? null);
        $loopResultRow->set("CALCULATED_PROMO_PRICE", $prices['promoPrice'] ?? null);
        $loopResultRow->set("CALCULATED_TAXED_PROMO_PRICE", $prices['taxedPromoPrice'] ?? null);

        $loopResult->addRow($loopResultRow);

        return $loopResult;
    }
}
















