<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Model\ProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;

/**
 * Class ProductExtendPriceFormListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class ProductExtendPriceFormListener implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [TheliaEvents::FORM_AFTER_BUILD . '.thelia_product_default_sale_element_update_form' => ['extendProductPriceForm', 128]];
    }

    /**
     * Add a purchase price input to the product
     *
     * @param TheliaFormEvent $event
     */
    public function extendProductPriceForm(TheliaFormEvent $event)
    {
        $event
            ->getForm()
            ->getFormBuilder()
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'handleExtendedData'], 0);

        $event->getForm()->getFormBuilder()
            ->add(
                'purchase_price',
                NumberType::class,
                [
                    'constraints' => [
                        new Constraints\GreaterThanOrEqual(['value' => 0])
                    ]
                ]
            );
    }

    /**
     * Create or update product's default PSE's purchase price
     *
     * @param FormEvent $formEvent
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function handleExtendedData(FormEvent $formEvent)
    {
        if (!$formEvent->getForm()->isValid()) {
            return;
        }

        $data = $formEvent->getData();

        if (null !== $purchasePrice = ProductPurchasePriceQuery::create()
                ->filterByProductSaleElementsId($data['product_sale_element_id'])
                ->findOneByCurrencyId($data['currency'])) {
            // Update purchase price
            $purchasePrice
                ->setPurchasePrice($data['purchase_price'])
                ->save();
        } else {
            // Create new purchase price
            (new ProductPurchasePrice())
                ->setProductSaleElementsId($data['product_sale_element_id'])
                ->setCurrencyId($data['currency'])
                ->setPurchasePrice($data['purchase_price'])
                ->save();
        }
    }
}