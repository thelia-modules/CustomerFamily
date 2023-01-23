<?php

namespace CustomerFamily\Model;

use CustomerFamily\Model\Base\CustomerCustomerFamily as BaseCustomerCustomerFamily;
use Propel\Runtime\Connection\ConnectionInterface;

class CustomerCustomerFamily extends BaseCustomerCustomerFamily
{
    /**
     * @deprecated Use SiretManagement module: https://github.com/thelia-modules/SiretManagement.
     */
    public function getSiret(ConnectionInterface $con = null)
    {
        return parent::getSiret($con);
    }

    /**
     * @deprecated Use SiretManagement module: https://github.com/thelia-modules/SiretManagement.
     */
    public function setSiret($v)
    {
        return parent::setSiret($v);
    }

    /**
     * @deprecated Use SiretManagement module: https://github.com/thelia-modules/SiretManagement.
     */
    public function getVat(ConnectionInterface $con = null)
    {
        return parent::getVat($con);
    }

    /**
     * @deprecated Use SiretManagement module: https://github.com/thelia-modules/SiretManagement.
     */
    public function setVat($v)
    {
        return parent::setVat($v);
    }
}
