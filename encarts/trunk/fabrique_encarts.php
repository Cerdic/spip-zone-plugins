<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-10-05 18:19:53
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
    'nom' => 'encarts',
    'slogan' => 'Pour ajouter des encadrés à vos articles SPIP !',
    'description' => 'Permet aux rédacteurs d\'ajouter des encarts (ou encadrés) dans leurs articles, et aux graphistes de les placer n\'importe où dans leurs mises-en-page.',
    'prefixe' => 'encarts',
    'version' => '2.0.0',
    'auteur' => 'Cyril',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'edition',
    'etat' => 'dev',
    'compatibilite' => '[3.0.12-dev;3.0.*]',
    'documentation' => 'http://contrib.spip.net/Plugin-Encarts',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Paramétrage des encarts',
    'fichiers' => 
    array (
      0 => 'pipelines',
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
      'nom' => 'Encarts',
      'nom_singulier' => 'Encart',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_encarts',
      'cle_primaire' => 'id_encart',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'encart',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'tinytext NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Texte',
          'champ' => 'texte',
          'sql' => 'text NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => 'date',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Encarts',
        'titre_objet' => 'Encart',
        'info_aucun_objet' => 'Aucun encart',
        'info_1_objet' => 'Un encart',
        'info_nb_objets' => '@nb@ encarts',
        'icone_creer_objet' => 'Créer un encart',
        'icone_modifier_objet' => 'Modifier cet encart',
        'titre_logo_objet' => 'Logo de cet encart',
        'titre_langue_objet' => 'Langue de cet encart',
        'titre_objets_rubrique' => 'Encarts de la rubrique',
        'info_objets_auteur' => 'Les encarts de cet auteur',
        'retirer_lien_objet' => 'Retirer cet encart',
        'retirer_tous_liens_objets' => 'Retirer tous les encarts',
        'ajouter_lien_objet' => 'Ajouter cet encart',
        'texte_ajouter_objet' => 'Ajouter un encart',
        'texte_creer_associer_objet' => 'Créer et associer un encart',
        'texte_changer_statut_objet' => 'Ce encart est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_articles',
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
          24 => 
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