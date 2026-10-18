<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Pagina\'s',
        'description' => 'Sleep een pagina boven, onder of op een andere pagina om de volgorde of de structuur te veranderen.',
        'empty' => 'Er zijn nog geen pagina\'s.',
        'home' => 'Startpagina',
    ],
    'create' => [
        'title' => 'Nieuwe pagina',
        'description' => 'Een nieuwe pagina begint als concept. Hij is pas zichtbaar als hij gepubliceerd is.',
    ],
    'edit' => [
        'view' => 'Pagina bekijken',
        'blocks' => 'Inhoud',
        'blocks_description' => 'De pagina bestaat uit deze blokken, van boven naar beneden. Sleep om de volgorde te veranderen.',
        'add_block' => 'Blok toevoegen',
        'revisions' => 'Versies',
        'revisions_description' => 'Elke wijziging bewaart een versie. De laatste 20 blijven bewaard.',
        'danger' => 'Pagina verwijderen',
        'danger_description' => 'Verwijderen haalt de pagina, de blokken en de versies weg.',
    ],
    'sections' => [
        'page' => 'Pagina',
        'seo' => 'Zoekmachines en delen',
    ],
    'fields' => [
        'title' => 'Titel',
        'slug' => 'Adres',
        'parent_id' => 'Bovenliggende pagina',
        'menu_title' => 'Titel in menu\'s',
        'meta_title' => 'Titel voor zoekmachines',
        'meta_description' => 'Omschrijving voor zoekmachines',
        'og_image_id' => 'Afbeelding bij delen',
        'canonical_url' => 'Canoniek adres',
        'schema_type' => 'Soort pagina',
        'noindex' => 'Verbergen voor zoekmachines',
        'status' => 'Status',
    ],
    'help' => [
        'slug' => 'Het laatste deel van het adres. Leeg gelaten wordt het gemaakt van de titel.',
        'menu_title' => 'Kortere titel voor menu\'s. Leeg gelaten wordt de titel gebruikt.',
        'seo' => 'Deze velden worden nu opgeslagen en gebruikt zodra de ondersteuning voor zoekmachines aan staat.',
        'noindex' => 'Vraagt zoekmachines deze pagina niet te tonen.',
    ],
    'actions' => [
        'create' => 'Nieuwe pagina',
        'save' => 'Opslaan',
        'edit' => 'Bewerken',
        'publish' => 'Publiceren',
        'unpublish' => 'Offline halen',
        'make_home' => 'Startpagina maken',
        'delete' => 'Pagina verwijderen',
        'delete_confirm' => 'Deze pagina verwijderen? Dit kan niet ongedaan worden gemaakt.',
    ],
    'publication' => [
        'title' => 'Publicatie',
        'requires_permission' => 'Je mag deze pagina bewerken, maar voor publiceren en offline halen heb je het recht "Pagina\'s publiceren" nodig.',
        'delete_requires_publish' => 'Deze pagina is gepubliceerd. Voor verwijderen heb je het recht "Pagina\'s publiceren" nodig.',
        'is_home' => 'Dit is de startpagina.',
        'write_requires_publish' => 'Deze pagina is gepubliceerd. Voor wijzigen heb je het recht "Pagina\'s publiceren" nodig.',
        'move_requires_publish' => 'Voor het verplaatsen van een gepubliceerde pagina heb je het recht "Pagina\'s publiceren" nodig.',
    ],
    'flash' => [
        'created' => 'Pagina ":title" aangemaakt.',
        'saved' => 'Pagina opgeslagen.',
        'deleted' => 'Pagina ":title" verwijderd.',
        'moved' => 'Pagina verplaatst.',
        'published' => 'Pagina gepubliceerd.',
        'unpublished' => 'Pagina offline gehaald.',
        'home' => 'Deze pagina is nu de startpagina.',
    ],
];
