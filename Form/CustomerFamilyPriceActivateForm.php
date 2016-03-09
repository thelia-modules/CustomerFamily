<?php

namespace CustomerFamily\Form;

use CustomerFamily\CustomerFamily;
use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyPriceActivateForm
 * @package CustomerFamily\Form
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceActivateForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "activate",
                "checkbox",
                [
                    "label" => $this->translator->trans(
                        "Activate / deactivate specific price calculation:",
                        [],
                        CustomerFamily::MODULE_DOMAIN
                    ),
                    "label_attr" => ['for' => 'activate'],
                    "value" => CustomerFamily::getConfigValue(CustomerFamily::PRICE_CALC_ACTIVE, false),
                ]
            );
    }

    public function getName()
    {
        return 'customer_family_price_activate';
    }
}