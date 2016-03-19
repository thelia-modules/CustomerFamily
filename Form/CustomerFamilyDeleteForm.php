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

use Thelia\Form\BaseForm;

/**
 * Class CustomerFamilyDeleteForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyDeleteForm extends BaseForm
{
    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'customer_family_delete_form';
    }

    protected function buildForm()
    {

    }
}
