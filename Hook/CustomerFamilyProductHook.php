<?php

namespace CustomerFamily\Hook;

use CustomerFamily\EventListener\ProductCreationFormListener;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\Currency;

/**
 * Injects the purchase-price field into the product create form (core product screen).
 */
class CustomerFamilyProductHook extends BaseHook
{
    public static function getSubscribedHooks(): array
    {
        return [
            'product.create-form' => [
                ['type' => 'back', 'method' => 'onProductCreateForm'],
            ],
        ];
    }

    public function onProductCreateForm(HookRenderEvent $event): void
    {
        $currency = Currency::getDefaultCurrency();

        $event->add($this->render('product-create-form.html.twig', [
            'currencySymbol' => $currency->getSymbol(),
            'currencyName' => $currency->getName(),
            'form_name' => ProductCreationFormListener::THELIA_PRODUCT_CREATION_FORM_NAME,
            'field_name' => ProductCreationFormListener::PURCHASE_PRICE_FIELD_NAME,
        ]));
    }
}
