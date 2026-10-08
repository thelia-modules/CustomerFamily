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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Thelia\Form\BaseForm;

class CustomerFamilyCustomerChoiceForm extends BaseForm
{
    public static function getName(): string
    {
        return 'customer_family_customer_choice';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'customer_can_choose_family',
                CheckboxType::class,
                [
                    'label' => $this->translator->trans('Let customers choose their family', [], CustomerFamily::MESSAGE_DOMAIN),
                    'data' => CustomerFamily::customerCanChooseFamily(),
                    'required' => false,
                ]
            );
    }
}
