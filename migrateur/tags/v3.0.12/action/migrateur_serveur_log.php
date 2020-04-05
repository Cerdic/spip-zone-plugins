<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/autoriser');


/**
 * Reçoit une demande d'action sur les logs serveur du migrateur
**/
function action_migrateur_serveur_log_dist() {


	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		minipres();
		exit;
	}

	if (!in_array($arg, array('delete', 'get_last'))) {
		include_spip('inc/minipres');
		minipres();
		exit;
	}

	// on stocke le dernier endroit où on est arrivé dans la lecture du log
	// pour le prochain passage...
	session_start();

	if ($arg == 'delete') {
		include_spip('inc/flock');
		supprimer_fichier(_DIR_TMP . '/migrateur/serveur.log');
		unset($_SESSION['migrateur_log_offset']);
		return;
	}

	if ($arg == 'get_last') {
		sous_repertoire(_DIR_TMP . '/migrateur/');
		$file = _DIR_TMP . '/migrateur/serveur.log';
		if (!file_exists($file)) {
			file_put_contents($file, "");
		}

		$handle = fopen(_DIR_TMP . '/migrateur/serveur.log', 'r');
		if (isset($_SESSION['migrateur_log_offset'])) {
			$data = stream_get_contents($handle, -1, $_SESSION['migrateur_log_offset']);
			$_SESSION['migrateur_log_offset'] = ftell($handle);
			echo $data; // echo nl2br($data);
		} else {
			fseek($handle, 0, SEEK_END);
			$_SESSION['migrateur_log_offset'] = ftell($handle);
		}
		exit;
	}
 
}
