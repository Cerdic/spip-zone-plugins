<?php

/**
 * Fonctions outils pour le migrateur
 *
 * @package SPIP\Migrateur\Fonctions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/migrateur_aide');
include_spip('inc/Migrateur_SSH');
include_spip('inc/Migrateur_SQL');
include_spip('inc/Migrateur_DATA');



/**
 * Retourne les données d'accès au serveur source 
 * 
 * Si elles sont là, on les retoune
 *
 * @uses migrateur_data()
 * @return null|Migrateur_DATA
 *     Données de connexions si définies
**/
function migrateur_source() {
	return migrateur_data('source');
}


/**
 * Retourne les données d'accès au serveur destination 
 * 
 * Si elles sont là, on les retoune
 *
 * @uses migrateur_data()
 * @return null|Migrateur
 *     Données de connexions si définies
**/
function migrateur_destination() {
	return migrateur_data('destination');
}


/**
 * Retourne les données d'accès au serveur source ou destination
 * 
 * Si elles sont là, on les retoune
 *
 * @uses migrateur_data()
 * @param string $type
 *     Type de données désirées (source | destination)
 * @return null|Migrateur_DATA
 *     Données de connexions si définies
**/
function migrateur_data($type) {
	static $migs = array();

	$type = strtoupper($type);

	if (isset($migs[$type])) {
		return $migs[$type];
	} 

	$props = array();

	foreach (array(
		"MIGRATEUR_{$type}_DIR" => 'dir', 

		"MIGRATEUR_{$type}_SQL_USER"        => 'sql/user', 
		"MIGRATEUR_{$type}_SQL_PASS"        => 'sql/pass', 
		"MIGRATEUR_{$type}_SQL_BDD"         => 'sql/bdd', 
		"MIGRATEUR_{$type}_SQL_LOGIN_PATH"  => 'sql/login_path', 

		"MIGRATEUR_{$type}_SSH_SERVER"      => 'ssh/server', 
		"MIGRATEUR_{$type}_SSH_USER"        => 'ssh/user', 
		"MIGRATEUR_{$type}_SSH_PORT"        => 'ssh/port', 
	) as $const => $prop) { 
		if (defined($const) and constant($const)) {
			// 'ssh/server' => group 'ssh' et clé 'server'
			$keys = explode('/', $prop);
			if (count($keys) > 1) {
				$group = array_shift($keys);
				$prop = array_shift($keys);
				if (!isset($props[$group])) {
					$props[$group] = array();
				}
				$props[$group][$prop] = constant($const);
			} else {
				$props[$prop] = constant($const);
			}
		}
	}

	return $migs[$type] = new Migrateur_DATA($props);
}



/**
 * Ajoute un message de log dans tmp/migrateur/migrateur.log
 * ainsi que dans tmp/migrateur/etape.log et tmp/log/migrateur.log !
 *
 * @param string $msg Le message
 * @param string $type Type de message
**/
function migrateur_log($msg, $type="") {
	static $done = false;
	$dir = _DIR_TMP . 'migrateur';
	if (!$done) {
		sous_repertoire(_DIR_TMP . 'migrateur');
	}

	if ($type) $message = '[' . $type . '] ' . $message;

	file_put_contents($dir . "/migrateur.log", date("Y:m:d H:i:s") . " > " . $msg . "\n", FILE_APPEND);
	file_put_contents($dir . "/etape.log", $msg . "\n", FILE_APPEND);
	spip_log($msg, 'migrateur');
}



/**
 * Retourne les données d'accès au serveur source s'il est par SSH
 * 
 * Si le SPIP source se trouve sur un autre serveur que le SPIP de distination
 * (là où on execute le plugin migrateur), alors des données
 * de connexion SSH sont définies.
 *
 * Si elles sont là, on les retoune
 *
 * @deprecated Utiliser migrateur_source()->ssh
 * 
 * @return null|Migrateur_SSH
 *     Données de connexion SSH si définies
**/
function migrateur_source_ssh() {
	$source = migrateur_source();
	return $source->ssh;
}



/**
 * Obtient le chemin d'un executable sur le serveur.
 *
 * @deprecated Utiliser migrateur_source()->obtenir_commande_serveur('rsync')
 * @example
 *     ```
 *     $cmd = migrateur_obtenir_commande_serveur('rsync');
 *     if ($cmd) 
 *         exec("$cmd ... ... ");
 *     }
 *     ```
 * @param string $command
 *     Nom de la commande
 * @return string
 *     Chemin de la commande
**/
function migrateur_obtenir_commande_serveur($command) {
	$source = migrateur_source();
	return $source->obtenir_commande_serveur($command);
}

