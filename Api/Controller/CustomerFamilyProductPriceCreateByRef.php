<?php

namespace CustomerFamily\Api\Controller;

use CustomerFamily\Api\Resource\CustomerFamilyProductPrice;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Model\ProductSaleElementsQuery;

#[AsController]
class CustomerFamilyProductPriceCreateByRef
{
    public function __construct(
    )
    {
    }

    public function __invoke(Request $request)
    {
        $jsonData = json_decode($request->getContent(), true);

        $productSaleElements = ProductSaleElementsQuery::create()
            ->findOneByRef($jsonData['productSaleElementsRef']);

        if(!$productSaleElements){
            throw new NotFoundHttpException('NotFound');
        }
        /** @var CustomerFamilyProductPrice $customerFamilyProductPriceResource */
        $customerFamilyProductPriceResource = $request->get('data');

        $customerFamilyProductPriceResource->setCustomerFamilyCode($jsonData['customerFamilyCode'])
            ->setProductSaleElementsId($productSaleElements->getId())
            ->setPrice($jsonData['price'])
            ->setPromoPrice($jsonData['promoPrice'])
            ->setPromo($jsonData['promo']);

        return $customerFamilyProductPriceResource;

    }
}