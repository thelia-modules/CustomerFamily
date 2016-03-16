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

/**
 * Class CustomerFamilyUpdateForm
 * @package CustomerFamily\Form
 */
class CustomerFamilyUpdateForm extends CustomerFamilyCreateForm
{
    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'customer_family_update_form';
    }

    protected function buildForm()
    {
        parent::buildForm();
    }
}
