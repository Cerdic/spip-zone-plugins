<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_saveauto_telecharger() {

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!@is_readable($arg)) {
		redirige_par_entete(generer_url_ecrire('sauvegarder', 'etat=nok_tele', true));
	}

	// Autorisation
	if(!autoriser('sauvegarder')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// Determination du mime-type
	$extension = pathinfo($arg, PATHINFO_EXTENSION);
	$mime_type = ($extension == 'zip') ? 'application/zip' : 'text/plain';

	// Telechargement du fichier
	header("Content-type: application/force-download;");
	header("Content-Transfer-Encoding: ${mime_type}");
	header("Content-Length: ".filesize($arg));
	header("Content-Disposition: attachment; filename=\"".basename($arg)."\"");
	header("Pragma: no-cache");
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
	readfile($arg);

	redirige_par_entete(generer_url_ecrire('sauvegarder', 'etat=ok_tele', true));
	return;
}
