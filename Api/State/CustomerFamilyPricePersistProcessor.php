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
use CustomerFamily\Api\Resource\CustomerFamilyProductPrice;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Model\ProductSaleElementsQuery;

readonly class CustomerFamilyPricePersistProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof Post) {
            return $this->create($data, $operation, $uriVariables, $context);
        }

        if ($operation instanceof Put) {
            return $this->update($data, $operation, $uriVariables, $context);
        }

        if ($operation instanceof Delete) {
            return $this->delete($data, $operation, $uriVariables, $context);
        }

        return $data;
    }

    private function create(CustomerFamilyProductPrice $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->filterByCode($data->getCustomerFamilyCode())
            ->findOne();

        if (null === $customerFamily) {
            throw new \Exception('The customer family with code '.$data->getCustomerFamilyCode().' does not exist');
        }

        $productSaleElements = ProductSaleElementsQuery::create()
            ->filterById($data->getProductSaleElementsId())
            ->findOne();

        if (null === $productSaleElements) {
            throw new \Exception('The product sale elements with id '.$data->getProductSaleElementsId().' does not exist');
        }

        $alreadyExists = CustomerFamilyProductPriceQuery::create()
            ->filterByProductSaleElementsId($data->getProductSaleElementsId())
            ->filterByCustomerFamilyId($customerFamily->getId())
            ->findOne();

        if (null !== $alreadyExists) {
            throw new \Exception('A customer family price already exist for this product_sale_elements and this family, please update it instead of creating a new one');
        }

        $customerFamilyProductPrice = (new \CustomerFamily\Model\CustomerFamilyProductPrice())
            ->setProductSaleElementsId($data->getProductSaleElementsId())
            ->setCustomerFamilyId($customerFamily->getId())
            ->setPrice($data->getPrice())
            ->setPromoPrice($data->getPromoPrice())
            ->setPromo($data->getPromo() ? 1 : 0);

        $customerFamilyProductPrice->save();

        return $data;
    }

    public function update(CustomerFamilyProductPrice $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $customerFamilyProductPrice = $data->getPropelModel();

        $customerFamilyProductPrice
            ->setPrice($data->getPrice())
            ->setPromoPrice($data->getPromoPrice())
            ->setPromo($data->getPromo() ? 1 : 0);

        $customerFamilyProductPrice->save();

        return $data;
    }

    public function delete(CustomerFamilyProductPrice $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $customerFamilyProductPrice = $data->getPropelModel();

        $customerFamilyProductPrice->delete();

        return $data;
    }
}
