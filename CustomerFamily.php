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
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;
use CustomerFamily\Model\CustomerFamily as CustomerFamilyModel;

/**
 * Class CustomerFamily
 * @package CustomerFamily
 * @contributor Etienne Perriere <eperriere@openstudio.fr>
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

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null): void
    {

        try {
            CustomerFamilyQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql"]);
        }

        //Generate the 2 defaults customer_family

        //Customer
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Particulier", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Private individual", "en_US");

        //Professional
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professionnel", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professional", "en_US");
    }

    public function update($currentVersion, $newVersion, ConnectionInterface $con = null): void
    {
        $finder = Finder::create()
            ->name('*.sql')
            ->depth(0)
            ->sortByName()
            ->in(__DIR__ . DS . 'Config' . DS . 'update');

        $database = new Database($con);

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            if (version_compare($currentVersion, $file->getBasename('.sql'), '<')) {
                $database->insertSql(null, [$file->getPathname()]);
            }
        }
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

        // Set 'particular' as default family
        if ($code == self::CUSTOMER_FAMILY_PARTICULAR) {
            $isDefault = 1;
        } else {
            $isDefault = 0;
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
                    ->save();
            } else {
                $customerFamily = new CustomerFamilyModel();
                $customerFamily
                    ->setCode($code)
                    ->setIsDefault($isDefault)
                    ->setLocale($locale)
                    ->setTitle($title)
                    ->save();
            }
        }

        return $customerFamily;
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
