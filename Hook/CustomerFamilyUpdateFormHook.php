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
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class CustomerFamilyUpdateFormHook extends BaseHook
{
    public function onAccountUpdateFormBottom(HookRenderEvent $event)
    {
        $event->add($this->render(
            'account-update.html',
            array(
                'form' => $event->getArgument('form'),
                'messageDomain' => CustomerFamily::MESSAGE_DOMAIN,
            )
        ));
    }

    public function onAccountUpdateAfterJSInclude(HookRenderEvent $event)
    {
        $event->add($this->addJS('assets/js/update.js'));
    }
}
