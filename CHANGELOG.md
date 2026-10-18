# 4.4.0

- Customers pick their family on the Flexy sign-up and account forms when the shop lets them ("Let customers choose
  their family"). The module adds its select to `flexybundle_form_customer_register_form` and
  `flexybundle_form_customer_update_form` when they are built, and saves the choice on `CREATE_CUSTOMER_MINIMAL` and
  `CUSTOMER_UPDATEPROFILE`. The sign-up form needs a Flexy release that renders the fields a module adds to it; the
  account form already does.
- The Smarty `register.html` and `account-update.html` templates, their jQuery scripts and the two hook classes that
  rendered them are removed (the hooks were no longer registered).

# 4.3.0

- The customer's family is shown on the Flexy account page through a theme hook (`account.bottom`), in place of the
  Smarty `account-additional.html` template and its unsubscribed hook class. The register and account update templates
  are untouched.

# 4.2.0

- Smarty is gone: the Smarty back-office templates (the Twig ones stay) and the `customer_family_price`,
  `product_purchase_price` and `customer_family_pse_calculated_prices` loops are removed. The front-office register and
  account templates, and the two loops they call, are untouched.
- The `customer_customer_family` loop binds the family code instead of inlining it in the SQL, and uses the Propel
  `COL_*` constants.

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
