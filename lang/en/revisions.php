<?php

declare(strict_types=1);

return [
    'fields' => [
        'created_at' => 'Saved at',
        'author' => 'By',
        'title' => 'Title',
        'blocks' => 'Blocks',
    ],
    'unknown_author' => 'Unknown',
    'current' => 'This is the current version.',
    'actions' => [
        'restore' => 'Restore',
        'restore_confirm' => 'Restore this version? The page fields and blocks are replaced; the publication status stays as it is.',
    ],
    'diff' => [
        'title' => 'What changes',
        'description' => 'The pending changes compared with the live page, as publishing would apply them.',
        'preview' => 'Preview with changes',
        'fields' => 'Page fields',
        'blocks' => 'Blocks',
        'empty' => '(empty)',
        'yes' => 'Yes',
        'no' => 'No',
        'moved' => 'Moves from position :before to :after',
        'visibility' => 'Visible: :before → :after',
        'markup_changed' => 'Only the formatting, a link or an image changes; the text stays the same.',
        'links' => 'Link targets',
        'no_links' => '(none)',
        'status' => [
            'added' => 'Added',
            'removed' => 'Removed',
            'changed' => 'Changed',
            'unchanged' => 'Unchanged',
        ],
    ],
    'flash' => [
        'restored' => 'Version restored.',
    ],
];
