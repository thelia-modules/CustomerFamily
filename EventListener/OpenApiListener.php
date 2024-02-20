<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use OpenApi\Events\ModelExtendDataEvent;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\Event\CustomerEvent;

class OpenApiListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly ModelFactory $modelFactory,
        private readonly RequestStack $requestStack
    )
    {
    }

    #[Schema(
        schema: "CustomerFamilyExtendCustomer",
        properties: [
            new Property(
                property: "customerFamily",
                ref: "#/components/schemas/CustomerFamily",
                type: "object"
            )
        ]
    )]
    public function addDataOnCustomer(ModelExtendDataEvent $event)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->useCustomerCustomerFamilyQuery()
                ->filterByCustomerId($event->getModel()->getId())
            ->endUse()
            ->findOne();

        if (null === $customerFamily) {
            return;
        }

        $customerFamilyApiModel = $this->modelFactory->buildModel('CustomerFamily', $customerFamily);

        if (!empty($customerFamilyApiModel)) {
            $event->setExtendDataKeyValue('customerFamily', $customerFamilyApiModel);
        }
    }

    public function saveCustomerFamily(CustomerEvent $customerEvent)
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent(), true);

        if (!isset($data['customer']['customerFamily'])) {
            return;
        }

        $customerFamilyQuery = CustomerFamilyQuery::create();

        if (isset($data['customer']['customerFamily']['id'])) {
            $customerFamilyQuery->filterById($data['customer']['customerFamily']['id']);
        } elseif (isset($data['customer']['customerFamily']['code'])) {
            $customerFamilyQuery->filterByCode($data['customer']['customerFamily']['code']);
        } else {
            return;
        }

        $customerFamily = $customerFamilyQuery->findOne();

        if (null === $customerFamily) {
            return;
        }

        $customerCustomerFamily = CustomerCustomerFamilyQuery::create()
            ->filterByCustomerId($customerEvent->getModel()->getId())
            ->findOneOrCreate();

        $customerCustomerFamily->setCustomerFamilyId($customerFamily->getId())
            ->save();
    }

    public static function getSubscribedEvents()
    {
        $events = [];
        if (class_exists('OpenApi\Events\ModelExtendDataEvent')){
            $events[CustomerEvent::POST_SAVE] = ['saveCustomerFamily',0];
            $events[ModelExtendDataEvent::ADD_EXTEND_DATA_PREFIX.'customer'] = ['addDataOnCustomer',0];
        }

        return $events;
    }

}