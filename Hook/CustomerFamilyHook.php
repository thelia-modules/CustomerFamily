<?php

namespace CustomerFamily\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class CustomerFamilyHook
 * @package CustomerFamily\Hook
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyHook extends BaseHook
{
    public function onAddCss(HookRenderEvent $event)
    {
        $event->add($this->addCSS('assets/css/style.css'));
    }
}