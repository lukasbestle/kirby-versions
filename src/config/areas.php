<?php

return [
    'versions' => function () {
        return [
            'label' => t('view.versions'),
            'icon'  => 'layers',
            'menu'  => true,
            'views' => [
                [
                    'pattern' => 'versions',
                    'action'  => function () {
                        return [
                            'component' => 'lbvs-versions-view',
                        ];
                    }
                ]
            ]
        ];
    }
];
