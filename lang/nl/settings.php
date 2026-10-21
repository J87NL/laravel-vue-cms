<?php

declare(strict_types=1);

return [
    'edit' => [
        'title' => 'Website',
        'description' => 'Gegevens die op elke pagina terugkomen: de naam van de site, standaardteksten voor zoekmachines en wie er achter de site zit.',
    ],
    'sections' => [
        'site' => 'Website',
        'entity' => 'Wie zit erachter',
        'findability' => 'Zoekmachines en AI',
    ],
    'fields' => [
        'site_name' => 'Naam van de website',
        'title_pattern' => 'Paginatitel',
        'default_meta_description' => 'Standaardomschrijving',
        'og_image_id' => 'Standaardafbeelding bij delen',
        'privacy_page_id' => 'Privacyverklaring',
        'contact_recipient' => 'Contactformulier naar',
        'entity_type' => 'Soort',
        'entity_name' => 'Naam',
        'entity_job_title' => 'Functie',
        'entity_email' => 'E-mailadres',
        'entity_image_id' => 'Foto of logo',
        'entity_same_as' => 'Profielen elders',
        'robots_extra' => 'Extra regels voor robots.txt',
        'llms_intro' => 'Introductie voor llms.txt',
    ],
    'help' => [
        'title_pattern' => 'Gebruik :title voor de titel van de pagina, bijvoorbeeld ":title | Mijn website". Leeg betekent ":title | naam van de website".',
        'default_meta_description' => 'Voor pagina\'s zonder eigen omschrijving en zonder tekstblok.',
        'og_image_id' => 'Getoond als een pagina wordt gedeeld en zelf geen afbeelding heeft.',
        'privacy_page_id' => 'Formulieren op de site linken naar deze pagina.',
        'contact_recipient' => 'Het adres waar het contactformulier naartoe mailt. De afzender krijg je als antwoordadres.',
        'entity' => 'Zoekmachines tonen deze gegevens bij de website.',
        'entity_job_title' => 'Alleen voor een persoon.',
        'entity_email' => 'Wordt niet op de pagina getoond, alleen in de gegevens voor zoekmachines. Laat leeg als je dat niet wilt.',
        'entity_same_as' => 'Eén volledig adres per regel, bijvoorbeeld je LinkedIn- of GitHub-profiel.',
        'robots_extra' => 'Bijvoorbeeld "User-agent: GPTBot" en "Disallow: /" om een bepaalde crawler buiten te houden.',
        'llms_intro' => 'Een korte omschrijving van de website voor AI-assistenten. Leeg betekent de standaardomschrijving.',
    ],
    'actions' => [
        'save' => 'Opslaan',
    ],
    'flash' => [
        'saved' => 'Instellingen opgeslagen.',
    ],
];
