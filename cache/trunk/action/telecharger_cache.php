<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Telecharger un fichier d'export JSON.
 *
 * @return void
 */
function action_telecharger_cache_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$fichier = $securiser_action();

	// Verification des autorisations
	include_spip('inc/autoriser');
	if (!autoriser('cache')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	if (file_exists($fichier)) {
		// Vider tous les tampons pour ne pas provoquer de Fatal memory exhausted
		$level = @ob_get_level();
		while ($level--) {
			@ob_end_clean();
		}

		// Header du stream
		$nom = basename($fichier);
		header('Content-Type: application/json');
		header("Content-Disposition: attachment; filename=\"${nom}\";");
		header('Content-Transfer-Encoding: binary');

		// fix for IE catching or PHP bug issue
		header('Pragma: public');
		header('Expires: 0'); // set expiration time
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

		if ($taille = filesize($fichier)) {
			header('Content-Length: ' . $taille);
		}
		readfile($fichier);
	} else {
		http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur') . ' 404', _T('info_acces_interdit'));
	}

	// et on finit comme ca d'un coup
	exit;
}
