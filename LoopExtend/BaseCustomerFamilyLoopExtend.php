<?php

namespace CustomerFamily\LoopExtend;

use CustomerFamily\Model\CustomerFamilyQuery;
use Thelia\Core\Security\SecurityContext;

class BaseCustomerFamilyLoopExtend
{
    /** @var SecurityContext */
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getCustomerFamily()
    {
        $currentCustomer = $this->securityContext->getCustomerUser();

        if (null === $currentCustomer) {
            return null;
        }

        $customerFamily = CustomerFamilyQuery::create()
            ->useCustomerCustomerFamilyQuery()
                ->filterByCustomerId($currentCustomer->getId())
            ->endUse()
            ->findOne();

        return $customerFamily;
    }
}