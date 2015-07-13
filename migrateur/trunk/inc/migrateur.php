<?php

/**
 * Fonctions outils pour le migrateur
 *
 * @package SPIP\Migrateur\Fonctions
**/

use SPIP\Migrateur;


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/migrateur_aide');


/**
 * Retourne certaines informations du site 
 *
 * @uses migrateur_infos()
 * @return null|Spip\Migrateur\Data
 *     Données de connexions si obtenues
**/
function migrateur_source() {
	return migrateur_infos();
}


/**
 * Retourne certaines informations du site
 *  
 * @uses migrateur_infos()
 * @return null|Spip\Migrateur\Data
 *     Données de connexions si obtenues
**/
function migrateur_destination() {
	return migrateur_infos();
}


/**
 * Retourne certaines informations sur le SPIP actuellement exécuté
 * 
 * Notamment les identifiants de connexion à la base de données,
 * ainsi que le chemin complet vers la racine du site.
 *
 * @api
 * @return null|Spip\Migrateur\Data
 *     Données de connexions si définies
**/
function migrateur_infos() {
	return new Migrateur\Data();
}




/**
 * Ajoute un message de log dans tmp/migrateur/migrateur.log
 * ainsi que dans tmp/migrateur/etape.log et tmp/log/migrateur.log !
 *
 * @param string $msg Le message
 * @param string $type Type de message
 * @param bool|null $set_stream
 *     Indique si la fonction doit effectuer des echos lorsqu'elle est appelée (défaut false).
 *     À définir avec `migrateur_log('', '', true);`
**/
function migrateur_log($msg, $type="", $set_stream = null) {
	static $done   = false;
	static $stream = false;

	if (!is_null($set_stream)) {
		$stream = (bool)$set_stream;
		return true;
	}

	$dir = _DIR_TMP . 'migrateur';
	if (!$done) {
		sous_repertoire(_DIR_TMP . 'migrateur');
		$done = true;
	}

	if ($type) {
		$message = '[' . $type . '] ' . $message;
	}

	file_put_contents($dir . "/migrateur.log", date("Y:m:d H:i:s") . " | " . $msg . "\n", FILE_APPEND);
	file_put_contents($dir . "/etape.log", $msg . "\n", FILE_APPEND);
	spip_log($msg, 'migrateur');

	if ($stream) {
		echo $msg . "\n";
		flush();
	}
}



