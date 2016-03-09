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

namespace CustomerFamily;

use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;
use CustomerFamily\Model\CustomerFamily as CustomerFamilyModel;

/**
 * Class CustomerFamily
 * @package CustomerFamily
 */
class CustomerFamily extends BaseModule
{
    /** @cont string */
    const MODULE_DOMAIN = 'customerfamily';

    /** @cont string */
    const MESSAGE_DOMAIN = 'customerfamily';

    /** @cont string */
    const CUSTOMER_FAMILY_PARTICULAR = "particular";

    /** @cont string */
    const CUSTOMER_FAMILY_PROFESSIONAL = "professional";

    const PRICE_CALC_ACTIVE = 'customer_family_price_activated';

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        try {
            CustomerFamilyQuery::create()->findOne();
        } catch (\Exception $e) {
            $database->insertSql(null, array(__DIR__ . "/Config/thelia.sql"));
        }

        //Generate the 2 defaults customer_family

        //Customer
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Particulier", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Particular", "en_US");

        //Professional
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professionnel", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professional", "en_US");
    }

    /**
     * @param $code
     * @param null $title
     * @param string $locale
     *
     * @return Model\CustomerFamily
     */
    public static function getCustomerFamilyByCode($code, $title = null, $locale = "fr_FR")
    {
        if ($title == null) {
            $title = $code;
        }

        /** @var CustomerFamilyModel $customerFamily */
        if (null == $customerFamily = CustomerFamilyQuery::create()
                ->useCustomerFamilyI18nQuery()
                    ->filterByLocale($locale)
                ->endUse()
                ->filterByCode($code)
                ->findOne()
        ) {
            //Be sure that you don't create it twice
            /** @var CustomerFamilyModel $customerF */
            if (null != $customerF = CustomerFamilyQuery::create()->findOneByCode($code)) {
                $customerF
                    ->setLocale($locale)
                    ->setTitle($title)
                    ->save()
                ;
            } else {
                $customerFamily = new CustomerFamilyModel();
                $customerFamily
                    ->setCode($code)
                    ->setLocale($locale)
                    ->setTitle($title)
                    ->save()
                ;
            }

        }

        return $customerFamily;
    }
}
