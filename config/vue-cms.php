<?php

declare(strict_types=1);

return [

    'admin' => [
        'prefix' => 'admin/cms',
        'middleware' => [
            'web',
            'auth',
        ],
    ],

    'pages' => [
        'dashboard' => 'cms/Dashboard',
    ],

    'navigation_config_key' => 'j87nl.navigation',

];
