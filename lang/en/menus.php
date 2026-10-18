<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Menus',
        'description' => 'Items that point at a page follow its address and only show while the page is published.',
    ],
    'create' => [
        'title' => 'New menu item',
    ],
    'edit' => [
        'title' => 'Edit menu item',
    ],
    'fields' => [
        'link_type' => 'Links to',
        'page_id' => 'Page',
        'url' => 'Link',
        'label' => 'Label',
        'parent_id' => 'Under',
        'link' => 'Link',
        'status' => 'Status',
    ],
    'link_types' => [
        'page' => 'A page',
        'url' => 'Another address',
    ],
    'help' => [
        'url' => 'A path such as /contact, or an address starting with https://, mailto: or tel:.',
        'label' => 'Left empty for a page, the title in menus is used.',
    ],
    'statuses' => [
        'visible' => 'Visible',
        'hidden' => 'Hidden (the page or a page above it is not published)',
    ],
    'errors' => [
        'has_children' => 'This item has items below it and cannot be placed under another item.',
    ],
    'actions' => [
        'create' => 'Add item',
        'save' => 'Save',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this menu item and the items below it?',
    ],
    'flash' => [
        'saved' => 'Menu item saved.',
        'deleted' => 'Menu item deleted.',
    ],
];
