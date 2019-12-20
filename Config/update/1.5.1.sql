# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `customer_family` ADD `category_restriction_enabled` TINYINT DEFAULT 0 AFTER `code`;
ALTER TABLE `customer_family` ADD `brand_restriction_enabled` TINYINT DEFAULT 0 AFTER `category_restriction_enabled`;

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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
