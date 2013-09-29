<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-09-29 23:38:01
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
    'nom' => 'Projets - Sites internet',
    'slogan' => 'Ayez sous la main vos infos de sites en production, récette et développement',
    'description' => 'Ce plugin va vous permettre d\'ajouter à vos projets les infos sur vos sites en production, en recette et en développement.',
    'prefixe' => 'projets_sites',
    'version' => '0.5.0',
    'auteur' => 'Teddy Payet',
    'auteur_lien' => 'http://www.teddypayet.com/',
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
      1 => 'fonctions',
      2 => 'options',
      3 => 'pipelines',
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
    'exemples' => '',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Sites pour projet',
      'nom_singulier' => 'Site pour projet',
      'genre' => 'masculin',
      'logo_variantes' => 'on',
      'table' => 'spip_projets_sites',
      'cle_primaire' => 'id_site',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'projet_site',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Nom du logiciel',
          'champ' => 'logiciel_nom',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '8',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Version du logiciel',
          'champ' => 'logiciel_version',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '3',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Type de site',
          'champ' => 'type_site',
          'sql' => 'varchar(4) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '3',
          'saisie' => 'selection',
          'explication' => '',
          'saisie_options' => 'datas=[(#ARRAY{prod,projet_site:type_prod,rec,projet_site:type_recette,dev,projet_site:type_dev})]',
        ),
        3 => 
        array (
          'nom' => 'Date de création',
          'champ' => 'date_creation',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Front office',
          'champ' => 'fo_fieldset',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'fieldset',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Url Front Office',
          'champ' => 'fo_url',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '5',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Identifiant du Front Office',
          'champ' => 'fo_login',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Mot de passe du Front Office',
          'champ' => 'fo_password',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Back office',
          'champ' => 'bo_fieldset',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'fieldset',
          'explication' => '',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Url du Back Office',
          'champ' => 'bo_url',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'Identifiant du Back Office',
          'champ' => 'bo_login',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        11 => 
        array (
          'nom' => 'Mot de passe du Back Office',
          'champ' => 'bo_password',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        12 => 
        array (
          'nom' => 'Applicatif',
          'champ' => 'applicatif_fieldset',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'fieldset',
          'explication' => '',
          'saisie_options' => '',
        ),
        13 => 
        array (
          'nom' => 'Serveur appplicatif',
          'champ' => 'applicatif_serveur',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '6',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        14 => 
        array (
          'nom' => 'Chemin de l\'applicatif',
          'champ' => 'applicatif_path',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        15 => 
        array (
          'nom' => 'Surveillance de l\'applicatif',
          'champ' => 'applicatif_surveillance',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        16 => 
        array (
          'nom' => 'Chemin SVN',
          'champ' => 'svn_path',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        17 => 
        array (
          'nom' => 'Trac SVN',
          'champ' => 'svn_trac',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        18 => 
        array (
          'nom' => 'SAS DPI',
          'champ' => 'sas_dpi',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        19 => 
        array (
          'nom' => 'Information SGBD',
          'champ' => 'sgbd_fieldset',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'fieldset',
          'explication' => '',
          'saisie_options' => '',
        ),
        20 => 
        array (
          'nom' => 'Type de SGBD',
          'champ' => 'sgbd_type',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Exemples: MySQL, Oracle, Microsoft SQL Server, MongoDB, etc.',
          'saisie_options' => '',
        ),
        21 => 
        array (
          'nom' => 'Serveur SGBD',
          'champ' => 'sgbd_serveur',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        22 => 
        array (
          'nom' => 'Nom de la SGBD',
          'champ' => 'sgbd_nom',
          'sql' => 'varchar(50) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        23 => 
        array (
          'nom' => 'Identifiant de la SGBD',
          'champ' => 'sgbd_login',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        24 => 
        array (
          'nom' => 'Mot de passe de la SGBD',
          'champ' => 'sgbd_password',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        25 => 
        array (
          'nom' => 'Autres',
          'champ' => 'autres_fieldset',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'recherche' => '',
          'saisie' => 'fieldset',
          'explication' => '',
          'saisie_options' => '',
        ),
        26 => 
        array (
          'nom' => 'SSO',
          'champ' => 'sso',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => 'Méthode d\'authentification unique',
          'saisie_options' => '',
        ),
        27 => 
        array (
          'nom' => 'Périmètre d\'accès',
          'champ' => 'perimetre_acces',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=6',
        ),
        28 => 
        array (
          'nom' => 'Outils de statistiques',
          'champ' => 'statistiques',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=6',
        ),
        29 => 
        array (
          'nom' => 'Moteur de recherche',
          'champ' => 'moteur_recherche',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Exemples : Moteur interne au logiciel, Exalead, Google, etc.',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=6',
        ),
        30 => 
        array (
          'nom' => 'Autres outils',
          'champ' => 'autres_outils',
          'sql' => 'mediumtext NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=6',
        ),
        31 => 
        array (
          'nom' => 'Remarques',
          'champ' => 'remarques',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => 'Caractéristiques particulières, fontionnalités, etc.',
          'saisie_options' => 'li_class=haut, class=inserer_barre_edition, rows=10',
        ),
      ),
      'champ_titre' => 'type_site',
      'champ_date' => 'date_creation',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Sites pour projets',
        'titre_objet' => 'Site pour projets',
        'info_aucun_objet' => 'Aucun site',
        'info_1_objet' => 'Un site',
        'info_nb_objets' => '@nb@ sites',
        'icone_creer_objet' => 'Créer un site',
        'icone_modifier_objet' => 'Modifier ce site',
        'titre_logo_objet' => 'Logo de ce site',
        'titre_langue_objet' => 'Langue de ce site',
        'titre_objets_rubrique' => 'Sites de la rubrique',
        'info_objets_auteur' => 'Les sites de cet auteur',
        'retirer_lien_objet' => 'Retirer ce site',
        'retirer_tous_liens_objets' => 'Retirer tous les sites',
        'ajouter_lien_objet' => 'Ajouter ce site',
        'texte_ajouter_objet' => 'Ajouter un site',
        'texte_creer_associer_objet' => 'Créer et associer un site',
        'texte_changer_statut_objet' => 'Ce site est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_projets',
      ),
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur_restreint',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur_restreint',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur_restreint',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
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
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAN5JREFUeNrs2k0KwjAQBtCJiDtXrsWjeCDBwwhewJv0KOK6q+7c6AVMoaE0wb5ZBvrDI0P4mKRYqO7d6RMN1iZWXgAAAAAAAAAAAAAArLS2JQ8Nt5gcbB6Z9cv5mWqGMS0AAAAAAAAAAAAAAMBKw9BrdyiY2PQ/V/fXSPkwEiZDAAAAANDcKVD7B2rfG9ACAAAAcAosUXMOQHKDmbEsMhng+O6zLxuizWCjBQAAAACg5jFYcm/ADgAAAACAipVyg5GxLDBn1f6+FgAAAAAAYWjC8WQHAAAAAMAf1VeAAQD+vidWsfBiyAAAAABJRU5ErkJggg==',
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
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAN5JREFUeNrs2k0KwjAQBtCJiDtXrsWjeCDBwwhewJv0KOK6q+7c6AVMoaE0wb5ZBvrDI0P4mKRYqO7d6RMN1iZWXgAAAAAAAAAAAAAArLS2JQ8Nt5gcbB6Z9cv5mWqGMS0AAAAAAAAAAAAAAMBKw9BrdyiY2PQ/V/fXSPkwEiZDAAAAANDcKVD7B2rfG9ACAAAAcAosUXMOQHKDmbEsMhng+O6zLxuizWCjBQAAAACg5jFYcm/ADgAAAACAipVyg5GxLDBn1f6+FgAAAAAAYWjC8WQHAAAAAMAf1VeAAQD+vidWsfBiyAAAAABJRU5ErkJggg==',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAJNJREFUeNpiZCADTD0o/5+BSoCJYYDBqANGHTDgDmDBJ/l5EgPW7LYIiZ1t/5CRkmw8GgWjDhj4XPCYTRhPxfIWzuLNY2BEpGKG0cqIPgURPdoGo1GAEgWyv96ilOufGbCndmLLf+S6BDkXjUYBWbkAV9U8WhBRChiR6wL0XEAMoFT/aBQMrmyIv20wGgW0AQABBgDcQicU1reYgwAAAABJRU5ErkJggg==',
          ),
          24 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAH9JREFUeNpiZCASTD0o/5+BDMDEQGMwagFBwIJN8PMkBowIXQSls+0fMpKSKEbjYBBE8mM2YSw59C2Y5M1jYEREGsP/oZNMqVkmDR4f4MtgsIyJHGcYFsj+eguX/MxAXoQOviDCViYNOh8wgDIa9sxGHfXDqMIhxduDygcAAQYAykMm8a/J408AAAAASUVORK5CYII=',
          ),
          16 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAG9JREFUeNpiYcABph6U/89ABGBioBBQbAALMufzJAa4sxcBcbb9Q0Z8mkHeHAReeMwmjBTabxl48xgYIc5jGEqxIPvrLdjZnxlQnU1UYkIOA+RoxAXQ1VA3IRHrCpxeIEcNxV5gJMYFyDGFDgACDACXrSdhsZk91gAAAABJRU5ErkJggg==',
          ),
          12 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAGBJREFUeNpiZEADUw/K/2fAA5gYSAQsMMbnSQxgkxcBcbb9Q0ZsikG2k2wD02M24f8gDOLw5jEwEtRAsg0gQvbXW0aSPU100MLcDwslbABZjgWXBEEbiJVjIaQJHQAEGABs3yIZrQXNWgAAAABJRU5ErkJggg==',
          ),
        ),
      ),
    ),
  ),
);

?>