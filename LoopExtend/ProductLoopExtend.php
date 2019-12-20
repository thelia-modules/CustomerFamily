<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\Model\CustomerFamily;
use CustomerFamily\Model\Map\CustomerFamilyAvailableBrandTableMap;
use CustomerFamily\Model\Map\CustomerFamilyAvailableCategoryTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Map\ProductCategoryTableMap;
use Thelia\Model\Map\ProductTableMap;

class ProductLoopExtend extends BaseCustomerFamilyLoopExtend implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product') => ['buildModelCriteria', 128],
        ];
    }

    public function buildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        if ($event->getLoop()->getBackendContext()) {
            return null;
        }

        $customerFamily = $this->getCustomerFamily();
        if (null === $customerFamily) {
            return;
        }

        $query = $event->getModelCriteria();
        $this->addCategoryFilter($query, $customerFamily);
        $this->addBrandFilter($query, $customerFamily);
    }

    protected function addCategoryFilter(ModelCriteria $query, CustomerFamily $customerFamily)
    {
        if (!$customerFamily->getCategoryRestrictionEnabled()) {
            return;
        }

        $join = new Join();
        $join->addExplicitCondition(
            ProductTableMap::TABLE_NAME,
            'ID',
            'product',
            ProductCategoryTableMap::TABLE_NAME,
            'product_id',
            'cf_product_category'
        );
        $join->setJoinType(Criteria::INNER_JOIN);
        $query->addJoinObject($join, 'customer_family_product_category_join');

        $join = new Join();
        $join->addExplicitCondition(
            ProductCategoryTableMap::TABLE_NAME,
            'category_id',
            'cf_product_category',
            CustomerFamilyAvailableCategoryTableMap::TABLE_NAME,
            'category_id',
            'customer_family_available_category'
        );
        $join->setJoinType(Criteria::INNER_JOIN);
        $query->addJoinObject($join, 'customer_family_available_category_join');

        $query->addJoinCondition(
            'customer_family_available_category_join',
            'customer_family_available_category_join.customer_family_id = ? ',
            $customerFamily->getId()
        );
    }

    protected function addBrandFilter(ModelCriteria $query, CustomerFamily $customerFamily)
    {
        if (!$customerFamily->getBrandRestrictionEnabled()) {
            return;
        }

        $join = new Join();
        $join->addExplicitCondition(
            ProductTableMap::TABLE_NAME,
            'brand_id',
            'product',
            CustomerFamilyAvailableBrandTableMap::TABLE_NAME,
            'brand_id',
            'customer_family_available_brand'
        );
        $join->setJoinType(Criteria::INNER_JOIN);
        $query->addJoinObject($join, 'customer_family_available_brand_join');

        $query->addJoinCondition(
            'customer_family_available_brand_join',
            'customer_family_available_brand_join.customer_family_id = ? ',
            $customerFamily->getId()
        );
    }
}