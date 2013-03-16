<?php
/**
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
 
function action_langonet_telecharger_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$file_name = $securiser_action();
	if (!@is_readable($file_name)) {
		spip_log("*** LANGONET (action_langonet_telecharger_dist) ERREUR: $file_name pas accessible en lecture");
	}

	// Telechargement du fichier de log (.log) ou de langue (.php)
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate");
	header("Cache-Control: private", false);
	header('Content-Type: texte/plain');
	header("Content-Length: ".filesize($file_name));
	header("Content-Disposition: attachment; filename=\"".basename($file_name)."\"");
	header("Content-Transfer-Encoding: binary");
	@readfile($file_name);
	exit();

}

?>