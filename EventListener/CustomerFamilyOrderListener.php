<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\Model\CustomerFamilyOrder;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\ProductPurchasePriceTableMap;
use CustomerFamily\Model\OrderProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Event\OrderEvent as PropelOrderEvents;

/**
 * Class CustomerFamilyOrderListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyOrderListener implements EventSubscriberInterface
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
            TheliaEvents::ORDER_BEFORE_PAYMENT => ['createOrderPurchasePrices', 128],
            PropelOrderEvents::POST_INSERT => ['saveOrderFamilyAndEquation', 128]
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
        $customerFamilyId = $this->customerFamilyService->getCustomerCustomerFamilyId($orderEvent->getOrder()->getCustomerId());

        /** @var \Thelia\Model\OrderProduct $orderProduct */
        foreach ($orderProducts as $orderProduct) {
            // If a ProductPurchasePrice exists for the PSE & currency
            if (null !== $purchasePrice = $this->customerFamilyService
                    ->getPurchasePrice(
                        $orderProduct->getProductSaleElementsId(),
                        $currencyId)){
                // Initialize equation
                $equation = 'Equation not used';

                // If equation was used, get information about it
                if (null !== $customerFamilyPrice = $this->customerFamilyService->getCustomerFamilyPrice(
                    $customerFamilyId, $orderProduct->getWasInPromo(), 1)) {

                    $equation = '( ' . $purchasePrice . ' + ' . $customerFamilyPrice->getAmountAddedBefore() .
                        ' ) x ' . $customerFamilyPrice->getMultiplicationCoefficient() . ' + ' .
                        $customerFamilyPrice->getAmountAddedAfter();
                }

                // New OrderProductPurchasePrice
                (new OrderProductPurchasePrice())
                    ->setOrderProductId($orderProduct->getId())
                    ->setPurchasePrice($purchasePrice)
                    ->setSaleDayEquation($equation)
                    ->save();
            }
        }
    }

    /**
     * @param PropelOrderEvents $orderEvent
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveOrderFamilyAndEquation(PropelOrderEvents $orderEvent)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->findOneById(
                $this->customerFamilyService->getCustomerCustomerFamilyId($orderEvent->getModel()->getCustomerId())
            );

        (new CustomerFamilyOrder())
            ->setOrderId($orderEvent->getModel()->getId())
            ->setCustomerFamilyId($customerFamily->getId())
            ->save();
    }
}