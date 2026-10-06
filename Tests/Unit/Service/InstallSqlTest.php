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

namespace CustomerFamily\Tests\Unit\Service;

use CustomerFamily\Service\InstallSql;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InstallSqlTest extends TestCase
{
    #[Test]
    public function theInstallScriptNoLongerDropsATable(): void
    {
        $sql = InstallSql::keepingExistingTables((string) file_get_contents(__DIR__.'/../../../Config/TheliaMain.sql'));

        self::assertStringNotContainsString('DROP TABLE', $sql);
        self::assertSame(10, substr_count($sql, 'CREATE TABLE IF NOT EXISTS `'));
        self::assertStringContainsString('CREATE TABLE IF NOT EXISTS `customer_family_product_price`', $sql);
        self::assertStringContainsString('CREATE TABLE IF NOT EXISTS `order_product_purchase_price`', $sql);
    }

    #[Test]
    public function everythingElseOfTheScriptIsKept(): void
    {
        $sql = InstallSql::keepingExistingTables("SET FOREIGN_KEY_CHECKS = 0;\nDROP TABLE IF EXISTS `customer_family`;\n\nCREATE TABLE `customer_family`\n(\n    `id` INTEGER NOT NULL\n) ENGINE=InnoDB;\nSET FOREIGN_KEY_CHECKS = 1;\n");

        self::assertSame("SET FOREIGN_KEY_CHECKS = 0;\n\nCREATE TABLE IF NOT EXISTS `customer_family`\n(\n    `id` INTEGER NOT NULL\n) ENGINE=InnoDB;\nSET FOREIGN_KEY_CHECKS = 1;\n", $sql);
    }

    /**
     * Table options after the engine must not let a statement run on into the DROP TABLE of the next table, and
     * Windows line endings must not make a table silently skipped.
     */
    #[Test]
    #[DataProvider('lineEndings')]
    public function tableOptionsAndLineEndingsKeepEveryTableAndNoDrop(string $eol): void
    {
        $script = str_replace("\n", $eol, "SET FOREIGN_KEY_CHECKS = 0;\n\nDROP TABLE IF EXISTS `customer_family`;\n\nCREATE TABLE `customer_family`\n(\n    `id` INTEGER NOT NULL\n) ENGINE=InnoDB CHARACTER SET='utf8mb4';\n\n"
            ."DROP TABLE IF EXISTS `customer_family_i18n`;\n\nCREATE TABLE `customer_family_i18n`\n(\n    `id` INTEGER NOT NULL\n) ENGINE=InnoDB;\n\nSET FOREIGN_KEY_CHECKS = 1;\n");

        $sql = InstallSql::keepingExistingTables($script);

        self::assertStringNotContainsString('DROP TABLE', $sql);
        self::assertStringContainsString('SET FOREIGN_KEY_CHECKS = 0;', $sql);
        self::assertStringContainsString('CREATE TABLE IF NOT EXISTS `customer_family`', $sql);
        self::assertStringContainsString('CREATE TABLE IF NOT EXISTS `customer_family_i18n`', $sql);
        self::assertSame(['customer_family', 'customer_family_i18n'], InstallSql::createdTables($script));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function lineEndings(): iterable
    {
        yield 'unix' => ["\n"];
        yield 'windows' => ["\r\n"];
    }
}
