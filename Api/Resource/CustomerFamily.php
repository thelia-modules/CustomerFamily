<?php

namespace CustomerFamily\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use CustomerFamily\Model\Map\CustomerFamilyTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/customer_families',
        ),
        new GetCollection(
            uriTemplate: '/admin/customer_families'
        ),
        new Get(
            uriTemplate: '/admin/customer_families/{id}',
            normalizationContext: ['groups' => [self::GROUP_ADMIN_READ, self::GROUP_ADMIN_READ_SINGLE]]
        ),
        new Put(
            uriTemplate: '/admin/customer_families/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/customer_families/{id}'
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]]
)]
class CustomerFamily extends AbstractTranslatableResource
{
    public const GROUP_ADMIN_READ = 'admin:customer_family:read';
    public const GROUP_ADMIN_READ_SINGLE = 'admin:customer_family:read:single';
    public const GROUP_ADMIN_WRITE = 'admin:customer_family:write';

    use PropelResourceTrait;

    #[Groups([
        self::GROUP_ADMIN_READ,
    ])]
    public ?int $id = null;

    #[Groups([
        self::GROUP_ADMIN_READ,
    ])]
    #[NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    public string $code;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public I18nCollection $i18ns;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): CustomerFamily
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): CustomerFamily
    {
        $this->code = $code;
        return $this;
    }

    public function getI18ns(): I18nCollection
    {
        return $this->i18ns;
    }

    public function setI18ns(I18nCollection|array $i18ns): CustomerFamily
    {
        $this->i18ns = $i18ns;
        return $this;
    }

    public static function getI18nResourceClass(): string
    {
        return CustomerFamilyI18n::class;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new CustomerFamilyTableMap();
    }
}