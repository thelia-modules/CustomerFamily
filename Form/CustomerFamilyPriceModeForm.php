<?php
/**
 * Created by PhpStorm.
 * User: nicolasbarbey
 * Date: 19/09/2019
 * Time: 09:30
 */

namespace CustomerFamily\Form;


use CustomerFamily\CustomerFamily;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Thelia\Form\BaseForm;

class CustomerFamilyPriceModeForm extends BaseForm
{
    public static function getName()
    {
        return 'customer_family_price_mode';
    }

    protected function buildForm()
    {
        $value = CustomerFamily::getConfigValue("customer_family_price_mode", 0);
        $value = $value == 1 ? true : false;

        $this->formBuilder
            ->add(
                "price_mode",
                CheckboxType::class,
                [
                    "label" => $this->translator->trans("Use the product price", [], CustomerFamily::MESSAGE_DOMAIN),
                    "data" => $value,
                    'label_attr'  => [
                       'help' => $this->translator->trans(
                        "By checking this check box this module will use the product price of Thelia instead of the the purchase price of CustomerFamily",
                            [],
                    CustomerFamily::MESSAGE_DOMAIN),
                    ]
                ]
            );
    }

}