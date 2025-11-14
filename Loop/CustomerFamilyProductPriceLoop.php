<?php

namespace CustomerFamily\Loop;

use CustomerFamily\Model\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Currency;
use Thelia\Model\ProductSaleElementsQuery;

class CustomerFamilyProductPriceLoop extends BaseLoop implements PropelSearchLoopInterface
{
    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('pse_id', null, true),
            Argument::createIntTypeArgument('currency_id', Currency::getDefaultCurrency()->getId()),
            Argument::createIntTypeArgument('locale', null),
            Argument::createIntTypeArgument('customer_family_id')
        );
    }

    public function buildModelCriteria(): ModelCriteria
    {
        /** @var Session $session */
        $session = $this->getCurrentRequest()->getSession();

        $pseId = $this->getPseId();

        if (!$locale = $this->getLocale()) {
            $locale = $session->getAdminEditionLang()->getLocale();
        }

        $query = CustomerFamilyQuery::create('cf');

        if ($customerFamilyId = $this->getCustomerFamilyId()) {
            $query->filterById($customerFamilyId);
        }

        $query
            ->leftJoinCustomerFamilyPrice('cfpp')
            ->addJoinCondition('cfpp', 'cfpp.promo = 0')

            ->leftJoinCustomerFamilyPrice('cfpp_promo')
            ->addJoinCondition('cfpp_promo', 'cfpp_promo.promo = 1')

            ->leftJoinCustomerFamilyI18n('cfi')
            ->addJoinCondition('cfi', 'cfi.locale = ?', $locale)

            ->leftJoinCustomerFamilyProductPrice('pp')
            ->addJoinCondition('pp', 'pp.product_sale_elements_id = ?', $pseId, \PDO::PARAM_INT)

            ->leftJoinWith('pp.ProductSaleElements pse')
            ->addJoinCondition('pse', 'pse.id = ?', $pseId, \PDO::PARAM_INT)

            ->withColumn('cfi.title', 'Title')
            ->withColumn('cfpp.use_equation', 'UseEquation')
            ->withColumn('cfpp_promo.use_equation', 'UsePromoEquation')
            ->withColumn('pse.id', 'PseId');

        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult): LoopResult
    {

        /** @var \CustomerFamily\Service\CustomerFamilyService $customerFamilyService */
        $customerFamilyService = $this->container->get('customer.family.service');
        $pse = ProductSaleElementsQuery::create()->findOneById($this->getPseId());

        /** @var CustomerFamily $customerFamily */
        foreach ($loopResult->getResultDataCollection() as $customerFamily) {

            $loopResultRow = new LoopResultRow($customerFamily);

            $loopResultRow->set("CUSTOMER_FAMILY_TITLE", $customerFamily->getVirtualColumn('Title'));
            $loopResultRow->set("CUSTOMER_FAMILY_ID", $customerFamily->getId());

            $loopResultRow->set("WITH_FORMULA", false);
            $loopResultRow->set("WITH_FORMULA_PROMO", false);

            if ($customerFamily->getVirtualColumn('UseEquation')) {
                $loopResultRow->set("WITH_FORMULA", true);
            }

            if ($customerFamily->getVirtualColumn('UsePromoEquation')) {
                $loopResultRow->set("WITH_FORMULA_PROMO", true);
            }

            $loopResultRow->set("PRICE", null);
            $loopResultRow->set("TAXED_PRICE", null);
            $loopResultRow->set("PROMO_PRICE", null);
            $loopResultRow->set("TAXED_PROMO_PRICE", null);

            if ($pse->getId() !== $customerFamily->getVirtualColumn('PseId')) {
                $loopResult->addRow($loopResultRow);
                continue;
            }

            $prices = $customerFamilyService->calculateCustomerFamilyPsePrice(
                $pse,
                $customerFamily->getId(),
                $this->getCurrencyId()
            );

            $loopResultRow->set("PRICE", $prices['price'] ?? null);
            $loopResultRow->set("TAXED_PRICE", $prices['taxedPrice'] ?? null);
            $loopResultRow->set("PROMO_PRICE", $prices['promoPrice'] ?? null);
            $loopResultRow->set("TAXED_PROMO_PRICE", $prices['taxedPromoPrice'] ?? null);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
















