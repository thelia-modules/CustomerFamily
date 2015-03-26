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

namespace CustomerFamily\Hook;

use CustomerFamily\CustomerFamily;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;

class CustomerFamilyAccountDisplayHook extends BaseHook
{
    public function onAccountAdditional(HookRenderBlockEvent $event)
    {
        $customer = $this->getCustomer();

        if (is_null($customer)) {
            // No customer => nothing to do.
            return;
        }

        $customerId = $customer->getId();
        $title = $this->trans('My customer family');

        $event->add(array(
            'id'      => $customerId,
            'title'   => $title,
            'content' => $this->render(
                'account-additional.html',
                array(
                    'customerId' => $customerId,
                    'particular' => CustomerFamily::CUSTOMER_FAMILY_PARTICULAR,
                    'title'      => $title,
                )
            )
        ));
    }
}
