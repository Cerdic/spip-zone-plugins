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
 * Loge une erreur 
 *
 * @param string $msg
**/
function migrateur_log_error($msg) {
	migrateur_log('<error>' . $msg . '</error>');
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

	/* Forcer un echo sur les logs */
	if (!is_null($set_stream)) {
		$stream = (bool)$set_stream;
		return true;
	}

	/* créer le répertoire de log */
	$dir = _DIR_TMP . 'migrateur';
	if (!$done) {
		sous_repertoire(_DIR_TMP . 'migrateur');
		$done = true;
	}

	if ($type) {
		$msg = '[' . $type . '] ' . $msg;
	}

	file_put_contents($dir . "/migrateur.log", date("Y:m:d H:i:s") . " | " . $msg . PHP_EOL, FILE_APPEND);
	file_put_contents($dir . "/etape.log", $msg . PHP_EOL, FILE_APPEND);
	spip_log($msg, 'migrateur');

	if ($stream) {
		migrateur_stream_log($msg);
	}
}

/**
 * Envoie un log (json) au navigateur
 * 
 * Certaines configurations sont ennuyantes, pour passer outre la mise en buffer,
 * entre php, mod_xxx d'apache, apache, le navigateur. 
 * 
 * La fonction `migrateur_preparer_streaming()` fait tout son possible pour
 * les désactiver, mais cela ne suffit pas toujours.
 * 
 * On force donc l'envoi d'un paquet assez gros (8ko) pour qu'il soit envoyé
 * à chaque fois au navigateur. Mais pas trop rapidement entre chaque 
 * message, sinon, là encore, les messages peuvent se cumuler avant d'arriver
 * au navigateur (ce qui fait que le json n'est plus valide)…
 *
 * @param string $msg 
 *    Le message à envoyer
**/
function migrateur_stream_log($msg) {
	/** 
	 * Si stream, forcer gros message, pour passer outre différents buffers…
	 * Tristement…
	 */
	static $buffer_size = 8192;

	/**
	 * Si stream, il faut un délai minimal entre 2 envois :/
	 * Tristement…
	 * 
	 * ~ 16ms / ligne requis, mais je JS est tolérant et peut recevoir
	 * plusieurs lignes d'un coup. 
	 */
	static $delai_minimal_ms = 5;

	/**
	 * Pour calculer le temps écoulé depuis le dernier envoi d'un log
	 */
	static $chronometre = false;

	if ($chronometre) {
		$last = intval(spip_timer('stream_log', true));
	} else {
		$last = 0;
	}

	$message = json_encode(array('log' => $msg . PHP_EOL));
	echo str_pad($message, $buffer_size) . PHP_EOL;
	flush();

	if ($last < $delai_minimal_ms) {
		usleep(($delai_minimal_ms-$last) * 1000);
	}

	spip_timer('stream_log');
	$chronometre = true;
}



