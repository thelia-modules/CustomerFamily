# Customer Family

Create customer families (professional, private individual, ...) and manage specific prices.

Also allow to define purchase prices for products.

## Compatibility
* To use on Thelia 2.3.x, use tag [1.3](https://github.com/thelia-modules/CustomerFamily/tree/1.3)
* To use on Thelia 2.2.x, use tag [1.2](https://github.com/thelia-modules/CustomerFamily/tree/1.2)
* To use on Thelia 2.1.x, use tag [1.1](https://github.com/thelia-modules/CustomerFamily/tree/1.1)
* To use on Thelia 2.0.x, use tag [1.0](https://github.com/thelia-modules/CustomerFamily/tree/1.0)

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/CustomerFamily``` directory and be sure that the name of the module is CustomerFamily.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/customer-family-module:~1.0
```

## Usage

This module is visible in the BackOffice Customer Edit.

Use the first tab to create, edit or remove families. You can also define default family, use to show specific price to unlogged customers.

The second tab allows you to define how prices are calculated for each family, depending on the products purchase price.

Product's prices are automatically changed in the 'product' loop, you don't need to use 'customer_family_pse_calculated_prices' loop (see below) to get product's prices.

## Loops

Use provided loops to display for example taxed or untaxed price depending on the customer's family.

### customer_family

This loop returns client families

#### Input arguments

|Argument |Description |Version |
|---      |---         |--- |
|**id** | family id | 1.0
|**exclude_id** | exclude family id | 1.0
|**is_default** | filter default family | 1.3

#### Output values

|Argument |Description |Version |
|---      |---         |--- |
|**CUSTOMER_FAMILY_ID** | customer family id | 1.0
|**CODE** | customer family code | 1.0
|**TITLE_CUSTOMER_FAMILY** | customer family title | 1.0
|**IS_DEFAULT** | default customer family | 1.3

#### Example
```
{loop type="customer_family" name="customer_family_loop" current_product=$product_id limit="4"}
    {$CODE} ({$TITLE_CUSTOMER_FAMILY})
{/loop}
```

### customer_customer_family

This loop returns customer family for specific customer

#### Input arguments

|Argument |Description |Version |
|---      |---         |--- |
|**customer_id** | customer id | 1.0
|**customer_family_id** | family id | 1.0

#### Output values

|Argument |Description |Version |
|---      |---         |--- |
|**CUSTOMER_FAMILY_ID** | customer family id | 1.0
|**CUSTOMER_ID** | customer id | 1.0
|**SIRET** | siret number | 1.0
|**VAT** | vat number id | 1.0

#### Example
```
{loop type="customer_customer_family" name="customer_customer_family_loop" customer_id="4"}
    {SIRET}
{/loop}
```

### customer_family_price

This loop returns the customer family's equation data

#### Input arguments

|Argument |Description |Version |
|---      |---         |--- |
|**customer_family_id** | family id | 1.3
|**promo** | equation for the promo price or not | 1.3
|**use_equation** | is the equation used to calculate price | 1.3

#### Output values

|Argument |Description |Version |
|---      |---         |--- |
|**CUSTOMER_FAMILY_ID** | customer family id | 1.3
|**PROMO** | equation for the promo price or not | 1.3
|**USE_EQUATION** | is the equation used to calculate price | 1.3
|**AMOUNT_ADDED_BEFORE** | amount directly added to the purchase price | 1.3
|**AMOUNT_ADDED_AFTER** | amount added to the purchase price after the multiplication | 1.3
|**COEFFICIENT** | coefficient the purchase price added to AMOUNT_ADDED_BEFORE is multiplied by | 1.3
|**IS_TAXED** | are taxes applied on the final calculated price | 1.3

#### Example
```
{loop type="customer_family_price" name="customer_family_price_loop" customer_family_id=1 promo=0}
    {$AMOUNT_ADDED_BEFORE}
    ...
{/loop}
```

### customer_family_pse_calculated_prices

This loop returns the PSE's calculated price based on the given customer family & currency

#### Input arguments

|Argument |Description |Version |
|---      |---         |--- |
|**pse_id** | *mandatory*, PSE id | 1.3
|**currency_id** | currency id *(if not given, use default currency)* | 1.3
|**customer_family_id** | *mandatory*, customer family id | 1.3

#### Output values

|Argument |Description |Version |
|---      |---         |--- |
|**CALCULATED_PRICE** | customer family id | 1.3
|**CALCULATED_TAXED_PRICE** | equation for the promo price or not | 1.3
|**CALCULATED_PROMO_PRICE** | is the equation used to calculate price | 1.3
|**CALCULATED_TAXED_PROMO_PRICE** | amount directly added to the purchase price | 1.3

#### Example
```
{loop type="customer_family_pse_calculated_prices" name="customer_family_pse_calculated_prices_loop" pse_id=22 customer_family_id=1}
    {$CALCULATED_TAXED_PRICE}
    ...
{/loop}
```

## Form customer_family_customer_create_form

This form extend customer_create_form

### Fields

|Name |Type |Required |Version |
|--- |--- |--- |--- |
|**customer_family_id** | integer | true | 1.0
|**siret** | string | false | 1.0
|**vat** | string | false | 1.0

## Default

By default, two families are created
* Private individual
* Professional
