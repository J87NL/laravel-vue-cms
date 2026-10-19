<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Doorverwijzingen',
        'description' => 'Oude adressen die naar een nieuw adres doorsturen. Als je een pagina hernoemt of verplaatst, komt hier vanzelf een doorverwijzing bij.',
    ],
    'create' => [
        'title' => 'Doorverwijzing toevoegen',
    ],
    'edit' => [
        'title' => 'Doorverwijzing bewerken',
    ],
    'import' => [
        'title' => 'CSV importeren',
        'description' => 'Eén doorverwijzing per regel: oud adres, nieuw adres en eventueel de statuscode (301 of 302). Een bestaande doorverwijzing vanaf hetzelfde oude adres wordt bijgewerkt. Bevat een regel een fout, dan wordt niets geïmporteerd.',
        'failed_lines' => 'Er is niets geïmporteerd: :count regels bevatten een fout. Pas het bestand aan en importeer het opnieuw. :lines',
    ],
    'fields' => [
        'from_path' => 'Oud adres',
        'to' => 'Nieuw adres',
        'status_code' => 'Soort',
        'source' => 'Bron',
        'hits' => 'Bezocht',
        'last_hit_at' => 'Laatst bezocht',
        'file' => 'CSV-bestand',
    ],
    'help' => [
        'from_path' => 'Bijvoorbeeld /oude-pagina. Een volledig adres op deze website mag ook.',
        'to' => 'Een pad zoals /nieuwe-pagina, of een volledig adres dat begint met https://.',
        'file' => 'Komma of puntkomma als scheidingsteken. Een kopregel met "from" of "van" wordt overgeslagen.',
    ],
    'status_codes' => [
        '301' => '301: blijvend verplaatst',
        '302' => '302: tijdelijk verplaatst',
    ],
    'actions' => [
        'save' => 'Opslaan',
        'import' => 'Importeren',
        'edit' => 'Bewerken',
        'delete' => 'Verwijderen',
        'delete_confirm' => 'Deze doorverwijzing verwijderen?',
    ],
    'flash' => [
        'saved' => 'Doorverwijzing opgeslagen.',
        'deleted' => 'Doorverwijzing verwijderd.',
        'imported' => '{0} Geen doorverwijzingen geïmporteerd.|{1} 1 doorverwijzing geïmporteerd.|[2,*] :count doorverwijzingen geïmporteerd.',
    ],
];
