<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2014-07-30 10:20:05
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
    'nom' => 'Double Click',
    'slogan' => 'Protection des formulaires contre les cliqueurs frénétiques',
    'description' => 'Protège les formulaires contre la double (ou plus) validation en cas de délais de réponse du serveur trop longs pour le client pressé.',
    'prefixe' => 'doubleclick',
    'version' => '1.0.0',
    'auteur' => 'Camille Sauvage',
    'auteur_lien' => 'http://www.espci.fr',
    'licence' => 'GNU/GPL',
    'categorie' => 'performance',
    'etat' => 'dev',
    'compatibilite' => '[3.0.14;3.0.*]',
    'documentation' => '',
    'administrations' => '',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration de Double Click',
    'fichiers' => 
    array (
      0 => 'fonctions',
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