<?php

declare(strict_types=1);

return [
    'edit' => [
        'title' => 'Site settings',
        'description' => 'Details that appear on every page: the site name, default texts for search engines and who is behind the site.',
    ],
    'sections' => [
        'site' => 'Site',
        'entity' => 'Who is behind it',
        'findability' => 'Search engines and AI',
    ],
    'fields' => [
        'site_name' => 'Site name',
        'title_pattern' => 'Page title',
        'default_meta_description' => 'Default description',
        'og_image_id' => 'Default sharing image',
        'privacy_page_id' => 'Privacy statement',
        'contact_recipient' => 'Contact form to',
        'entity_type' => 'Type',
        'entity_name' => 'Name',
        'entity_job_title' => 'Job title',
        'entity_email' => 'Email address',
        'entity_image_id' => 'Photo or logo',
        'entity_same_as' => 'Profiles elsewhere',
        'robots_extra' => 'Extra robots.txt lines',
        'llms_intro' => 'llms.txt introduction',
    ],
    'help' => [
        'title_pattern' => 'Use :title for the page title, for example ":title | My website". Empty means ":title | site name".',
        'default_meta_description' => 'For pages without their own description and without a text block.',
        'og_image_id' => 'Shown when a page without its own image is shared.',
        'privacy_page_id' => 'Forms on the site link to this page.',
        'contact_recipient' => 'The address the contact form mails to. The sender becomes the reply-to address.',
        'entity' => 'Search engines show these details with the site.',
        'entity_job_title' => 'Only for a person.',
        'entity_email' => 'Not shown on the page, only in the data for search engines. Leave empty if you prefer.',
        'entity_same_as' => 'One full address per line, for example your LinkedIn or GitHub profile.',
        'robots_extra' => 'For example "User-agent: GPTBot" and "Disallow: /" to keep a specific crawler out.',
        'llms_intro' => 'A short description of the site for AI assistants. Empty means the default description.',
    ],
    'actions' => [
        'save' => 'Save',
    ],
    'flash' => [
        'saved' => 'Settings saved.',
    ],
];
