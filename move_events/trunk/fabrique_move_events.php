<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2014-06-07 15:56:47
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
    'nom' => 'Migration d\'événements',
    'slogan' => 'Associez vos événements à un autre article',
    'description' => 'Ce plugin sert à déplacer en masse les événements associés à un article vers un autre.',
    'prefixe' => 'move_events',
    'version' => '1.0.0',
    'auteur' => 'amaury',
    'auteur_lien' => 'http://contrib.spip.net/Amaury-Adon',
    'licence' => 'GNU/GPL',
    'categorie' => 'date',
    'etat' => 'dev',
    'compatibilite' => '[3.0.14;3.0.*]',
    'documentation' => '',
    'administrations' => '',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'inserer' => 
    array (
      'paquet' => '<necessite nom="agenda"  compatibilite="[1.24.0;]" /> ',
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
          'extension' => '',
          'contenu' => '',
        ),
      ),
    ),
    'objets' => 
    array (
    ),
  ),
);

?>