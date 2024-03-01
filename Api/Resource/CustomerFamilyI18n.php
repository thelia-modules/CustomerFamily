<?php

namespace CustomerFamily\Api\Resource;

use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\I18n;

class CustomerFamilyI18n extends I18n
{
    #[Groups([
        CustomerFamily::GROUP_ADMIN_READ,
        CustomerFamily::GROUP_ADMIN_WRITE
    ])]
    protected ?string $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}