<?php

declare(strict_types=1);

return [
    'edit' => [
        'title' => 'Spambeveiliging',
        'description' => 'Het openbare contactformulier gebruikt altijd een verborgen honeypotveld en een limiet per IP-adres. Cloudflare Turnstile voegt een extra controle toe als je beide sleutels invult.',
    ],
    'status' => [
        'active' => 'Cloudflare Turnstile staat aan op het openbare contactformulier.',
        'inactive' => 'Cloudflare Turnstile staat uit. Vul een sitesleutel en een geheime sleutel in om het aan te zetten.',
    ],
    'fields' => [
        'contact_turnstile_site_key' => 'Turnstile-sitesleutel',
        'contact_turnstile_secret_key' => 'Geheime Turnstile-sleutel',
    ],
    'help' => [
        'contact_turnstile_site_key' => 'Uit de Turnstile-widget in je Cloudflare-dashboard. Laat leeg om Turnstile uit te zetten.',
        'contact_turnstile_secret_key' => 'Wordt versleuteld opgeslagen en nooit meer getoond.',
    ],
    'actions' => [
        'save' => 'Opslaan',
    ],
    'flash' => [
        'saved' => 'Instellingen opgeslagen.',
    ],
];
