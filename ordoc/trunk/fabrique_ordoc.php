<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2017-01-07 19:21:19
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
    'prefixe' => 'ordoc',
    'nom' => 'Ordre des documents',
    'slogan' => 'Permet d\'ordonner les documents liés à un objet par glisser / déposer.',
    'description' => '',
    'version' => '1.0.0',
    'auteur' => 'Matthieu Marcillaud',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'edition',
    'etat' => 'dev',
    'compatibilite' => '[3.1.0;3.2.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
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
