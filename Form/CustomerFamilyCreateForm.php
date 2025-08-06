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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
    public static function getName(): string
    {
        return 'customer_family_create_form';
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

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'code',
                TextType::class,
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
                TextType::class,
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
                TextType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new Callback(
                            array($this, "checkLocale")
                        )
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
}
