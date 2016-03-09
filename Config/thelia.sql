
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- customer_family
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family`;

CREATE TABLE `customer_family`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(45) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `customer_family_U_1` (`code`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_customer_family
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_customer_family`;

CREATE TABLE `customer_customer_family`
(
    `customer_id` INTEGER NOT NULL,
    `customer_family_id` INTEGER NOT NULL,
    `siret` VARCHAR(50),
    `vat` VARCHAR(50),
    PRIMARY KEY (`customer_id`),
    INDEX `idx_customer_customer_family_customer_family_id` (`customer_family_id`),
    CONSTRAINT `customer_customer_family_FK_1`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `customer_customer_family_FK_2`
        FOREIGN KEY (`customer_family_id`)
        REFERENCES `customer_family` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_family_price
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family_price`;

CREATE TABLE `customer_family_price`
(
    `customer_family_id` INTEGER NOT NULL,
    `use_equation` TINYINT DEFAULT 0 NOT NULL,
    `amount_added_before` DECIMAL(16,6) DEFAULT 0,
    `amount_added_after` DECIMAL(16,6) DEFAULT 0,
    `multiplication_coefficient` DECIMAL(16,6) DEFAULT 1,
    `is_taxed` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`customer_family_id`),
    CONSTRAINT `fk_customer_family_id`
        FOREIGN KEY (`customer_family_id`)
        REFERENCES `customer_family` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_purchase_price
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product_purchase_price`;

CREATE TABLE `product_purchase_price`
(
    `product_sale_elements_id` INTEGER NOT NULL,
    `currency_id` INTEGER NOT NULL,
    `purchase_price` DECIMAL(16,6) DEFAULT 0,
    PRIMARY KEY (`product_sale_elements_id`,`currency_id`),
    INDEX `FI_currency_id` (`currency_id`),
    CONSTRAINT `fk_product_sale_elements_id`
        FOREIGN KEY (`product_sale_elements_id`)
        REFERENCES `product_sale_elements` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_currency_id`
        FOREIGN KEY (`currency_id`)
        REFERENCES `currency` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_product_purchase_price
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_product_purchase_price`;

CREATE TABLE `order_product_purchase_price`
(
    `order_product_id` INTEGER NOT NULL,
    `purchase_price` DECIMAL(16,6) DEFAULT 0,
    PRIMARY KEY (`order_product_id`),
    CONSTRAINT `fk_order_product_id`
        FOREIGN KEY (`order_product_id`)
        REFERENCES `order_product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_family_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family_i18n`;

CREATE TABLE `customer_family_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `customer_family_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `customer_family` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
