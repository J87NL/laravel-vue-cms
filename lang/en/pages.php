<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Pages',
        'description' => 'Drag a page above, below or onto another page to change the order or the structure.',
        'empty' => 'There are no pages yet.',
        'home' => 'Home page',
    ],
    'create' => [
        'title' => 'New page',
        'description' => 'A new page starts as a draft. It only becomes visible once it is published.',
    ],
    'edit' => [
        'view' => 'View page',
        'blocks' => 'Content',
        'blocks_description' => 'The page is built from these blocks, from top to bottom. Drag to reorder.',
        'add_block' => 'Add block',
        'revisions' => 'Revisions',
        'revisions_description' => 'Every save keeps a revision. The last 20 are kept.',
        'danger' => 'Delete page',
        'danger_description' => 'Deleting removes the page, its blocks and its revisions.',
    ],
    'sections' => [
        'page' => 'Page',
        'seo' => 'Search engines and sharing',
    ],
    'fields' => [
        'title' => 'Title',
        'slug' => 'Address',
        'parent_id' => 'Parent page',
        'menu_title' => 'Title in menus',
        'meta_title' => 'Title for search engines',
        'meta_description' => 'Description for search engines',
        'og_image_id' => 'Image when shared',
        'canonical_url' => 'Canonical address',
        'schema_type' => 'Page type',
        'noindex' => 'Hide from search engines',
        'status' => 'Status',
    ],
    'help' => [
        'slug' => 'The last part of the address. Left empty, it is made from the title.',
        'menu_title' => 'Shorter title for menus. Left empty, the title is used.',
        'seo' => 'These fields are stored now and used once search engine support is switched on.',
        'noindex' => 'Asks search engines not to show this page.',
    ],
    'actions' => [
        'create' => 'New page',
        'save' => 'Save',
        'edit' => 'Edit',
        'publish' => 'Publish',
        'unpublish' => 'Take offline',
        'make_home' => 'Make home page',
        'delete' => 'Delete page',
        'delete_confirm' => 'Delete this page? This cannot be undone.',
    ],
    'publication' => [
        'title' => 'Publication',
        'requires_permission' => 'You may edit this page, but publishing and taking it offline requires the "Publish pages" permission.',
        'delete_requires_publish' => 'This page is published. Deleting it requires the "Publish pages" permission.',
        'is_home' => 'This is the home page.',
        'write_requires_publish' => 'This page is published. Changing it requires the "Publish pages" permission.',
        'move_requires_publish' => 'Moving a published page requires the "Publish pages" permission.',
    ],
    'flash' => [
        'created' => 'Page ":title" created.',
        'saved' => 'Page saved.',
        'deleted' => 'Page ":title" deleted.',
        'moved' => 'Page moved.',
        'published' => 'Page published.',
        'unpublished' => 'Page taken offline.',
        'home' => 'This page is now the home page.',
    ],
];
