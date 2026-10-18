<?php

declare(strict_types=1);

namespace CustomerFamily\Hook\Theme;

use CustomerFamily\CustomerFamily;
use Thelia\Core\Hook\Theme\ThemeHookInterface;
use Twig\Environment;

final readonly class CustomerFamilyAccountThemeHook implements ThemeHookInterface
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    public function supports(string $hookName): bool
    {
        return 'account.bottom' === $hookName;
    }

    public function render(string $hookName, array $parameters): string
    {
        $customerId = (int) ($parameters['customer']['id'] ?? 0);

        if ($customerId <= 0) {
            return '';
        }

        return $this->twig->render('@CustomerFamilyModule/theme-hook/account-additional.html.twig', [
            'customerId' => $customerId,
        ]);
    }
}
