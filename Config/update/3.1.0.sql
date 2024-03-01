# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `customer_family_product_price`;

CREATE TABLE `customer_family_product_price`
(
    `customer_family_id` INTEGER NOT NULL,
    `product_sale_elements_id` INTEGER NOT NULL,
    `price` DECIMAL(16,6) DEFAULT 0.000000 NOT NULL,
    `promo_price` DECIMAL(16,6) DEFAULT 0.000000 NOT NULL,
    `promo` TINYINT DEFAULT 0 NOT NULL,
    PRIMARY KEY (`customer_family_id`,`product_sale_elements_id`),
    INDEX `fi_customer_family_product_price_product_sale_elements_id` (`product_sale_elements_id`),
    CONSTRAINT `fk_customer_family_product_price_customer_family_id`
        FOREIGN KEY (`customer_family_id`)
            REFERENCES `customer_family` (`id`)
            ON UPDATE RESTRICT
            ON DELETE CASCADE,
    CONSTRAINT `fk_customer_family_product_price_product_sale_elements_id`
        FOREIGN KEY (`product_sale_elements_id`)
            REFERENCES `product_sale_elements` (`id`)
            ON UPDATE RESTRICT
            ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;