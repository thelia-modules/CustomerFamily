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
composer require thelia/customer-family-module:~1.5.0
```

## Usage

This module is visible in the BackOffice Customer Edit.

Use the first tab to create, edit or remove families. You can also define default family, use to show specific price to unlogged customers.

By default only an administrator sets the family of a customer. Tick "Let customers choose their family" in the first tab to let customers pick it on the register and account forms.

The second tab allows you to define how prices are calculated for each family, depending on the products purchase price.

Product's prices are automatically changed in the 'product' loop.

## Loops

Use provided loops to read the customer families.

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

#### Example
```
{loop type="customer_customer_family" name="customer_customer_family_loop" customer_id="4"}

{/loop}
```

## Form customer_family_customer_create_form

This form extend customer_create_form

### Fields

|Name |Type |Required |
|--- |--- |--- |
|**customer_family_id** | integer | true |

## Default

By default, two families are created
* Private individual
* Professional
