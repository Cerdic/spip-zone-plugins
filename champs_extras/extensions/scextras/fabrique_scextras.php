<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-05-27 12:58:19
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
    'nom' => 'Champs Extras (Synchronisation)',
    'slogan' => 'Synchroniser les champs extras à partir d\'un site de référence',
    'description' => 'Ce plugin permet de mettre à jour la définition des champs extra à partir des définitions prises dans un site de référence choisi.',
    'prefixe' => 'scextras',
    'version' => '1.0.0',
    'auteur' => 'Bruno Caillard',
    'auteur_lien' => 'http://contrib.spip.net/bruno31',
    'licence' => 'GNU/GPL',
    'categorie' => 'maintenance',
    'etat' => 'dev',
    'compatibilite' => '[3.0.5;3.0.*]',
    'documentation' => '',
    'administrations' => '',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration de SYNCHRO-CEXTRAS',
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