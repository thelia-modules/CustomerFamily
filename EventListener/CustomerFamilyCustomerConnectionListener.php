<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\DefaultActionEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\ProductSaleElementsQuery;

/**
 * Class CustomerFamilyCustomerConnectionListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyCustomerConnectionListener implements EventSubscriberInterface
{
    protected $requestStack;
    protected $customerFamilyService;
    protected $dispatcher;

    public function __construct(
        RequestStack $requestStack,
        CustomerFamilyService $customerFamilyService,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->requestStack = $requestStack;
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
            TheliaEvents::CUSTOMER_LOGOUT => ['refreshCartItemPrices', -230],
            TheliaEvents::CUSTOMER_LOGIN => ['refreshCartItemPrices', -230]
        ];
    }

    public function refreshCartItemPrices(ActionEvent $event)
    {
        $cart = $this->requestStack->getCurrentRequest()->getSession()->getSessionCart($this->dispatcher);

        foreach ($cart->getCartItems() as $cartItem) {
            $this->customerFamilyService->setCustomerFamilyPriceToCartItem(
                $cartItem,
                null,
                $cart->getCurrencyId()
            );

            $cartItem->save();
        }
    }
}
