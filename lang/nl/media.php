<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Media',
        'description' => 'Afbeeldingen voor de website. Een afbeelding kan pas weg als geen enkele pagina hem gebruikt.',
    ],
    'create' => [
        'title' => 'Afbeelding uploaden',
        'description' => 'JPEG, PNG, WebP of GIF. Kleinere WebP-versies worden op de achtergrond gemaakt.',
    ],
    'edit' => [
        'title' => 'Afbeelding bewerken',
    ],
    'sections' => [
        'focal_point' => 'Focuspunt',
        'replace' => 'Bestand vervangen',
    ],
    'fields' => [
        'file' => 'Afbeelding',
        'preview' => 'Voorbeeld',
        'alt' => 'Alternatieve tekst',
        'caption' => 'Onderschrift',
        'focal_x' => 'Horizontaal',
        'focal_y' => 'Verticaal',
        'usage' => 'Gebruikt',
    ],
    'help' => [
        'alt' => 'Beschrijf wat er op de afbeelding staat, voor bezoekers die hem niet kunnen zien.',
        'focal_point' => 'Het deel van de afbeelding dat zichtbaar moet blijven als hij wordt bijgesneden, als percentage vanaf links en vanaf boven.',
        'replace' => 'Kies een nieuwe afbeelding. Alternatieve tekst, onderschrift en focuspunt blijven, en elke pagina die deze afbeelding gebruikt toont meteen het nieuwe bestand.',
    ],
    'actions' => [
        'upload' => 'Uploaden',
        'save' => 'Opslaan',
        'edit' => 'Bewerken',
        'delete' => 'Verwijderen',
        'delete_confirm' => 'Deze afbeelding verwijderen?',
        'replace' => 'Bestand vervangen',
        'replace_confirm' => 'Het huidige bestand vervangen? Het oude bestand wordt verwijderd.',
    ],
    'flash' => [
        'uploaded' => 'Afbeelding geüpload.',
        'saved' => 'Afbeelding opgeslagen.',
        'deleted' => 'Afbeelding verwijderd.',
        'replaced' => 'Bestand vervangen.',
    ],
];
