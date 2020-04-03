<?php

return [
    'menu' => [
        'title' => 'Contenu',
        'wakapdfs' => 'Pdfs',
        'wakapdfs_description' => 'Gestion des modèles pdfs',
        'settings_category' => 'Wakaari Modèle',
    ],
    'wakapdf' => [
        'name' => 'Nom',
        'path' => 'Fichier source',
        'analyze' => "Log d'analyse des codes du fichier source",
        'has_sectors_perso' => 'Personaliser le contenu en fonction du secteur',
        'data_source' => ' Sources des données',
        'data_source_placeholder' => 'Choisissez une source de données',
        'show' => 'Voir un exemple',
        'check' => 'Vérifier',
        'scopes' => [
            'title' => "limiter le pdf pour une cible",
            'prompt' => 'Ajouter une nouvelle limites',
            'com' => "Vous pouvez décider de n'afficher ce modèle que sous certains critères Attention seul les valeurs id sont accepté",
            'self' => "Fonction de restriction liée à l'id de ce modèle ?",
            'target' => 'Relation de la cible',
            'target_com' => "Ecrire le nom de la relation les relations parentes ne sont pas disponible",
            'id' => 'ID recherché',
            'id_com' => "Vous pouvez ajouter plusieurs ID",
            'conditions' => "Conditions",
        ],
        'subject' => "Sujet de l'email",
        'slug' => "Slug ou code",
        'addFunction' => 'Ajouter une fonction/collection',
        'test' => "Tester",
        'show' => "Voir",
    ],
    "button" => [
        'exemple_download' => "Télecharger un exemple",
        'exemple_inline' => "Voir un éxemple",

    ],
];