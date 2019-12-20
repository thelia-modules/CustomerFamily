<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\Model\Map\CustomerFamilyAvailableCategoryTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Map\CategoryTableMap;

class CategoryLoopExtend extends BaseCustomerFamilyLoopExtend implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'category') => ['buildModelCriteria', 128],
        ];
    }

    public function buildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        if ($event->getLoop()->getBackendContext()) {
            return null;
        }

        $customerFamily = $this->getCustomerFamily();

        if (null === $customerFamily || !$customerFamily->getCategoryRestrictionEnabled()) {
            return;
        }

        $query = $event->getModelCriteria();

        $join = new Join();
        $join->addExplicitCondition(
            CategoryTableMap::TABLE_NAME,
            'ID',
            'category',
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
}