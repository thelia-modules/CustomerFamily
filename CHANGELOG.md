# 4.1.0

- Customers no longer change their own family unless the shop allows it. A new option, "Let customers choose their
  family" in the module configuration, off by default, decides whether the register and account forms carry the
  family. It used to be read from the raw request on every profile update, so any signed-in customer could switch
  family by adding `thelia_customer_profile_update[customer_family_code]` to the account form (an unknown code ended
  in an error 500). Shops whose customers pick their family must tick the option after updating.
- The back office customer creation sets the family again: the select is posted outside the core form, which refuses
  extra fields, and is read only for an administrator allowed to create customers.
- The category and brand restrictions and the family prices of a product check the administrator's rights on the
  module and the CSRF token. The family price is saved with a `POST` (it was a `GET`, writable from a simple link).

# 4.0.3

- Activating the module on a database that already holds its tables no longer empties them, and creates the tables
  added since an older version (`customer_family_product_price`, `order_product_purchase_price`). The install script
  is run without its `DROP TABLE` statements and with `CREATE TABLE IF NOT EXISTS` (`Service/InstallSql`, unit
  tested), and only when one of its tables is missing. It used to run, `DROP TABLE` included, whenever reading
  `customer_family` failed for any reason, and not at all when that table existed.
- Known, not handled here: creating a table commits the transaction `BaseModule::activate()` opens around
  `postActivation()` (implicit commit of a MySQL DDL statement).
