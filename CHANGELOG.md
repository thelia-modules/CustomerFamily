# 4.0.3

- Activating the module on a database that already holds its tables no longer empties them, and creates the tables
  added since an older version (`customer_family_product_price`, `order_product_purchase_price`). The install script
  is run without its `DROP TABLE` statements and with `CREATE TABLE IF NOT EXISTS` (`Service/InstallSql`, unit
  tested), and only when one of its tables is missing. It used to run, `DROP TABLE` included, whenever reading
  `customer_family` failed for any reason, and not at all when that table existed.
- Known, not handled here: creating a table commits the transaction `BaseModule::activate()` opens around
  `postActivation()` (implicit commit of a MySQL DDL statement).
