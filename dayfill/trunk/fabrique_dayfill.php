<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-04-03 12:35:39
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
    'nom' => 'DayFill',
    'slogan' => 'Gestion d\'activités liées à des projets',
    'description' => 'DayFill (ou Défile en français...) permet de saisir des activités (anciennement des actions) dans le cadre d\'un projet.',
    'prefixe' => 'dayfill',
    'version' => '1.0.0',
    'auteur' => 'Cyril Marion',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.7;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '2.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuer le gestionnaire d\'activités',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'pipelines',
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
      'nom' => 'Activités',
      'nom_singulier' => 'Activité',
      'genre' => 'feminin',
      'logo_variantes' => 'on',
      'table' => 'spip_projets_activites',
      'cle_primaire' => 'id_projets_activite',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'projets_activite',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id projet',
          'champ' => 'id_projet',
          'sql' => 'int(11) DEFAULT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Id facture',
          'champ' => 'id_facture',
          'sql' => 'int(11) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Facture dans le cadre de laquelle l\'activité a été effectuée.',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'Descriptif',
          'champ' => 'descriptif',
          'sql' => 'varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => '',
          'explication' => 'Décrivez l\'activité',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Auteur',
          'champ' => 'id_auteur',
          'sql' => 'int(11) NOT NULL DEFAULT \'0\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Indiquez l\'auteur de l\'activité',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Heure debut',
          'champ' => 'date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Indiquez le moment où l\'activité a démarré',
          'saisie_options' => '',
        ),
        9 => 
        array (
          'nom' => 'Heure fin',
          'champ' => 'date_fin',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Indiquez le moment où l\'activité a pris fin',
          'saisie_options' => '',
        ),
        10 => 
        array (
          'nom' => 'Nb heures passees',
          'champ' => 'nb_heures_passees',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Indiquez la durée brute de l\'activité, en heures.',
          'saisie_options' => '',
        ),
        11 => 
        array (
          'nom' => 'Nb heures decomptees',
          'champ' => 'nb_heures_decomptees',
          'sql' => 'decimal(18,2) DEFAULT NULL',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => 'Indiquez la durée de l\'activité qui sera réellement prise en compte, en heures.',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'descriptif',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Activités',
        'titre_objet' => 'Activité',
        'info_aucun_objet' => 'Aucune activité',
        'info_1_objet' => 'Une activité',
        'info_nb_objets' => '@nb@ activités',
        'icone_creer_objet' => 'Créer une activité',
        'icone_modifier_objet' => 'Modifier cette activité',
        'titre_logo_objet' => 'Logo de cette activité',
        'titre_langue_objet' => 'Langue de cette activité',
        'titre_objets_rubrique' => 'Activités de la rubrique',
        'info_objets_auteur' => 'Les activités de cet auteur',
        'retirer_lien_objet' => 'Retirer cette activité',
        'retirer_tous_liens_objets' => 'Retirer toutes les activités',
        'ajouter_lien_objet' => 'Ajouter cette activité',
        'texte_ajouter_objet' => 'Ajouter une activité',
        'texte_creer_associer_objet' => 'Créer et associer une activité',
        'texte_changer_statut_objet' => 'Cette activité est :',
      ),
      'table_liens' => '',
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
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
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
    ),
  ),
);

?>