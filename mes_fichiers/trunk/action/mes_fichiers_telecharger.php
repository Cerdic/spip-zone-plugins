<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_mes_fichiers_telecharger() {

	// Securisation de l'argument nom de l'archive
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Si l'archive n'est pas accessible on renvoie une erreur
	if (!@is_readable($arg)) {
		spip_log("L'archive $arg n'est pas accessible pour le téléchargement", 'mes_fichiers' . _LOG_ERREUR);
		redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=nok_tele', true));
	}

	// Autorisation
	if(!autoriser('sauvegarder')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// Telechargement du fichier
	header("Content-type: application/force-download;");
	header("Content-Transfer-Encoding: application/zip");
	header("Content-Length: ".filesize($arg));
	header("Content-Disposition: attachment; filename=\"".basename($arg)."\"");
	header("Pragma: no-cache");
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
	readfile($arg);
	redirige_par_entete(generer_url_ecrire('mes_fichiers', 'etat=ok_tele', true));
}
?>