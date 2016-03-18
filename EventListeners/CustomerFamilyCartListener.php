<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Service\CustomerFamilyService;
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

    public function __construct(CustomerFamilyService $customerFamilyService)
    {
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
        $pseId = $cartEvent->getProductSaleElementsId();
        $pse = ProductSaleElementsQuery::create()->findOneById($pseId);

        $prices = $this->customerFamilyService->calculateCustomerPsePrice($pse);

        if ($prices['price'] !== null) {
            $cartEvent->getCartItem()->setPrice($prices['price']);
        }
        if ($prices['promoPrice'] !== null) {
            $cartEvent->getCartItem()->setPromoPrice($prices['promoPrice']);
        }

        $cartEvent->getCartItem()->save();
    }
}