<?php

namespace CustomerFamily\EventListeners;

use CustomerFamily\Model\ProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;

/**
 * Class PseExtendPriceFormListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class PseExtendPriceFormListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [TheliaEvents::FORM_AFTER_BUILD . '.thelia_product_sale_element_update_form' => ['extendPsePriceForm', 128]];
    }

    /**
     * Add a purchase price input to PSEs
     *
     * @param TheliaFormEvent $event
     */
    public function extendPsePriceForm(TheliaFormEvent $event)
    {
        $event
            ->getForm()
            ->getFormBuilder()
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'handleExtendedData'], 0);

        $event->getForm()->getFormBuilder()
            ->add(
                'purchase_price',
                'collection',
                [
                    'type'  => 'number',
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'options' => [
                        'constraints' => [
                            new Constraints\NotBlank,
                            new Constraints\GreaterThanOrEqual(['value' => 0])
                        ]
                    ],
                    'label_attr' => ['for' => 'purchase_price']
                ]
            );
    }

    /**
     * Create or update PSE's purchase price
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

        if (is_array($data['product_sale_element_id'])) {
            foreach (array_keys($data['product_sale_element_id']) as $idx) {
                if (null !== $purchasePrice = ProductPurchasePriceQuery::create()
                    ->filterByProductSaleElementsId($data['product_sale_element_id'][$idx])
                    ->findOneByCurrencyId($data['currency'])) {
                    // Update purchase price
                    $purchasePrice
                        ->setPurchasePrice($data['purchase_price'][$idx])
                        ->save();
                } else {
                    // Create new purchase price
                    (new ProductPurchasePrice())
                        ->setProductSaleElementsId($data['product_sale_element_id'][$idx])
                        ->setCurrencyId($data['currency'])
                        ->setPurchasePrice($data['purchase_price'][$idx])
                        ->save();
                }
            }
        }
    }
}