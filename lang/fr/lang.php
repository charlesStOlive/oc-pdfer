<?php

return [
    'menu' => [
        'title' => 'Contenu',
        'wakapdfs' => 'Pdfs',
        'wakapdfs_description' => 'Gestion des modèles pdfs',
        'settings_category' => 'Wakaari Modèle',
        'pdflayouts' => 'Css des PDF',
        'pdflayouts_description' => 'Gestion des feuilles de styles pour les PDF',
    ],
    'pdflayout' => [
        'name' => "Nom du template",
        'wconfig_layout' => "Nom de la bibliothèque CSS",
        'add_css' => "Ajouter vos propres CSS",
        'css' => "CSS",
    ],
    'wakapdf' => [
        'tab_info' => "Info",
        'tab_edit' => "Edit",
        'tab_scopes' => "Limites",
        'tab_fnc' => "Images et fonctions",
        'pdf_layout' => 'Lyout et CSS',
        'name' => 'Nom',
        'pdf_layout' => "Modèle",
        'template' => "Template HTML compatible bootstrap 3.4.1",
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
        'exemple_download' => "Télecharger un ex.",
        'exemple_inline' => "ex. en ligne",
        'exemple_html' => "Voir HTML",
        'loading_indicator' => "Création du PDF en cours"

    ],
    "errors" => [
        "modelId" => "Le modèle source de la fuison n'est pas determiné",
        "wakaPdfId" => "Il manque le modèle PDF",
    ],
];
