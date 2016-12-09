CREATE TABLE IF NOT EXISTS `customer_family_order`
(
    `order_id` INTEGER NOT NULL,
    `customer_family_id` INTEGER NOT NULL,
    PRIMARY KEY (`order_id`),
    INDEX `FI_customer_family_order_customer_family_id` (`customer_family_id`),
    CONSTRAINT `fk_customer_family_order_customer_family_order_id`
    FOREIGN KEY (`order_id`)
    REFERENCES `order` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_customer_family_order_customer_family_id`
    FOREIGN KEY (`customer_family_id`)
    REFERENCES `customer_family` (`id`)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

