<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-09-19 18:21:30
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'projets',
    'slogan' => 'Gerer des projets avec SPIP',
    'description' => 'Crée une base de données de projets, permettant de définir les objectifs, les enjeux, ... d\'un projet.

Ce plugin est un élément autonome de SPIPMINE.',
    'prefixe' => 'projets',
    'version' => '1.0.0',
    'auteur' => 'Cyril Marion',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'options',
      2 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Projets',
      'nom_singulier' => 'Projet',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_projets',
      'cle_primaire' => 'id_projet',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'projet',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Projet parent',
          'champ' => 'id_parent',
          'sql' => 'int(11) DEFAULT 0 NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'projets',
          'explication' => 'Sentez-vous libre de sélectionner un projet parent...',
          'saisie_options' => 'recursif=oui,id_projet=#ID_PROJET',
        ),
        1 => 
        array (
          'nom' => 'Nom du projet',
          'champ' => 'nom',
          'sql' => 'varchar(75) DEFAULT \'\' NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '7',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Voir en ligne',
          'champ' => 'url_site',
          'sql' => 'varchar(255) DEFAULT \'\' NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '2',
          'saisie' => 'input',
          'explication' => 'Vous pouvez indiquer une url permettant de voir le projet en ligne',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Cadre du projet',
          'champ' => 'id_projets_cadre',
          'sql' => 'int(11) DEFAULT 0 NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'projets_cadres',
          'explication' => 'Vous pouvez préciser le cadre du projet en le sélectionnant dans cette liste',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Catégorie du projet',
          'champ' => 'id_projets_categorie',
          'sql' => 'int(11) DEFAULT 0 NOT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '2',
          'saisie' => 'projets_categories',
          'explication' => 'Vous pouvez péciser la catégorie du projet (en général le type de prestation réalisée dans le cadre de ce projet)',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Date de démarrage',
          'champ' => 'date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'Date de démarrage du projet',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Date livraison prévue',
          'champ' => 'date_livraison_prevue',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'Date à laquelle le projet doit être livré',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Date livraison effective',
          'champ' => 'date_livraison',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'Date de livraison effective du projet',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'Nb heures estimees',
          'champ' => 'nb_heures_estimees',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        11 => 
        array (
          'nom' => 'Nombre d\'heures réel',
          'champ' => 'nb_heures_reel',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Nombre d\'heures réellement passées sur le projet',
          'saisie_options' => '',
        ),
        12 => 
        array (
          'nom' => 'Actif',
          'champ' => 'actif',
          'sql' => 'varchar(3) NOT NULL DEFAULT \'on\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'oui_non',
          'explication' => 'Super-statut du projet',
          'saisie_options' => '',
        ),
        14 => 
        array (
          'nom' => 'Objectif',
          'champ' => 'objectif',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '3',
          'saisie' => 'textarea',
          'explication' => 'Les objectifs du projet, en quelques mots...',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
        15 => 
        array (
          'nom' => 'Enjeux',
          'champ' => 'enjeux',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '2',
          'saisie' => 'textarea',
          'explication' => 'Décrire les enjeux, s\'il y en a',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
        16 => 
        array (
          'nom' => 'Méthode',
          'champ' => 'methode',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '1',
          'saisie' => 'textarea',
          'explication' => 'Quelques mots sur la méthode recommandée pour réaliser le projet',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
        17 => 
        array (
          'nom' => 'Descriptif du projet',
          'champ' => 'descriptif',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '2',
          'saisie' => 'textarea',
          'explication' => 'Quelques explications complémentaires, commentaires...',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
      ),
      'champ_titre' => 'nom',
      'champ_date' => 'date_publication',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Projets',
        'titre_objet' => 'Projet',
        'info_aucun_objet' => 'Aucun projet',
        'info_1_objet' => 'Un projet',
        'info_nb_objets' => '@nb@ projets',
        'icone_creer_objet' => 'Créer un projet',
        'icone_modifier_objet' => 'Modifier ce projet',
        'titre_logo_objet' => 'Logo de ce projet',
        'titre_langue_objet' => 'Langue de ce projet',
        'titre_objets_rubrique' => 'Projets de la rubrique',
        'info_objets_auteur' => 'Les projets de cet auteur',
        'retirer_lien_objet' => 'Retirer ce projet',
        'retirer_tous_liens_objets' => 'Retirer tous les projets',
        'ajouter_lien_objet' => 'Ajouter ce projet',
        'texte_ajouter_objet' => 'Ajouter un projet',
        'texte_creer_associer_objet' => 'Créer et associer un projet',
        'texte_changer_statut_objet' => 'Ce projet est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_organisations',
      ),
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
    1 => 
    array (
      'nom' => 'Cadres de projet',
      'nom_singulier' => 'Cadre de projet',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_projets_cadres',
      'cle_primaire' => 'id_projets_cadre',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'projets_cadre',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '2',
          'saisie' => 'input',
          'explication' => 'Donnez le nom de ce cadre de projet',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Vous pouvez décrire plus précisément ce cadre de projet',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Cadres de projet',
        'titre_objet' => 'Cadre de projet',
        'info_aucun_objet' => 'Aucun cadre de projet',
        'info_1_objet' => 'Un cadre de projet',
        'info_nb_objets' => '@nb@ cadres de projet',
        'icone_creer_objet' => 'Créer un cadre de projet',
        'icone_modifier_objet' => 'Modifier ce cadre de projet',
        'titre_logo_objet' => 'Logo de ce cadre de projet',
        'titre_langue_objet' => 'Langue de ce cadre de projet',
        'titre_objets_rubrique' => 'Cadres de projet de la rubrique',
        'info_objets_auteur' => 'Les cadres de projet de cet auteur',
        'retirer_lien_objet' => 'Retirer ce cadre de projet',
        'retirer_tous_liens_objets' => 'Retirer tous les cadres de projet',
        'ajouter_lien_objet' => 'Ajouter ce cadre de projet',
        'texte_ajouter_objet' => 'Ajouter un cadre de projet',
        'texte_creer_associer_objet' => 'Créer et associer un cadre de projet',
        'texte_changer_statut_objet' => 'Ce cadre de projet est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
    2 => 
    array (
      'nom' => 'Catégories de projets',
      'nom_singulier' => 'Catégorie de projet',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_projets_categories',
      'cle_primaire' => 'id_projets_categorie',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'projets_categorie',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre de la catégorie',
          'champ' => 'titre',
          'sql' => 'tinytext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '2',
          'saisie' => 'input',
          'explication' => 'Donner un titre à cette catégorie de projet',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Descriptif de la catégorie de projet',
          'champ' => 'descriptif',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '3',
          'saisie' => 'textarea',
          'explication' => 'Description plus précise de la catégorie de projet',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=4',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Catégories de projets',
        'titre_objet' => 'Catégorie de projet',
        'info_aucun_objet' => 'Aucune catégorie de projet',
        'info_1_objet' => 'Une catégorie de projet',
        'info_nb_objets' => '@nb@ catégories de projets',
        'icone_creer_objet' => 'Créer une catégorie de projet',
        'icone_modifier_objet' => 'Modifier cette catégorie de projet',
        'titre_logo_objet' => 'Logo de cette catégorie de projet',
        'titre_langue_objet' => 'Langue de cette catégorie de projet',
        'titre_objets_rubrique' => 'Catégories de projets de la rubrique',
        'info_objets_auteur' => 'Les catégories de projets de cet auteur',
        'retirer_lien_objet' => 'Retirer cette catégorie de projet',
        'retirer_tous_liens_objets' => 'Retirer toutes les catégories de projets',
        'ajouter_lien_objet' => 'Ajouter cette catégorie de projet',
        'texte_ajouter_objet' => 'Ajouter une catégorie de projet',
        'texte_creer_associer_objet' => 'Créer et associer une catégorie de projet',
        'texte_changer_statut_objet' => 'Cette catégorie de projet est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => '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',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
        ),
      ),
      1 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => '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',
          ),
        ),
      ),
      2 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'jpg',
            'contenu' => '/9j/4AAQSkZJRgABAQEAZABkAAD/2wBDAAoHBwkHBgoJCAkLCwoMDxkQDw4ODx4WFxIZJCAmJSMgIyIoLTkwKCo2KyIjMkQyNjs9QEBAJjBGS0U+Sjk/QD3/2wBDAQsLCw8NDx0QEB09KSMpPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT3/wAARCAAwADADASIAAhEBAxEB/8QAGQABAAMBAQAAAAAAAAAAAAAABwIEBQYD/8QAMBAAAgICAQMCBAUDBQAAAAAAAQIDBAURAAYSITFBBxMUUSIjYXGBUpHBFTJCYqH/xAAVAQEBAAAAAAAAAAAAAAAAAAAAAf/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AGbhr8ScqasGQk22gK9EEHyFkLSTa/dEUcSTwc+KFmGQmKWQxxWMqSzqvcQsUKISBvzpmb+3AsU8ma3xCmLyZJ7RmD4+GqO6CzTKHsQDYVR6Hu9Bo8qjMQSdX3JhayM9VIbUmXW2pSMRkERxCMnwQSFB9z6cqQ0XWeHGPWy02KWcDE5hKzJLXZj41/UhPt4+454NjlVRTnxmYqdNQuzWrYrky2ZVBAeQ+yg+gGwP5JBXcfDPKTXadV52LSWaf5hJ8s8L/L7j+pRowT/14gcJfhZbRYMcqsSi2bdYFtA/jSOVd/uI2/txZ4RmZrLjHxCKDse7KpMaOdKoHrI59kX1J/geTwVnvw9SfEDD1KyyXMfVnSMEr5nHf3yykewY7Y/oOLPUPSzZRrTwvC6W40jsVrAbskCna6ZSGUjf6j9OcBfq4vpuqtCaMYiO3PPWuWarNYkdURWVQ76KqS/kAeda998COBy08PV8817I5A5tL0scmPAd0sxaIREA/Cuj52dADR5GbICx8RKcmOv35slPfP1MUqvHHXraG4WVvXtAbZHjlH5NHJdO4G1PkHpZtzKlW+/4FcROAiykf7TojTeda0SRyEtLF47pS08N+WeaXIR08jkI17u5GUu6R7OyuwNn/lr7eOB4YXKRYjqjJ46bePhmtl67yLr6SZHJiZh/TolW/RuN+HyyZOBg6fJtw6WxXJ2Y2/yp9Qw8EcKsTUxnU7zRSVo8v8m6tKnasu8EjRGN3HzGTZfXZoEjej7cSMD062MljnnljLxV/poYYEKxQx7B0Nksx2PUn9gPPA3T6cIOubGOrZOu+Yqy2qgyNvujjftO/lxAf+ni+fThZ1dUlGZEv+jnLpUvSyS1VDE9k0Sdj6HqNow+2xwOQNqOpguj5/oFuIti0RVkJbv/ADV0vj1P8a37e3JQSwHoK/LYqdkAzsTSVomKdq9h2gJ2R9uX8hicrjK3R9Sm0VbJiex2hZR2wOzq3YzE+oBGx6n088lSo5DIdK5mG1Gl/IR5lZpYDLs2SibdVKnydbOh7DgaPQElKfIyTYyB69N80hiidu4qv083v/j2+54vD04V9DozZVZlxjY6K7lGsQVSCOyOOB1ZtH27nUfbZ0OKnA//2Q==',
          ),
        ),
      ),
    ),
  ),
);

?>