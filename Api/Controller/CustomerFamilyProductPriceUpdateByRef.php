<?php

namespace CustomerFamily\Api\Controller;

use CustomerFamily\Api\Resource\CustomerFamilyProductPrice;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Api\Bridge\Propel\Service\ApiResourcePropelTransformerService;

#[AsController]
class CustomerFamilyProductPriceUpdateByRef
{
    public function __construct(
        private ApiResourcePropelTransformerService $apiResourcePropelTransformerService
    )
    {
    }

    public function __invoke(string $productSaleElementsRef, string $customerFamilyCode, Request $request)
    {
        $customerFamilyProductPrice = CustomerFamilyProductPriceQuery::create()
            ->useProductSaleElementsQuery()
            ->filterByRef($productSaleElementsRef)
            ->endUse()
            ->useCustomerFamilyQuery()
            ->filterByCode($customerFamilyCode)
            ->endUse()
            ->findOne()
        ;

        if(!$customerFamilyProductPrice){
            throw new NotFoundHttpException('NotFound');
        }
        /** @var  CustomerFamilyProductPrice $customerFamilyProductPriceResource */
        $customerFamilyProductPriceResource = $this->apiResourcePropelTransformerService->modelToResource(CustomerFamilyProductPrice::class,$customerFamilyProductPrice,[]);

        /** @var  CustomerFamilyProductPrice $data */
        $data = $request->get('data');

        return $customerFamilyProductPriceResource
            ->setPromo(isset($data->promo) ? $data->getPromo() : $customerFamilyProductPriceResource->getPromo() )
            ->setPrice( isset($data->price) ? $data->getPrice() : $customerFamilyProductPriceResource->getPrice())
            ->setPromoPrice( isset($data->promoPrice) ? $data->getPromoPrice() : $customerFamilyProductPriceResource->getPromoPrice() );
    }
}