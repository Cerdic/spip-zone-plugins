<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Lance une action de migration
 *
 * Charge la fonction migrateur_$arg() dans migrateur/$arg.php
**/
function action_migrateur_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('migrateur/config');
	include_spip('inc/migrateur');

	$func = charger_fonction($arg, 'migrateur', true);

	/* En cas de demande de flux continue des logs, préparer ce qu'il faut */
	$stream = (bool) _request('stream');
	if ($stream) {
		// forcer les logs à faire des echos
		migrateur_log("", "", true);
		// forcer l'absence de redirection ajax
		$GLOBALS['redirect'] = "";

		header('Content-type: text/html; charset=utf-8');
		#header('Content-type: application/octet-stream;');

		// Turn off output buffering
		ini_set('output_buffering', 'off');
		// Turn off PHP output compression
		ini_set('zlib.output_compression', false);
		// Implicitly flush the buffer(s)
		ini_set('implicit_flush', true);
		ob_implicit_flush(true);
		// Clear, and turn off output buffering
		while (ob_get_level() > 0) {
			// Get the curent level
			$level = ob_get_level();
			// End the buffering
			ob_end_clean();
			// If the current level has not changed, abort
			if (ob_get_level() == $level) break;
		}

		// Disable apache output buffering/compression
		if (function_exists('apache_setenv')) {
			apache_setenv('no-gzip', '1');
			apache_setenv('dont-vary', '1');
		}
	}

	if (function_exists($func)) {

		// Les calculs peuvent être très long. On augmente le timeout
		@set_time_limit(0);


		// gestion des timeout pour certaines fonctions, à la manière des mises à jour de SPIP.
		if (!defined('_UPGRADE_TIME_OUT')) {
			define('_UPGRADE_TIME_OUT', 20);
		}
		define('_TIME_OUT', time() + _UPGRADE_TIME_OUT);

		// suppression du fichier de log de l'etape, pour réinitialiser les logs
		if (!_request('recharger')) {
			supprimer_fichier( _DIR_TMP . 'migrateur/etape.log' );
		}

		// donner accès aux fonctions de config
		include_spip('inc/config');

		spip_timer($func);
		$nb = parametre_url(_request('redirect'), 'nb');

		$description = $GLOBALS['MIGRATEUR_ETAPES'][$arg];

		$data = null;
		if (is_array($description)) {
			list($description, $data) = $description;
		}

		if ($nb) {
			migrateur_log("-----| Étape n°$nb");
			migrateur_log("     | <em>" . $description . "</em>");
		} else {
			migrateur_log("-----> " . $description);
		}
		migrateur_log("     | Exécution de $func()\n");
		$func($data);
		$t = spip_timer($func);
		migrateur_log("");
		migrateur_log("-----| Fin de $func(), en $t");

	} else {
		die("La fonction <code>$arg</code> n'existe pas !\n");
	}
}


