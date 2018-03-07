<?php
/*************************************************************************************/
/*      This file is part of the module CustomerFamily                               */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerFamily\Form;

use Symfony\Component\Validator\Constraints;
use CustomerFamily\CustomerFamily;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class CustomerCustomerFamilyForm
 * @package CustomerFamily\Form
 */
class CustomerCustomerFamilyForm extends BaseForm
{
    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'customer_customer_family_form';
    }

    /**
     * Validate a field only if customer family is professional
     *
     * @param string                    $value
     * @param ExecutionContextInterface $context
     */
    public function checkProfessionalInformation($value, ExecutionContextInterface $context)
    {
        $customerFamily = CustomerFamily::getCustomerFamilyByCode(CustomerFamily::CUSTOMER_FAMILY_PROFESSIONAL);

        if (null != $form = $this->getRequest()->request->get("customer_customer_family_form")) {
            if (array_key_exists("customer_family_id", $form) &&
                $form["customer_family_id"] == $customerFamily->getId()) {
                if (strlen($value) <= 1) {
                    $context->addViolation(Translator::getInstance()->trans(
                        "This field can't be empty",
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ));
                }
            }
        }
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add('customer_id', 'integer', array(
                    'constraints' => array(
                        new Constraints\NotBlank()
                    ),
                    'label' => Translator::getInstance()->trans(
                        'Customer',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'customer_id'
                    )
                ))
            ->add('customer_family_id', 'integer', array(
                    'constraints' => array(
                        new Constraints\NotBlank()
                    ),
                    'label' => Translator::getInstance()->trans(
                        'Customer family',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'customer_id'
                    )
                ))
            ->add('company_name','text', array(
                    'constraints' => array(
                        new Constraints\Callback(array("methods" => array(
                            array($this, "checkProfessionalInformation")
                        )))
                    ),
                    'label' => Translator::getInstance()->trans(
                        'Company name',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'company_name'
                    )
                ))
            ->add(
                'siret','text', array(
                    'constraints' => array(
                        new Constraints\Callback(array("methods" => array(
                            array($this, "checkProfessionalInformation")
                        )))
                    ),
                    'required' => false,
                    'empty_data' => false,
                    'label' => Translator::getInstance()->trans(
                        'Siret number',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'siret'
                    )
                )
            )
            ->add(
                'vat',
                'text',
                array(
                    'constraints' => array(
                        new Constraints\Callback(array("methods" => array(
                            array($this, "checkProfessionalInformation")
                        )))
                    ),
                    'required' => false,
                    'empty_data' => false,
                    'label' => Translator::getInstance()->trans(
                        'Vat',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'vat'
                    )
                )
            )
        ;
    }
}
