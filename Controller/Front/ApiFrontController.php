<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CustomerFamily\Controller\Front;

use CustomerFamily\Model\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyI18nQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;

/**
 * Backwards-compatibility shim for the legacy `/open_api/customer_families`
 * GET endpoint still consumed by integrations relying on the pre-AP4 contract.
 *
 * The canonical API lives under `/api/admin/customer_families` (AP 4.3).
 */
#[Route('/open_api/customer_families', name: 'customerfamily_legacy_customer_families_front')]
final class ApiFrontController extends BaseFrontController
{
    #[Route('', name: '_get', methods: ['GET'])]
    public function getCustomerFamilies(Request $request): JsonResponse
    {
        $locale = $request->get('locale', $request->getSession()->getLang()->getLocale());

        $customerFamilies = CustomerFamilyQuery::create()->find();

        $payload = array_map(
            static fn (CustomerFamily $customerFamily): array => self::toArray($customerFamily, $locale),
            iterator_to_array($customerFamilies),
        );

        return $this->legacyJson($payload);
    }

    /**
     * Reproduces the pre-AP4 OpenApi JSON shape for a CustomerFamily so legacy
     * front-office clients keep working unchanged.
     *
     * @return array<string, mixed>
     */
    private static function toArray(CustomerFamily $customerFamily, string $locale): array
    {
        $customerFamily->setLocale($locale);

        $i18n = CustomerFamilyI18nQuery::create()
            ->filterById($customerFamily->getId())
            ->filterByLocale($locale)
            ->findOne();

        return [
            'id' => (string) $customerFamily->getId(),
            'code' => $customerFamily->getCode(),
            'isDefault' => (bool) $customerFamily->getIsDefault(),
            'i18n' => [
                'title' => $i18n?->getTitle(),
                'description' => null,
                'chapo' => null,
                'postscriptum' => null,
                'metaTitle' => null,
                'metaDescription' => null,
                'metaKeywords' => null,
            ],
        ];
    }

    private function legacyJson(mixed $data, int $status = 200): JsonResponse
    {
        $response = (new JsonResponse())->setContent(json_encode($data));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setStatusCode($status);

        return $response;
    }
}
