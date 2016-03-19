<?php

namespace CustomerFamily\Form;

use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyUpdateDefaultForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyUpdateDefaultForm extends BaseForm
{
    public function getName()
    {
        return 'customer_family_update_default_form';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_family_id',
                'integer'
            );
    }
}