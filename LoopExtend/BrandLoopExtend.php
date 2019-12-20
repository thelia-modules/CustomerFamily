<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\Model\Map\CustomerFamilyAvailableBrandTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Map\BrandTableMap;

class BrandLoopExtend extends BaseCustomerFamilyLoopExtend implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'brand') => ['buildModelCriteria', 128],
        ];
    }

    public function buildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        if ($event->getLoop()->getBackendContext()) {
            return null;
        }

        $customerFamily = $this->getCustomerFamily();

        if (null === $customerFamily || !$customerFamily->getBrandRestrictionEnabled()) {
            return;
        }

        $query = $event->getModelCriteria();

        $join = new Join();
        $join->addExplicitCondition(
            BrandTableMap::TABLE_NAME,
            'ID',
            'brand',
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