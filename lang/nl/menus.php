<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Menu\'s',
        'description' => 'Items die naar een pagina wijzen volgen het adres van die pagina en zijn alleen zichtbaar zolang de pagina gepubliceerd is.',
    ],
    'create' => [
        'title' => 'Nieuw menu-item',
    ],
    'edit' => [
        'title' => 'Menu-item bewerken',
    ],
    'fields' => [
        'link_type' => 'Verwijst naar',
        'page_id' => 'Pagina',
        'url' => 'Link',
        'label' => 'Tekst',
        'parent_id' => 'Onder',
        'link' => 'Link',
        'status' => 'Status',
    ],
    'link_types' => [
        'page' => 'Een pagina',
        'url' => 'Een ander adres',
    ],
    'help' => [
        'url' => 'Een pad zoals /contact, of een adres dat begint met https://, mailto: of tel:.',
        'label' => 'Leeg gelaten bij een pagina wordt de titel in menu\'s gebruikt.',
    ],
    'statuses' => [
        'visible' => 'Zichtbaar',
        'hidden' => 'Verborgen (de pagina of een pagina erboven is niet gepubliceerd)',
    ],
    'actions' => [
        'create' => 'Item toevoegen',
        'save' => 'Opslaan',
        'edit' => 'Bewerken',
        'delete' => 'Verwijderen',
        'delete_confirm' => 'Dit menu-item en de items eronder verwijderen?',
    ],
    'flash' => [
        'saved' => 'Menu-item opgeslagen.',
        'deleted' => 'Menu-item verwijderd.',
    ],
];
