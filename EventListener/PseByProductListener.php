<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Event\CustomerFamilyEvents;
use CustomerFamily\Event\CustomerFamilyPriceChangeEvent;
use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Security\SecurityContext;
use TheliaSmarty\Events\PseByProductEvent;

class PseByProductListener implements EventSubscriberInterface
{
    protected $customerFamilyService;

    protected $securityContext;
    protected EventDispatcherInterface $dispatcher;

    public function __construct(
        CustomerFamilyService $customerFamilyService,
        SecurityContext $securityContext,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->customerFamilyService = $customerFamilyService;
        $this->securityContext = $securityContext;
        $this->dispatcher = $dispatcher;
    }

    public function updatePriceInPseByProduct(PseByProductEvent $event)
    {

        $priceChangeEvent = new CustomerFamilyPriceChangeEvent();
        $this->dispatcher->dispatch($priceChangeEvent, CustomerFamilyEvents::CUSTOMER_FAMILY_PRICE_CHANGE);

        if (!$priceChangeEvent->getAllowPriceChange()) {
            return;
        }
        $pse = $event->getProductSaleElements();
        $prices = $this->customerFamilyService->calculateCustomerFamilyPsePrice($pse);

        $discount = 0;
        if ($this->securityContext->hasCustomerUser()) {
            $discount = $this->securityContext->getCustomerUser()->getDiscount();
        }

        if (isset($prices['price'])) {
            $pse->setVirtualColumn('price_PRICE', $prices['price'] * (1 - ($discount / 100)));
        }

        if (isset($prices['promoPrice'])) {
            $pse->setVirtualColumn('price_PROMO_PRICE', $prices['promoPrice'] * (1 - ($discount / 100)));
        }

        if (isset($prices['promo'])) {
            $pse->setPromo($prices['promo']);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            PseByProductEvent::class => ['updatePriceInPseByProduct', 128]
        ];
    }
}
