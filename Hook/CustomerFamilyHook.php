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
    public static function getSubscribedHooks(): array
    {
        return [
            'main.head-css' => [
                ['type' => 'back', 'method' => 'onAddCss'],
            ],
        ];
    }

    public function onAddCss(HookRenderEvent $event): void
    {
        $event->add($this->render('customer-family-css.html.twig'));
    }
}
