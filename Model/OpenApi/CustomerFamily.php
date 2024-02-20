<?php

namespace CustomerFamily\Model\OpenApi;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use OpenApi\Model\Api\BaseApiModel;
use OpenApi\Model\Api\ModelTrait\translatable;

#[Schema(
    description: "Customer families",
)]
class CustomerFamily extends BaseApiModel
{
    use translatable;

    #[Property(type: "integer")]
    protected string $id;

    #[Property(type: "string")]
    protected string $code;

    #[Property(type: "boolean")]
    protected bool $isDefault;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): CustomerFamily
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

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(?bool $isDefault): CustomerFamily
    {
        $this->isDefault = $isDefault ?? false;
        return $this;
    }
}