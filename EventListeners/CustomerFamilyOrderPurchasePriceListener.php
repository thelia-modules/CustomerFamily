<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Model\Map\ProductPurchasePriceTableMap;
use CustomerFamily\Model\OrderProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;

/**
 * Class CustomerFamilyOrderPurchasePriceListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyOrderPurchasePriceListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_BEFORE_PAYMENT => ['createOrderPurchasePrices', 128]
        ];
    }

    /**
     * Save purchase price for each product of the order
     *
     * @param OrderEvent $orderEvent
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createOrderPurchasePrices(OrderEvent $orderEvent)
    {
        $orderProducts = $orderEvent->getOrder()->getOrderProducts();
        $currencyId = $orderEvent->getOrder()->getCurrencyId();

        /** @var \Thelia\Model\OrderProduct $orderProduct */
        foreach ($orderProducts as $orderProduct) {
            // If a ProductPurchasePrice exists for the PSE & currency
            if (null !== $purchasePrice = ProductPurchasePriceQuery::create()
                ->filterByProductSaleElementsId($orderProduct->getProductSaleElementsId())
                ->filterByCurrencyId($currencyId)
                ->select(ProductPurchasePriceTableMap::PURCHASE_PRICE)
                ->findOne()) {
                // New OrderProductPurchasePrice
                (new OrderProductPurchasePrice())
                    ->setOrderProductId($orderProduct->getId())
                    ->setPurchasePrice($purchasePrice)
                    ->save();
            }
        }
    }
}