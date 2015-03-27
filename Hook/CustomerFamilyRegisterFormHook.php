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
use Thelia\Form\CustomerCreateForm;

class CustomerFamilyRegisterFormHook extends BaseHook
{
    /**
     * Extra form fields.
     * @param HookRenderEvent $event
     */
    public function onRegisterFormBottom(HookRenderEvent $event)
    {
        $event->add($this->render(
            'register.html',
            array(
                'form' => $event->getArgument('form'),
                'messageDomain' => CustomerFamily::MESSAGE_DOMAIN,
            )
        ));
    }

    /**
     * Javascript for extra form fields.
     * @param HookRenderEvent $event
     */
    public function onRegisterAfterJSInclude(HookRenderEvent $event)
    {
        $event->add($this->addJS('assets/js/register.js'));
    }
}
