<?php

declare(strict_types=1);

return [
    'edit' => [
        'title' => 'Spam protection',
        'description' => 'The public contact form always uses a hidden honeypot field and a limit per IP address. Cloudflare Turnstile adds an extra check when you fill in both keys.',
    ],
    'status' => [
        'active' => 'Cloudflare Turnstile is on for the public contact form.',
        'inactive' => 'Cloudflare Turnstile is off. Fill in a site key and a secret key to switch it on.',
    ],
    'fields' => [
        'contact_turnstile_site_key' => 'Turnstile site key',
        'contact_turnstile_secret_key' => 'Turnstile secret key',
    ],
    'help' => [
        'contact_turnstile_site_key' => 'From the Turnstile widget in your Cloudflare dashboard. Leave empty to switch Turnstile off.',
        'contact_turnstile_secret_key' => 'Stored encrypted and never shown again.',
    ],
    'actions' => [
        'save' => 'Save',
    ],
    'flash' => [
        'saved' => 'Settings saved.',
    ],
];
