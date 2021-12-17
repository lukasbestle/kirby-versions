<?php

use Kirby\Exception\LogicException;
use LukasBestle\Versions\Plugin;

return [
    'versions' => function ($kirby) {
        try {
            $accessPermission = Plugin::instance()->hasPermission('access');
        } catch (LogicException $e) {
            // area was loaded by Kirby without a logged-in user
            $accessPermission = false;
        }

        return [
            'label' => t('view.versions'),
            'icon'  => 'layers',
            'menu'  => $accessPermission ? true : 'disabled',
            'views' => [
                [
                    'pattern' => 'versions',
                    'action'  => function () {
                        return [
                            'component' => 'lbvs-view',
                        ];
                    }
                ]
            ]
        ];
    }
];
