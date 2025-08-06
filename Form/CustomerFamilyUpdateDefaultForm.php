<?php

namespace CustomerFamily\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyUpdateDefaultForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyUpdateDefaultForm extends BaseForm
{
    public static function getName(): string
    {
        return 'customer_family_update_default_form';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_family_id',
                IntegerType::class
            );
    }
}