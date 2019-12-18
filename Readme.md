# Customer Family

Create customer families (professional, private individual, ...) and manage specific prices.

Also allow to define purchase prices for products.

## Compatibility
    Thelia >= 2.3.x
    
    For use with lower version of Thelia look for older tags on this module.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/CustomerFamily``` directory and be sure that the name of the module is CustomerFamily.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/customer-family-module:~1.4.0
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

|Argument |Description |
|---      |---         |
|**id** | family id |
|**exclude_id** | exclude family id |
|**is_default** | filter default family |

#### Output values

|Argument |Description |
|---      |---         |
|**CUSTOMER_FAMILY_ID** | customer family id |
|**CODE** | customer family code 
|**TITLE_CUSTOMER_FAMILY** | customer family title |
|**IS_DEFAULT** | default customer family |

#### Example
```
{loop type="customer_family" name="customer_family_loop" current_product=$product_id limit="4"}
    {$CODE} ({$TITLE_CUSTOMER_FAMILY})
{/loop}
```

### customer_customer_family

This loop returns customer family for specific customer or inverse

#### Input arguments

|Argument |Description |
|---      |---         |
|**customer_id** | customer id |
|**customer_family_id** | family id |
|**customer_family_code** | family code |

#### Output values

|Argument |Description |
|---      |---         |
|**CUSTOMER_FAMILY_ID** | customer family id |
|**CUSTOMER_ID** | customer id |
|**SIRET** | siret number |
|**VAT** | vat number id |

#### Example
```
{loop type="customer_customer_family" name="customer_customer_family_loop" customer_id="4"}
    {SIRET}
{/loop}
```

### customer_family_price

This loop returns the customer family's equation data

#### Input arguments

|Argument |Description |
|---      |---         |
|**customer_family_id** | family id |
|**promo** | equation for the promo price or not |
|**use_equation** | is the equation used to calculate price |

#### Output values

|Argument |Description |
|---      |---         |
|**CUSTOMER_FAMILY_ID** | customer family id |
|**PROMO** | equation for the promo price or not |
|**USE_EQUATION** | is the equation used to calculate price |
|**AMOUNT_ADDED_BEFORE** | amount directly added to the purchase price | 
|**AMOUNT_ADDED_AFTER** | amount added to the purchase price after the multiplication |
|**COEFFICIENT** | coefficient the purchase price added to AMOUNT_ADDED_BEFORE is multiplied by |
|**IS_TAXED** | are taxes applied on the final calculated price |

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

|Argument |Description |
|---      |---         |
|**pse_id** | *mandatory*, PSE id |
|**currency_id** | currency id *(if not given, use default currency)* |
|**customer_family_id** | *mandatory*, customer family id |

#### Output values

|Argument |Description |
|---      |---         |
|**CALCULATED_PRICE** | customer family id |
|**CALCULATED_TAXED_PRICE** | equation for the promo price or not |
|**CALCULATED_PROMO_PRICE** | is the equation used to calculate price |
|**CALCULATED_TAXED_PROMO_PRICE** | amount directly added to the purchase price |

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

|Name |Type |Required |
|--- |--- |--- |
|**customer_family_id** | integer | true |
|**siret** | string | false |
|**vat** | string | false |

## Default

By default, two families are created
* Private individual
* Professional
