<?php

namespace CustomerFamily\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class CustomerFamilyProductPriceHook
 * @package CustomerFamily\Hook
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyProductPriceHook extends BaseHook
{
    public function onPsePriceEdit(HookRenderEvent $event)
    {
        $event->add($this->render(
            'product-edit-price.html',
            [
                'pseId' => $event->getArgument('pse'),
                'idx' => $event->getArgument('idx')
            ]
        ));
    }
}