<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Event\CustomerFamilyEvents;
use CustomerFamily\Event\CustomerFamilyPriceChangeEvent;
use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ProductSaleElementsQuery;

/**
 * Class CustomerFamilyCartListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyCartListener implements EventSubscriberInterface
{
    protected $customerFamilyService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(CustomerFamilyService $customerFamilyService, EventDispatcherInterface $dispatcher)
    {
        $this->customerFamilyService = $customerFamilyService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CART_ADDITEM => ['addCartItem', 128]
        ];
    }

    /**
     * @param CartEvent $cartEvent
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addCartItem(CartEvent $cartEvent)
    {
        $event = new CustomerFamilyPriceChangeEvent();
        $this->dispatcher->dispatch($event, CustomerFamilyEvents::CUSTOMER_FAMILY_PRICE_CHANGE);

        if (!$event->getAllowPriceChange()) {
            return;
        }

        $cartItem = $cartEvent->getCartItem();
        $this->customerFamilyService->setCustomerFamilyPriceToCartItem($cartItem);

        $cartItem->save();
    }
}
