<?php

declare(strict_types=1);

return [
    'create' => [
        'title' => 'New block: :type',
    ],
    'edit' => [
        'title' => 'Edit block: :type',
    ],
    'fields' => [
        'type' => 'Type',
        'summary' => 'Content',
        'is_visible' => 'Visible',
    ],
    'image_count' => '{0} No images|{1} 1 image|[2,*] :count images',
    'actions' => [
        'save' => 'Save block',
        'edit' => 'Edit',
        'hide' => 'Hide',
        'show' => 'Show',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this block?',
    ],
    'flash' => [
        'created' => 'Block added.',
        'saved' => 'Block saved.',
        'deleted' => 'Block deleted.',
    ],
];
