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

    /** @var string */
    protected $siret;

    /** @var string */
    protected $vat;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param int $customerFamilyId
     *
     * @return CustomerCustomerFamily
     */
    public function setCustomerFamilyId($customerFamilyId)
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

    /**
     * @param int $customerId
     *
     * @return CustomerCustomerFamily
     */
    public function setCustomerId($customerId)
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

    /**
     * @param mixed $siret
     *
     * @return CustomerCustomerFamily
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * @param mixed $vat
     *
     * @return CustomerCustomerFamily
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }
}
