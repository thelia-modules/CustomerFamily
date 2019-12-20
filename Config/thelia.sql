
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
    `category_restriction_enabled` TINYINT DEFAULT 0,
    `brand_restriction_enabled` TINYINT DEFAULT 0,
    `is_default` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `customer_family_u_4db226` (`code`)
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
    CONSTRAINT `customer_customer_family_fk_7e8f3e`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `customer_customer_family_fk_90b22e`
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
    `promo` TINYINT DEFAULT 0 NOT NULL,
    `use_equation` TINYINT DEFAULT 0 NOT NULL,
    `amount_added_before` DECIMAL(16,6) DEFAULT 0,
    `amount_added_after` DECIMAL(16,6) DEFAULT 0,
    `multiplication_coefficient` DECIMAL(16,6) DEFAULT 1,
    `is_taxed` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`customer_family_id`,`promo`),
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
    INDEX `fi_currency_id` (`currency_id`),
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
    `sale_day_equation` TEXT NOT NULL,
    PRIMARY KEY (`order_product_id`),
    CONSTRAINT `fk_order_product_id`
        FOREIGN KEY (`order_product_id`)
        REFERENCES `order_product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_family_order
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family_order`;

CREATE TABLE `customer_family_order`
(
    `order_id` INTEGER NOT NULL,
    `customer_family_id` INTEGER NOT NULL,
    PRIMARY KEY (`order_id`),
    INDEX `fi_customer_family_order_customer_family_id` (`customer_family_id`),
    CONSTRAINT `fk_customer_family_order_customer_family_order_id`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_customer_family_order_customer_family_id`
        FOREIGN KEY (`customer_family_id`)
        REFERENCES `customer_family` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_family_available_category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family_available_category`;

CREATE TABLE `customer_family_available_category`
(
    `customer_family_id` INTEGER NOT NULL,
    `category_id` INTEGER NOT NULL,
    PRIMARY KEY (`customer_family_id`,`category_id`),
    INDEX `idx_customer_family_available_category_customer_family_id` (`customer_family_id`),
    INDEX `idx_customer_family_available_category_category_id` (`category_id`),
    CONSTRAINT `fk_customer_family_available_category_customer_family_id`
        FOREIGN KEY (`customer_family_id`)
        REFERENCES `customer_family` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_customer_family_available_category_category_id`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_family_available_brand
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_family_available_brand`;

CREATE TABLE `customer_family_available_brand`
(
    `customer_family_id` INTEGER NOT NULL,
    `brand_id` INTEGER NOT NULL,
    PRIMARY KEY (`customer_family_id`,`brand_id`),
    INDEX `idx_customer_family_available_brand_customer_family_id` (`customer_family_id`),
    INDEX `idx_customer_family_available_brand_brand_id` (`brand_id`),
    CONSTRAINT `fk_customer_family_available_brand_customer_family_id`
        FOREIGN KEY (`customer_family_id`)
        REFERENCES `customer_family` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_customer_family_available_brand_brand_id`
        FOREIGN KEY (`brand_id`)
        REFERENCES `brand` (`id`)
        ON UPDATE CASCADE
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
    CONSTRAINT `customer_family_i18n_fk_0253ce`
        FOREIGN KEY (`id`)
        REFERENCES `customer_family` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
