<?php

namespace CustomerFamily\EventListener;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Model\ProductPurchasePrice;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints;
use Thelia\Core\Event\Product\ProductCreateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;

/**
 * Class ProductCreationFormListener
 * @package CustomerFamily\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class ProductCreationFormListener implements EventSubscriberInterface
{
    /** @var RequestStack */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::FORM_AFTER_BUILD.'.thelia_product_creation'  => ['addPurchasePriceOnProductCreation', 128],
            TheliaEvents::PRODUCT_CREATE => ['createProductPurchasePrice', 96]
        ];
    }

    /**
     * Add purchase price input to product creation form
     *
     * @param TheliaFormEvent $event
     */
    public function addPurchasePriceOnProductCreation(TheliaFormEvent $event)
    {
        $event->getForm()->getFormBuilder()
            ->add(
                'purchase_price',
                NumberType::class,
                [
                    'constraints' => [
                        new Constraints\GreaterThanOrEqual(['value' => 0])
                    ],
                    'label' => self::trans('Purchase price'),
                    'label_attr' => ['for' => 'purchase_price']
                ]
            )
        ;
    }

    /**
     * Create purchase price when product is created
     *
     * @param ProductCreateEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createProductPurchasePrice(ProductCreateEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!isset($request->get('thelia_product_creation')['purchase_price'])) {
            return;
        }

        if (null != $purchasePrice = $this->requestStack->getCurrentRequest()->get('thelia_product_creation')['purchase_price']) {
            (new ProductPurchasePrice())
                ->setProductSaleElementsId($event->getProduct()->getDefaultSaleElements()->getId())
                ->setCurrencyId($event->getCurrencyId())
                ->setPurchasePrice($purchasePrice)
                ->save()
            ;
        }
    }

    /**
     * Utility for translations
     * @param $id
     * @param array $parameters
     * @return string
     */
    protected static function trans($id, array $parameters = array())
    {
        return Translator::getInstance()->trans($id, $parameters, CustomerFamily::MESSAGE_DOMAIN);
    }
}