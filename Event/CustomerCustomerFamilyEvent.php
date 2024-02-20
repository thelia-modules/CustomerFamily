<?php
/*************************************************************************************/
/*      This file is part of the module CustomerFamily                               */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerFamily\Event;

use CustomerFamily\Model\CustomerCustomerFamily;
use Thelia\Core\Event\ActionEvent;

/**
 * Class CustomerCustomerFamilyEvent
 * @package CustomerFamily\Event
 */
class CustomerCustomerFamilyEvent extends ActionEvent
{
    /** @var int */
    protected $customerId;

    /** @var int */
    protected $customerFamilyId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }


    public function setCustomerFamilyId($customerFamilyId): CustomerCustomerFamilyEvent
    {
        $this->customerFamilyId = $customerFamilyId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerFamilyId()
    {
        return $this->customerFamilyId;
    }


    public function setCustomerId($customerId): CustomerCustomerFamilyEvent
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
