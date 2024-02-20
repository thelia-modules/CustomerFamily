<?php

namespace CustomerFamily\Controller\Front;

use CustomerFamily\Model\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyQuery;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use OpenApi\Controller\Front\BaseFrontOpenApiController;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Core\HttpFoundation\Request;

class ApiFrontController extends BaseFrontOpenApiController
{
    #[Route("/open_api/customer_families", name: "customer_families")]
    #[Get(
        path: "/customer_families",
        summary: "get customer families",
        tags: ["CustomerFamily"],
        responses: [
            new Response(
                response: "200",
                description: "Success",
                content: [
                    new JsonContent(
                        type: "array",
                        items: new Items(
                            ref: "#/components/schemas/CustomerFamily"
                        )
                    )
                ]
            )
        ]
    )]
    public function getCustomerFamilies(
        Request $request,
        ModelFactory $modelFactory
    ) {
        $locale = $request->get('locale', $request->getSession()->getLang()->getLocale());

        $customerFamilies = CustomerFamilyQuery::create()
            ->find();

        return OpenApiService::jsonResponse(
            array_map(
                function (CustomerFamily $customerFamily) use ($modelFactory, $locale) {
                    $customerFamily->setLocale($locale);
                    return $modelFactory->buildModel('CustomerFamily', $customerFamily);
                },
                iterator_to_array($customerFamilies)
            )
        );
    }
}