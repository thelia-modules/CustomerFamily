<?php

namespace CustomerFamily\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use CustomerFamily\Api\Controller\CustomerFamilyProductPriceUpdateByRef;
use CustomerFamily\Api\State\CustomerFamilyPricePersistProcessor;
use CustomerFamily\Model\Map\CustomerFamilyProductPriceTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/customer_family_product_prices',
            denormalizationContext: ['groups' => [self::GROUP_ADMIN_CREATE, self::GROUP_ADMIN_WRITE]]
        ),
        new Get(
            uriTemplate: '/admin/customer_family_product_prices/{productSaleElementsId}/family/{customerFamilyCode}',
        ),
        new Put(
            uriTemplate: '/admin/customer_family_product_prices/reference/{productSaleElementsRef}/family/{customerFamilyCode}',
            controller: CustomerFamilyProductPriceUpdateByRef::class,
            read: false,
        ),
        new Put(
            uriTemplate: '/admin/customer_family_product_prices/{productSaleElementsId}/family/{customerFamilyCode}',
        ),
        new Delete(
            uriTemplate: '/admin/customer_family_product_prices/{productSaleElementsId}/family/{customerFamilyCode}',
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    processor: CustomerFamilyPricePersistProcessor::class
)]
class CustomerFamilyProductPrice implements PropelResourceInterface
{
    public const GROUP_ADMIN_READ = 'admin:customer_family_product_price:read';
    public const GROUP_ADMIN_READ_SINGLE = 'admin:customer_family_product_price:read:single';
    public const GROUP_ADMIN_WRITE = 'admin:customer_family_product_price:write';
    public const GROUP_ADMIN_CREATE = 'admin:customer_family_product_price:create';

    use PropelResourceTrait;

    #[Groups([self::GROUP_ADMIN_CREATE])]
    #[NotBlank(groups: [self::GROUP_ADMIN_CREATE])]
    public string $customerFamilyCode;

    #[Groups([self::GROUP_ADMIN_CREATE])]
    #[NotBlank(groups: [self::GROUP_ADMIN_CREATE])]
    public int $productSaleElementsId;

    #[Groups([
        self::GROUP_ADMIN_READ,
        self::GROUP_ADMIN_WRITE,
    ])]
    #[NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    public float $price;

    #[Groups([
        self::GROUP_ADMIN_READ,
        self::GROUP_ADMIN_WRITE,
    ])]
    public float $promoPrice;

    #[Groups([
        self::GROUP_ADMIN_READ,
        self::GROUP_ADMIN_WRITE,
    ])]
    public ?bool $promo;

    public function getCustomerFamilyCode(): string
    {
        return $this->customerFamilyCode;
    }

    public function setCustomerFamilyCode(string $customerFamilyCode): CustomerFamilyProductPrice
    {
        $this->customerFamilyCode = $customerFamilyCode;
        return $this;
    }

    public function getProductSaleElementsId(): int
    {
        return $this->productSaleElementsId;
    }

    public function setProductSaleElementsId(int $productSaleElementsId): CustomerFamilyProductPrice
    {
        $this->productSaleElementsId = $productSaleElementsId;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): CustomerFamilyProductPrice
    {
        $this->price = $price;
        return $this;
    }

    public function getPromoPrice(): float
    {
        return $this->promoPrice;
    }

    public function setPromoPrice(float $promoPrice): CustomerFamilyProductPrice
    {
        $this->promoPrice = $promoPrice;
        return $this;
    }

    public function getPromo(): ?bool
    {
        return $this->promo;
    }

    public function setPromo(?bool $promo): CustomerFamilyProductPrice
    {
        $this->promo = $promo;
        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new CustomerFamilyProductPriceTableMap();
    }
}