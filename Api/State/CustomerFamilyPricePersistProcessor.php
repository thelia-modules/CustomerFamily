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
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use CustomerFamily\Model\CustomerFamilyProductPrice as CustomerFamilyProductPriceModel;
use CustomerFamily\Model\CustomerFamilyProductPriceQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Model\ProductSaleElementsQuery;

readonly class CustomerFamilyPricePersistProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
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
            throw new UnprocessableEntityHttpException('The customer family with code '.$data->getCustomerFamilyCode().' does not exist');
        }

        $productSaleElements = ProductSaleElementsQuery::create()
            ->filterById($data->getProductSaleElementsId())
            ->findOne();

        if (null === $productSaleElements) {
            throw new UnprocessableEntityHttpException('The product sale elements with id '.$data->getProductSaleElementsId().' does not exist');
        }

        $alreadyExists = CustomerFamilyProductPriceQuery::create()
            ->filterByProductSaleElementsId($data->getProductSaleElementsId())
            ->filterByCustomerFamilyId($customerFamily->getId())
            ->findOne();

        if (null !== $alreadyExists) {
            throw new ConflictHttpException('A customer family price already exists for this product sale element and this family, update it instead of creating a new one');
        }

        $customerFamilyProductPrice = (new CustomerFamilyProductPriceModel())
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
        $customerFamilyProductPrice = $this->resolveExistingPrice($data, $uriVariables)
            ?? throw new NotFoundHttpException('No customer family price to update for this product sale element and this family');

        $customerFamilyProductPrice
            ->setPrice($data->getPrice())
            ->setPromoPrice($data->getPromoPrice())
            ->setPromo($data->getPromo() ? 1 : 0);

        $customerFamilyProductPrice->save();

        return $data;
    }

    public function delete(CustomerFamilyProductPrice $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $customerFamilyProductPrice = $this->resolveExistingPrice($data, $uriVariables)
            ?? throw new NotFoundHttpException('No customer family price to delete for this product sale element and this family');

        $customerFamilyProductPrice->delete();

        return $data;
    }

    /**
     * The row the request addresses, read from the URI rather than from the
     * submitted resource: a standard PUT denormalizes the body into a fresh
     * resource, which carries no Propel model even though the provider did
     * read the existing row.
     */
    private function resolveExistingPrice(
        CustomerFamilyProductPrice $data,
        array $uriVariables,
    ): ?CustomerFamilyProductPriceModel {
        $model = $data->getPropelModel();

        if ($model instanceof CustomerFamilyProductPriceModel) {
            return $model;
        }

        $familyCode = $uriVariables['customerFamilyCode'] ?? null;
        $productSaleElementsId = $uriVariables['productSaleElementsId'] ?? null;

        if (null === $familyCode || null === $productSaleElementsId) {
            return null;
        }

        $customerFamily = CustomerFamilyQuery::create()->findOneByCode($familyCode);

        if (null === $customerFamily) {
            return null;
        }

        return CustomerFamilyProductPriceQuery::create()
            ->filterByCustomerFamilyId($customerFamily->getId())
            ->filterByProductSaleElementsId($productSaleElementsId)
            ->findOne();
    }
}
