<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2019-07-12 10:07:37
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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$data = array (
  'fabrique' => 
  array (
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'svgbase',
    'nom' => 'SVG en base de données',
    'slogan' => '',
    'description' => '',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '0.1.0',
    'auteur' => 'chankalan',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'multimedia',
    'etat' => 'dev',
    'compatibilite' => '[3.2.4;3.2.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '0.1.0',
    'formulaire_config' => 'on',
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
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'SVGs',
      'nom_singulier' => 'SVG',
      'genre' => 'masculin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => '',
      'table' => 'spip_svg',
      'cle_primaire' => 'id_svg',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'svg',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'varchar(55) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
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
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'SVG',
          'champ' => 'svg',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'textarea',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'SVGs',
        'titre_objet' => 'SVG',
        'info_aucun_objet' => 'Aucun SVG',
        'info_1_objet' => 'Un SVG',
        'info_nb_objets' => '@nb@ SVGs',
        'icone_creer_objet' => 'Créer un SVG',
        'icone_modifier_objet' => 'Modifier ce SVG',
        'titre_logo_objet' => 'Logo de ce SVG',
        'titre_langue_objet' => 'Langue de ce SVG',
        'texte_definir_comme_traduction_objet' => 'Ce SVG est une traduction du svg numéro :',
        'titre_\\objets_lies_objet' => 'Liés à ce SVG',
        'titre_objets_rubrique' => 'SVGs de la rubrique',
        'info_objets_auteur' => 'Les SVGs de cet auteur',
        'retirer_lien_objet' => 'Retirer ce SVG',
        'retirer_tous_liens_objets' => 'Retirer tous les SVGs',
        'ajouter_lien_objet' => 'Ajouter ce SVG',
        'texte_ajouter_objet' => 'Ajouter un SVG',
        'texte_creer_associer_objet' => 'Créer et associer un SVG',
        'texte_changer_statut_objet' => 'Ce SVG est :',
        'supprimer_objet' => 'Supprimer cet SVG',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cet SVG ?',
      ),
      'liaison_directe' => '',
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_articles',
        1 => 'spip_auteurs',
        2 => 'spip_groupes_mots',
        3 => 'spip_mots',
        4 => 'spip_rubriques',
        5 => 'spip_syndic',
      ),
      'afficher_liens' => 'on',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'fichiers' => 
      array (
        'explicites' => 
        array (
          0 => 'action/supprimer_objet.php',
        ),
      ),
      'saisies' => 
      array (
        0 => 'objets',
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
      ),
    ),
  ),
);
