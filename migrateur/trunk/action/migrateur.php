<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('migrateur/config');
include_spip('inc/migrateur');


/**
 * Lance une action de migration
 *
 * Charge la fonction migrateur_$arg() dans migrateur/$arg.php
**/
function action_migrateur_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$func = charger_fonction($arg, 'migrateur', true);

	if (function_exists($func)) {

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
		if ($nb) {
			migrateur_log("-----| Étape n°$nb");
			migrateur_log("     | " . $GLOBALS['MIGRATEUR_ETAPES'][$arg]);
		} else {
			migrateur_log("-----> " . $GLOBALS['MIGRATEUR_ETAPES'][$arg]);
		}
		migrateur_log("     | Exécution de $func()\n");
		$func();
		$t = spip_timer($func);
		migrateur_log("");
		migrateur_log("-----| Fin de $func(), en $t");

	} else {
		die("La fonction $func n'existe pas !");
	}
}




?>
