<?php

namespace CustomerFamily\Form;

use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyPriceForm
 * @package CustomerFamily\Form
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceForm extends BaseForm
{
    public function getName()
    {
        return 'customer_family_price_update';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_family_id',
                'integer'
            )
            ->add(
                'promo',
                'integer'
            )
            ->add(
                'use_equation',
                'checkbox',
                []
            )
            ->add(
                'amount_added_before',
                'number',
                [
                    'precision' => 6,
                    'required' => false
                ]
            )
            ->add(
                'amount_added_after',
                'number',
                [
                    'precision' => 6,
                    'required' => false
                ]
            )
            ->add(
                'coefficient',
                'number',
                [
                    'precision' => 6,
                    'required' => false
                ]
            )
            ->add(
                'is_taxed',
                'checkbox',
                []
            )
        ;
    }
}