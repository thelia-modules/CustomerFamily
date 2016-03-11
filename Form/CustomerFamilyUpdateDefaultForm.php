<?php

namespace CustomerFamily\Form;

use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyUpdateDefaultForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyUpdateDefaultForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_family_id',
                'integer'
            );
    }

    public function getName()
    {
        return 'customer_family_update_default_form';
    }
}