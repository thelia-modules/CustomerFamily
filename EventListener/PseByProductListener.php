<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Security\SecurityContext;
use TheliaSmarty\Events\PseByProductEvent;

class PseByProductListener implements EventSubscriberInterface
{
    protected $customerFamilyService;

    protected $securityContext;

    public function __construct(
        CustomerFamilyService $customerFamilyService,
        SecurityContext $securityContext
    )
    {
        $this->customerFamilyService = $customerFamilyService;
        $this->securityContext = $securityContext;
    }

    public function updatePriceInPseByProduct(PseByProductEvent $event)
    {
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