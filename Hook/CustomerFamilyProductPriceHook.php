<?php

namespace CustomerFamily\Hook;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\ProductPurchasePriceQuery;
use CustomerFamily\Service\CustomerFamilyService;
use Symfony\Contracts\Service\Attribute\Required;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\Currency;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\ProductSaleElementsQuery;

/**
 * Class CustomerFamilyProductPriceHook
 * @package CustomerFamily\Hook
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyProductPriceHook extends BaseHook
{
    #[Required]
    public CustomerFamilyService $customerFamilyService;

    public static function getSubscribedHooks(): array
    {
        return [
            'product.combinations-row' => [
                ['type' => 'back', 'method' => 'onPsePriceEdit'],
            ],
            'product.edit-js' => [
                ['type' => 'back', 'method' => 'onPseJsEdit'],
            ],
            'product.details-pricing-form' => [
                ['type' => 'back', 'method' => 'onDetailsPricingForm'],
            ],
            'product.details-promotion-form' => [
                ['type' => 'back', 'method' => 'onDetailsPromotionForm'],
            ],
        ];
    }

    public function onPsePriceEdit(HookRenderEvent $event): void
    {
        $pseId = (int) $event->getArgument('pse');
        $productId = (int) $event->getArgument('product_id');
        $currencyId = $this->currentCurrencyId();

        $event->add($this->render('product-edit-price.html.twig', [
            'pseId' => $pseId,
            'idx' => $event->getArgument('idx'),
            'product_id' => $productId,
            'purchasePrice' => $this->purchasePrice($pseId, $currencyId),
            'calculatedPrices' => $this->calculatedPrices($pseId, $currencyId),
            'currencySymbol' => $this->currencySymbol($currencyId),
        ]));
    }

    public function onPseJsEdit(HookRenderEvent $event): void
    {
        $event->add($this->render('product-edit-price-js.html.twig'));
    }

    public function onDetailsPricingForm(HookRenderEvent $event): void
    {
        $pseId = (int) $event->getArgument('product_sale_element_id', $event->getArgument('pse'));
        $currencyId = $this->currentCurrencyId();

        $event->add($this->render('product-details-pricing.html.twig', [
            'purchasePrice' => $this->purchasePrice($pseId, $currencyId),
            'calculatedPrices' => $this->calculatedPrices($pseId, $currencyId),
            'currencySymbol' => $this->currencySymbol($currencyId),
        ]));
    }

    public function onDetailsPromotionForm(HookRenderEvent $event): void
    {
        $pseId = (int) $event->getArgument('product_sale_element_id', $event->getArgument('pse'));
        $currencyId = $this->currentCurrencyId();

        $event->add($this->render('product-details-promo.html.twig', [
            'calculatedPrices' => $this->calculatedPrices($pseId, $currencyId),
            'currencySymbol' => $this->currencySymbol($currencyId),
        ]));
    }

    private function currentCurrencyId(): int
    {
        $currencyId = $this->getRequest()->query->get('edit_currency_id');

        return $currencyId ? (int) $currencyId : (int) Currency::getDefaultCurrency()->getId();
    }

    private function currencySymbol(int $currencyId): string
    {
        $currency = \Thelia\Model\CurrencyQuery::create()->findPk($currencyId)
            ?? Currency::getDefaultCurrency();

        return (string) $currency->getSymbol();
    }

    private function purchasePrice(int $pseId, int $currencyId): ?string
    {
        if ($pseId <= 0) {
            return null;
        }

        if (CustomerFamily::getConfigValue('customer_family_price_mode', null)) {
            $price = ProductPriceQuery::create()
                ->filterByCurrencyId($currencyId)
                ->filterByProductSaleElementsId($pseId)
                ->findOne();

            return null !== $price ? (string) $price->getPrice() : null;
        }

        $purchasePrice = ProductPurchasePriceQuery::create()
            ->filterByProductSaleElementsId($pseId)
            ->filterByCurrencyId($currencyId)
            ->findOne();

        return null !== $purchasePrice ? (string) $purchasePrice->getPurchasePrice() : null;
    }

    private function useEquation(int $customerFamilyId, bool $promo): bool
    {
        $price = CustomerFamilyPriceQuery::create()
            ->filterByCustomerFamilyId($customerFamilyId)
            ->filterByPromo($promo ? 1 : 0)
            ->findOne();

        return null !== $price && (bool) $price->getUseEquation();
    }

    /**
     * Reproduces the customer_family_pse_calculated_prices loop for a given PSE.
     *
     * @return array<int, array<string, mixed>>
     */
    private function calculatedPrices(int $pseId, int $currencyId): array
    {
        if ($pseId <= 0) {
            return [];
        }

        $pse = ProductSaleElementsQuery::create()->findOneById($pseId);
        if (null === $pse) {
            return [];
        }

        $locale = $this->getRequest()->getLocale();
        $rows = [];

        foreach (CustomerFamilyQuery::create()->find() as $customerFamily) {
            $customerFamily->setLocale($locale);

            $prices = $this->customerFamilyService->calculateCustomerFamilyPsePrice(
                $pse,
                $customerFamily->getId(),
                $currencyId
            );

            $rows[] = [
                'CUSTOMER_FAMILY_ID' => $customerFamily->getId(),
                'CUSTOMER_FAMILY_TITLE' => $customerFamily->getTitle(),
                'WITH_FORMULA' => $this->useEquation($customerFamily->getId(), false),
                'WITH_FORMULA_PROMO' => $this->useEquation($customerFamily->getId(), true),
                'PRICE' => $prices['price'] ?? null,
                'TAXED_PRICE' => $prices['taxedPrice'] ?? null,
                'PROMO_PRICE' => $prices['promoPrice'] ?? null,
                'TAXED_PROMO_PRICE' => $prices['taxedPromoPrice'] ?? null,
                'CALCULATED_PRICE' => $prices['price'] ?? null,
                'CALCULATED_TAXED_PRICE' => $prices['taxedPrice'] ?? null,
                'CALCULATED_PROMO_PRICE' => $prices['promoPrice'] ?? null,
                'CALCULATED_TAXED_PROMO_PRICE' => $prices['taxedPromoPrice'] ?? null,
            ];
        }

        return $rows;
    }
}
