<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2017-10-31 10:17:23
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
    'prefixe' => 'panolens',
    'nom' => 'Panolens',
    'slogan' => 'Un panorama d\'images anamorphosées',
    'description' => 'Ce plugin repose sur le plugins Panolens de pchen66 porté pour Spip',
    'version' => '1.0.0',
    'auteur' => 'Charles Stephan',
    'auteur_lien' => 'https://www.lesmoutonssauvages.com',
    'licence' => 'GNU/GPL',
    'categorie' => 'multimedia',
    'etat' => 'test',
    'compatibilite' => '[3.2.0;3.2.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '<lib nom="Three" lien="https://github.com/mrdoob/three.js/archive/r87.zip" />
<lib nom="Panolens" lien="https://github.com/pchen66/panolens.js/archive/v0.9.0.zip" />',
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
      'nom' => 'Panoramas',
      'nom_singulier' => 'Panorama',
      'genre' => 'masculin',
      'logo' => 
      array (
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_panoramas',
      'cle_primaire' => 'id_panorama',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'panorama',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'varchar(255) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '1',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Panoramas',
        'titre_objet' => 'Panorama',
        'info_aucun_objet' => 'Aucun panorama',
        'info_1_objet' => 'Un panorama',
        'info_nb_objets' => '@nb@ panoramas',
        'icone_creer_objet' => 'Créer un panorama',
        'icone_modifier_objet' => 'Modifier ce panorama',
        'titre_logo_objet' => 'Logo de ce panorama',
        'titre_langue_objet' => 'Langue de ce panorama',
        'texte_definir_comme_traduction_objet' => 'Ce panorama est une traduction du panorama numéro :',
        'titre_\\objets_lies_objet' => 'Liés à ce panorama',
        'titre_objets_rubrique' => 'Panoramas de la rubrique',
        'info_objets_auteur' => 'Les panoramas de cet auteur',
        'retirer_lien_objet' => 'Retirer ce panorama',
        'retirer_tous_liens_objets' => 'Retirer tous les panoramas',
        'ajouter_lien_objet' => 'Ajouter ce panorama',
        'texte_ajouter_objet' => 'Ajouter un panorama',
        'texte_creer_associer_objet' => 'Créer et associer un panorama',
        'texte_changer_statut_objet' => 'Ce panorama est :',
        'supprimer_objet' => 'Supprimer ce panorama',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de ce panorama ?',
      ),
      'liaison_directe' => '',
      'table_liens' => '',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'fichiers' => 
      array (
        'echafaudages' => 
        array (
          0 => 'prive/objets/infos/objet.html',
          1 => 'prive/squelettes/contenu/objet.html',
        ),
        'explicites' => 
        array (
          0 => 'action/supprimer_objet.php',
        ),
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
