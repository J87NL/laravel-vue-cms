<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Redirects',
        'description' => 'Old addresses that forward to a new address. Renaming or moving a page adds a redirect here automatically.',
    ],
    'create' => [
        'title' => 'Add redirect',
    ],
    'edit' => [
        'title' => 'Edit redirect',
    ],
    'import' => [
        'title' => 'Import CSV',
        'description' => 'One redirect per line: old address, new address and optionally the status code (301 or 302). An existing redirect from the same old address is updated. If any line contains an error, nothing is imported.',
        'failed_lines' => 'Nothing was imported: :count lines contain an error. Fix the file and import it again. :lines',
    ],
    'fields' => [
        'from_path' => 'Old address',
        'to' => 'New address',
        'status_code' => 'Type',
        'source' => 'Source',
        'hits' => 'Hits',
        'last_hit_at' => 'Last hit',
        'file' => 'CSV file',
    ],
    'help' => [
        'from_path' => 'For example /old-page. A full address on this site works too.',
        'to' => 'A path such as /new-page, or a full address starting with https://.',
        'file' => 'Comma or semicolon separated. A header line starting with "from" or "van" is skipped.',
    ],
    'status_codes' => [
        '301' => '301: moved permanently',
        '302' => '302: moved temporarily',
    ],
    'actions' => [
        'save' => 'Save',
        'import' => 'Import',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this redirect?',
    ],
    'flash' => [
        'saved' => 'Redirect saved.',
        'deleted' => 'Redirect deleted.',
        'imported' => '{0} No redirects imported.|{1} 1 redirect imported.|[2,*] :count redirects imported.',
    ],
];
