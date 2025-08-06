<?php

namespace CustomerFamily\Hook;

use CustomerFamily\Model\CustomerFamilyProductPrice;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ProductSaleElementsQuery;

class ProductModuleHook extends BaseHook
{
    public function onProductTabContent(HookRenderEvent $event): void
    {
        $pseRefs = [];
        $prices = array_reduce(iterator_to_array(CustomerFamilyProductPriceQuery::create()
            ->useProductSaleElementsQuery()
                ->filterByProductId($event->getArgument('id'))
            ->endUse()
            ->find()),
            function ($carry, CustomerFamilyProductPrice $item) use (&$pseRefs) {
                $pseRefs[] = $item->getProductSaleElements()->getRef();
                $carry[$item->getCustomerFamily()->getCode()][$item->getProductSaleElements()->getRef()] = [
                    'promo' => $item->getPromo(),
                    'price' => $item->getPrice(),
                    'promoPrice' => $item->getPromoPrice()
                ];

                return $carry;
            },
            []
        );

        $content = $this->render(
            'customerFamily/product-tab-content.html',
            [
                'prices' => $prices,
                'pseRefs' => array_unique($pseRefs)
            ]
        );

        $event->add($content);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            "product.tab-content" => [
                [
                    "type" => "back",
                    "method" => "onProductTabContent",
                ]
            ]
        ];
    }
}