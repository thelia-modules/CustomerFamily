SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `customer_customer_family` ADD `company_name` VARCHAR(250) AFTER `customer_family_id`;

SET FOREIGN_KEY_CHECKS = 1;