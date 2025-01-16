<?php

namespace CustomerFamily\Event;

use CustomerFamily\Model\CustomerFamily;
use Thelia\Core\Event\ActionEvent;

class CustomerFamilyPriceChangeEvent extends ActionEvent
{
    public function __construct(
        private readonly ?CustomerFamily $customerFamily = null,
        private bool $allowPriceChange = true
    ) { }

    public function getCustomerFamily(): ?CustomerFamily
    {
        return $this->customerFamily;
    }

    public function getAllowPriceChange(): bool
    {
        return $this->allowPriceChange;
    }

    public function setAllowPriceChange($allowPriceChange): bool
    {
        return $this->allowPriceChange = $allowPriceChange;
    }
}
