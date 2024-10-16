<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CustomerFamily\Api\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;
use CustomerFamily\Api\Resource\CustomerFamilyProductPrice;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Api\Bridge\Propel\Service\ApiResourcePropelTransformerService;
use Thelia\Model\ProductSaleElementsQuery;
use TntSearch\Index\Customer;

class CustomerFamilyPriceProvider implements ProviderInterface
{

    public function __construct(
        private readonly ApiResourcePropelTransformerService $apiResourcePropelTransformerService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if(!isset($uriVariables['customerFamilyCode']) || !isset($uriVariables['productSaleElementsId'])) {
            return null;
        }
        $customerFamily = CustomerFamilyQuery::create()->findOneByCode($uriVariables['customerFamilyCode']);
        if (null === $customerFamily) {
            return null;
        }
        $customerFamilyPrice = CustomerFamilyProductPriceQuery::create()
            ->filterByCustomerFamilyId($customerFamily->getId())
            ->filterByProductSaleElementsId($uriVariables['productSaleElementsId'])
            ->findOne();
        if (null === $customerFamilyPrice) {
            return null;
        }
        return $this->apiResourcePropelTransformerService->modelToResource(
            CustomerFamilyProductPrice::class,
            $customerFamilyPrice,
            $context
        );
    }
}
