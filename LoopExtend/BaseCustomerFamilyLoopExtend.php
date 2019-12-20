<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Core\Security\SecurityContext;

class BaseCustomerFamilyLoopExtend
{
    /** @var SecurityContext */
    protected $securityContext;

    public function __construct($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getCustomerFamily()
    {
        $currentCustomer = $this->securityContext->getCustomerUser();

        $customerFamily = CustomerFamilyQuery::create()
            ->useCustomerCustomerFamilyQuery()
                ->filterByCustomerId($currentCustomer->getId())
            ->endUse()
            ->findOne();

        return $customerFamily;
    }
}