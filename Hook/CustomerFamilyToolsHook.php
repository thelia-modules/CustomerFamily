<?php
/**
 * Created by PhpStorm.
 * User: nicolasbarbey
 * Date: 18/09/2019
 * Time: 16:45
 */

namespace CustomerFamily\Hook;


use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

class CustomerFamilyToolsHook extends BaseHook
{
    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $event->add(
            [
                'id' => 'tools_menu_customer_family',
                'class' => '',
                'url' => URL::getInstance()->absoluteUrl('/admin/module/CustomerFamily'),
                'title' => "Customer families"
            ]
        );
    }
}