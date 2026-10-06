<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CustomerFamily\Service;

/**
 * Makes the generated install script safe to run on a database that already holds the tables.
 *
 * A database carried over from an older version keeps the tables of the module, but may lack the ones added since
 * (customer_family_product_price, order_product_purchase_price), and the DROP TABLE of the generated script would
 * empty the others. The tables are now created only when they do not exist; removing them stays the job of destroy().
 */
final class InstallSql
{
    public static function keepingExistingTables(string $sql): string
    {
        $sql = preg_replace('/^[ \t]*DROP TABLE IF EXISTS `\w+`;[ \t]*\R/m', '', $sql) ?? $sql;

        return preg_replace('/^(\s*)CREATE TABLE `/m', '$1CREATE TABLE IF NOT EXISTS `', $sql) ?? $sql;
    }

    /**
     * @return list<string> the tables the script creates, in its order
     */
    public static function createdTables(string $sql): array
    {
        preg_match_all('/^\s*CREATE TABLE (?:IF NOT EXISTS )?`(\w+)`/m', $sql, $tables);

        return $tables[1];
    }
}
