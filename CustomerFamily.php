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
use CustomerFamily\Service\InstallSql;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Core\Install\Database;
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
    public const MODULE_DOMAIN = 'customerfamily';

    /** @cont string */
    public const MESSAGE_DOMAIN = 'customerfamily';

    /** @cont string */
    public const CUSTOMER_FAMILY_PARTICULAR = "particular";

    /** @cont string */
    public const CUSTOMER_FAMILY_PROFESSIONAL = "professional";

    /** Module configuration key: when set, customers pick their own family on the register and account forms. */
    public const CUSTOMER_CAN_CHOOSE_FAMILY = 'customer_can_choose_family';

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(?ConnectionInterface $con = null): void
    {

        $database = new Database($con);
        $installSql = InstallSql::keepingExistingTables((string) file_get_contents(__DIR__ . "/Config/TheliaMain.sql"));

        // A table creation commits the transaction of the activation: run the script only when a table is missing.
        if (self::hasMissingTable($database, InstallSql::createdTables($installSql))) {
            $installScript = tempnam(sys_get_temp_dir(), 'customerfamily');

            if (false === $installScript) {
                throw new \RuntimeException('Unable to write the CustomerFamily install script to the temporary directory');
            }

            file_put_contents($installScript, $installSql);

            try {
                $database->insertSql(null, [$installScript]);
            } finally {
                unlink($installScript);
            }
        }

        //Generate the 2 defaults customer_family

        //Customer
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Particulier", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PARTICULAR, "Private individual", "en_US");

        //Professional
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professionnel", "fr_FR");
        self::getCustomerFamilyByCode(self::CUSTOMER_FAMILY_PROFESSIONAL, "Professional", "en_US");
    }

    /**
     * @param list<string> $tables
     */
    private static function hasMissingTable(Database $database, array $tables): bool
    {
        if ([] === $tables) {
            return false;
        }

        $existing = (int) $database->execute(
            sprintf(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN (%s)',
                implode(', ', array_fill(0, \count($tables), '?'))
            ),
            $tables
        )->fetchColumn();

        return $existing < \count($tables);
    }

    public function update($currentVersion, $newVersion, ?ConnectionInterface $con = null): void
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

    public static function customerCanChooseFamily(): bool
    {
        return (bool) self::getConfigValue(self::CUSTOMER_CAN_CHOOSE_FAMILY, false);
    }

    /**
     * @param $code
     * @param null $title
     * @param string $locale
     *
     * @return Model\CustomerFamily
     */
    public static function getCustomerFamilyByCode($code, $title = null, $locale = "fr_FR"): ?CustomerFamilyModel
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
            // The tests are no services of the shop, and PHPUnit is not installed with it.
            ->exclude([__DIR__.'/I18n/*', __DIR__.'/Tests/*'])
            ->autowire()
            ->autoconfigure();
    }
}
