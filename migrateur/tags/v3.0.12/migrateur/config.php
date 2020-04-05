<?php

// Étapes
// ------

$GLOBALS['MIGRATEUR_ETAPES'] = array(
	'mig_test_rien' => 'Il vous faut surcharger <code>migrateur/config.php</code>',
);

/*
 * Exemple simple
 * Les étapes mig_* sont présentes dans le plugin 'migrateur' par défaut.
 * Les autres sont à créer pour ses propres besoins.
 */
/*
$GLOBALS['MIGRATEUR_ETAPES'] = array(
	#'mig_test_rien'          => 'Ça ne fait rien',
	#'mig_test_stream_logs'   => 'Tester le stream des logs locaux',
	'mig_test_communication' => "Tester la communication avec le serveur source",

	'mig_sync_img'                  => 'Synchroniser le répertoire IMG',
	#'mig_sync'                     => array('Synchroniser le répertoire IMG', array(
		'repertoire' => 'IMG',
		'test' => false,  // mode test : si true, liste les différences (nombre de fichiers nouveaux, modifiés, supprimés) mais ne modifie rien.
	)),

	'mig_bdd_source_make_and_get_dump_sql' => 'Crée et récupère un dump SQL de la base de données source',
	# Options possibles (et valeurs par défaut) :
	#'mig_bdd_source_make_and_get_dump_sql' => array('Crée et récupère un dump SQL de la base de données source', array(
		'gzip_si_possible' => true,      // compresse la base de données (si possible)
		'mysqldump_si_possible' => true, // utilise mysqldump si disponible, sinon phpdump (plus long, mais parfois plus simple à mettre en œuvre)
	)),

	'mig_bdd_destination_put_dump_sql'  => 'Écrase la base de données actuelle par le dernier dump SQL présent dans tmp/dump)',

	#'supprimer_tables_inutiles' => 'Suppression des tables SQL inutiles',
	#'activer_plugins' => 'Activer les plugins',
	#'configurer_site' => 'Changer les configurations du site',
);
*/
