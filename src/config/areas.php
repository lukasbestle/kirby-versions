<?php

use LukasBestle\Versions\Plugin;

return [
    'versions' => function ($kirby) {
        $accessPermission = Plugin::instance()->hasPermission('access');

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
