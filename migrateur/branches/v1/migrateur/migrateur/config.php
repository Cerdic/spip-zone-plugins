<?php

// Étapes
// ------

$GLOBALS['MIGRATEUR_ETAPES'] = array(
	'00_rien' => 'Il vous faut surcharger <code>migrateur/config.php</code>',
);

/*
 * Exemple simple
 * Les étapes mig_* sont présentes dans le plugin 'migrateur' par défaut.
 * Les autres sont à créer pour ses propres besoins.
 */
/*
$GLOBALS['MIGRATEUR_ETAPES'] = array(
	'mig_rsync_img'       => 'Synchroniser le répertoire IMG',
	'mig_exporter_bdd'    => 'Exporter la base de données source',
	'mig_transferer_bdd'  => 'Transfert des données SQL sur la base de données destination',

	'supprimer_tables_inutiles' => 'Suppression des tables SQL inutiles',
	'activer_plugins' => 'Activer les plugins',
	'configurer_site' => 'Changer les configurations du site',
);
*/

// Configuration
// -------------

// origine
define('MIGRATEUR_SOURCE_DIR', '/sites/mon_domaine.net/html/');

// SQL source
define('MIGRATEUR_SOURCE_SQL_USER', 'user_prod');
define('MIGRATEUR_SOURCE_SQL_PASS', '*******');
define('MIGRATEUR_SOURCE_SQL_BDD', 'dn_prod');



// destination
define('MIGRATEUR_DESTINATION_DIR', '/sites/mon_domaine.net/sd/dev/html/');

// SQL destination
define('MIGRATEUR_DESTINATION_SQL_USER', 'user_dev');
define('MIGRATEUR_DESTINATION_SQL_PASS', '*******');
define('MIGRATEUR_DESTINATION_SQL_BDD', 'db_dev');

// Nom du fichier d'export SQL
define('MIGRATEUR_NOM_EXPORT_SQL', 'export_source.sql');

