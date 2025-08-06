<?php

namespace CustomerFamily\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyPriceForm
 * @package CustomerFamily\Form
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceForm extends BaseForm
{
    public static function getName(): string
    {
        return 'customer_family_price_update';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_family_id',
                IntegerType::class
            )
            ->add(
                'promo',
                IntegerType::class
            )
            ->add(
                'use_equation',
                CheckboxType::class,
                []
            )
            ->add(
                'amount_added_before',
                NumberType::class,
                [
                    'scale' => 6,
                    'required' => false
                ]
            )
            ->add(
                'amount_added_after',
                NumberType::class,
                [
                    'scale' => 6,
                    'required' => false
                ]
            )
            ->add(
                'coefficient',
                NumberType::class,
                [
                    'scale' => 6,
                    'required' => false
                ]
            )
            ->add(
                'is_taxed',
                CheckboxType::class,
                []
            )
        ;
    }
}