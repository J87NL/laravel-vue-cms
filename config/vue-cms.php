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
        'pages_index' => 'cms/pages/Index',
        'pages_edit' => 'cms/pages/Edit',
        'form' => 'cms/Form',
        'media_index' => 'cms/media/Index',
        'menus_index' => 'cms/menus/Index',
        'redirects_index' => 'cms/redirects/Index',
    ],

    'navigation_config_key' => 'j87nl.navigation',

    'toast_flash_key' => 'toast',

    'schema_types' => [
        'WebPage',
        'AboutPage',
        'ContactPage',
        'CollectionPage',
        'FAQPage',
    ],

];
