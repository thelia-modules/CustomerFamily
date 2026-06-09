<?php

namespace CustomerFamily\Hook;

use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Hooks injecting CustomerFamily widgets into the core customer admin screens.
 */
class CustomerFamilyCustomerHook extends BaseHook
{
    public static function getSubscribedHooks(): array
    {
        return [
            'customer.create-form' => [
                ['type' => 'back', 'method' => 'onCustomerCreateForm'],
            ],
            'customers.js' => [
                ['type' => 'back', 'method' => 'onCustomersJs'],
            ],
            'customer.edit' => [
                ['type' => 'back', 'method' => 'onCustomerEdit'],
            ],
        ];
    }

    public function onCustomerCreateForm(HookRenderEvent $event): void
    {
        $event->add($this->render('customer-create.html.twig', [
            'families' => $this->listFamilies(),
        ]));
    }

    public function onCustomersJs(HookRenderEvent $event): void
    {
        $event->add($this->render('customer-create-js.html.twig'));
    }

    public function onCustomerEdit(HookRenderEvent $event): void
    {
        $customerId = (int) $event->getArgument('customer_id');

        $currentFamilyId = null;
        if ($customerId > 0) {
            $relation = CustomerCustomerFamilyQuery::create()
                ->filterByCustomerId($customerId)
                ->findOne();
            if (null !== $relation) {
                $currentFamilyId = $relation->getCustomerFamilyId();
            }
        }

        $event->add($this->render('customer-edit.html.twig', [
            'customer_id' => $customerId,
            'current_family_id' => $currentFamilyId,
            'families' => $this->listFamilies(),
        ]));
    }

    /**
     * @return array<int, array{id: int, code: string, title: string}>
     */
    private function listFamilies(): array
    {
        $locale = $this->getRequest()->getLocale();

        $families = [];
        foreach (CustomerFamilyQuery::create()->find() as $family) {
            $family->setLocale($locale);
            $families[] = [
                'id' => $family->getId(),
                'code' => $family->getCode(),
                'title' => $family->getTitle(),
            ];
        }

        return $families;
    }
}
