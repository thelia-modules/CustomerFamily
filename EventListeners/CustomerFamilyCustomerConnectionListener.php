<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function __construct(RequestStack $requestStack, CustomerFamilyService $customerFamilyService)
    {
        $this->requestStack = $requestStack;
        $this->customerFamilyService = $customerFamilyService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CUSTOMER_LOGOUT => ['customerLogout', 128],
            TheliaEvents::CUSTOMER_LOGIN => ['customerLogin', 128]
        ];
    }

    /**
     * Update cart items' prices when logging out
     *
     * @param DefaultActionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function customerLogout(DefaultActionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        // Get cart & loop on its items
        $cart = $this->requestStack->getCurrentRequest()->getSession()->getSessionCart($dispatcher);

        /** @var \Thelia\Model\CartItem $cartItem */
        foreach ($cart->getCartItems() as $cartItem) {
            // Get item's corresponding PSE
            $pse = ProductSaleElementsQuery::create()->findOneById($cartItem->getProductSaleElementsId());

            // Get pse's prices for the customer
            $prices = $this->customerFamilyService->calculateCustomerPsePrice(
                $pse,
                null,
                $cart->getCurrencyId()
            );

            if ($prices['price'] !== null) {
                $cartItem->setPrice($prices['price']);
            }

            if ($prices['promoPrice'] !== null) {
                $cartItem->setPromoPrice($prices['promoPrice']);
            }

            $cartItem->save();
        }
    }

    /**
     * Update cart items' prices when logging in
     *
     * @param CustomerLoginEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function customerLogin(CustomerLoginEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        // Get cart & loop on its items
        $cart = $this->requestStack->getCurrentRequest()->getSession()->getSessionCart($dispatcher);

        /** @var \Thelia\Model\CartItem $cartItem */
        foreach ($cart->getCartItems() as $cartItem) {
            // Get item's corresponding PSE
            $pse = ProductSaleElementsQuery::create()->findOneById($cartItem->getProductSaleElementsId());

            // Get pse's prices for the customer
            $prices = $this->customerFamilyService->calculateCustomerPsePrice(
                $pse,
                $event->getCustomer()->getId(),
                $cart->getCurrencyId()
            );

            if ($prices['price'] !== null) {
                $cartItem->setPrice($prices['price']);
            }

            if ($prices['promoPrice'] !== null) {
                $cartItem->setPromoPrice($prices['promoPrice']);
            }

            $cartItem->save();
        }
    }
}