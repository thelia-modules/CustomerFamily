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

use CustomerFamily\CustomerFamily;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints\Callback;
use Thelia\Model\LangQuery;

/**
 * Class CustomerFamilyCreateForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyCreateForm extends BaseForm
{
    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'customer_family_create_form';
    }

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'code',
                'text',
                array(
                    'constraints' => array(
                        new NotBlank()
                    ),
                    'required' => true,
                    'empty_data' => false,
                    'label' => Translator::getInstance()->trans(
                        'Code',
                        array(),
                        CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'code'
                    )
                )
            )
            ->add(
                'title',
                'text',
                array(
                    'constraints' => array(
                        new NotBlank()
                    ),
                    'required' => true,
                    'empty_data' => false,
                    'label' => Translator::getInstance()->trans(
                        'Title'
                    ),
                    'label_attr' => array(
                        'for' => 'title'
                    )
                )
            )
            ->add(
                'locale',
                'text',
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new Callback(array("methods" => array(
                            array($this, "checkLocale")
                        )))
                    ),
                    'required' => true,
                    'empty_data' => false,
                    'label' => Translator::getInstance()->trans(
                        'Locale'
                    ),
                    'label_attr' => array(
                        'for' => 'locale'
                    )
                )
            );
    }

    /**
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function checkLocale($value, ExecutionContextInterface $context)
    {
        if (!LangQuery::create()->findOneByCode($value) === null) {
            $context->addViolation(Translator::getInstance()->trans(
                "Invalid locale"
            ));
        }
    }
}
