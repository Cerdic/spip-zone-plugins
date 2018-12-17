<?php

/**
 * Options globales chargées à chaque hit
 *
 * @package SPIP\Formidable\Options
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/* déclaration des différentes variables utilisées pour effectuer l'anonymisation */
$GLOBALS['formulaires']['variables_anonymisation'] = array(
	'remote_user' => 'isset($_SERVER["REMOTE_USER"]) ? $_SERVER["REMOTE_USER"] : null',
	'php_auth_user' => 'isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : null',
);

/*
* on se contente d'initialiser si ces variable si elles ne
* le sont pas dans mes_options.php de l'instance
*/
if (isset($GLOBALS['formulaires']['passwd']) == false) {
	$GLOBALS['formulaires']['passwd'] = array(
		'interne' => 'palabresecreta',
	);
}


/* Lieux de stockages des fichiers, qu'on définit ici pour pouvoir l'utiliser en squelette
*/
if (!defined('_DIR_FICHIERS')) { // En attendant que ce soit natif spip
	define('_DIR_FICHIERS', _DIR_ETC.'fichiers/');
}

if (!defined('_DIR_FICHIERS_FORMIDABLE')) {
	define('_DIR_FICHIERS_FORMIDABLE', _DIR_FICHIERS.'formidable/');
}
