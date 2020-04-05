<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2014-08-01 15:50:11
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
    'nom' => 'Accès Restreint Partiel',
    'slogan' => 'Monter un peu mais pas tout',
    'description' => 'Ce plugin vient en supplément du plugin Accès Restreint. Ce dernier permet d\'empêcher les accès aux rubriques/articles appartenant à une zone définie comme étant à accès restreint. Les rubriques/articles sont alors totalement invisibles aux visiteurs n\'ayant pas les droits d\'accès.

ARP autorise l\'accès ; les rubriques/articles sortent dans les résultats des boucles. Mais le contenu des #TEXTE est filtré de telle sorte de ne laisser voir qu\'une partie.',
    'prefixe' => 'arp',
    'version' => '1.0.0',
    'auteur' => 'Bruno Caillard',
    'auteur_lien' => 'http://www.bilp.fr/Bruno',
    'licence' => 'GNU/GPL',
    'categorie' => 'edition',
    'etat' => 'dev',
    'compatibilite' => '[3.0.9;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configurer ARP (Accès Restreint Partiel)',
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
    ),
  ),
);

?>