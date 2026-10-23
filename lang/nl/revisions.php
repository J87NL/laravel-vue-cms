<?php

declare(strict_types=1);

return [
    'fields' => [
        'created_at' => 'Opgeslagen op',
        'author' => 'Door',
        'title' => 'Titel',
        'blocks' => 'Blokken',
    ],
    'unknown_author' => 'Onbekend',
    'current' => 'Dit is de huidige versie.',
    'actions' => [
        'restore' => 'Terugzetten',
        'restore_confirm' => 'Deze versie terugzetten? De paginavelden en blokken worden vervangen; de publicatiestatus blijft zoals hij is.',
    ],
    'diff' => [
        'title' => 'Wat er verandert',
        'description' => 'De wachtende wijzigingen vergeleken met de live pagina, zoals ze bij publiceren worden doorgevoerd.',
        'preview' => 'Voorbeeld met wijzigingen',
        'fields' => 'Paginavelden',
        'blocks' => 'Blokken',
        'empty' => '(leeg)',
        'yes' => 'Ja',
        'no' => 'Nee',
        'moved' => 'Verplaatst van positie :before naar :after',
        'visibility' => 'Zichtbaar: :before → :after',
        'markup_changed' => 'Alleen de opmaak of een link verandert; de tekst blijft gelijk.',
        'links' => 'Linkdoelen',
        'no_links' => '(geen)',
        'status' => [
            'added' => 'Toegevoegd',
            'removed' => 'Verwijderd',
            'changed' => 'Gewijzigd',
            'unchanged' => 'Ongewijzigd',
        ],
    ],
    'flash' => [
        'restored' => 'Versie teruggezet.',
    ],
];
