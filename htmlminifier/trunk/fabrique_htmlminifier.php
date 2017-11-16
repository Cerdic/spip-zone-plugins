<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2017-11-13 13:49:18
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
    'prefixe' => 'htmlminifier',
    'nom' => 'HTML Minifier',
    'slogan' => '',
    'description' => 'Minifier les pages HTML servies par SPIP.',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '1.0.0',
    'auteur' => 'ladnet',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'performance',
    'etat' => 'dev',
    'compatibilite' => '[3.1.0;3.2.*]',
    'documentation' => '',
    'administrations' => '',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'fichiers' => 
    array (
      0 => 'fonctions',
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
    'exemples' => '',
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
