<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
 
function action_serveur_telecharger_cache_dist(){

	// Securisation: le nom du fichier est attendu en argument
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$fichier = $securiser_action();
	if (!@is_readable($fichier)) {
		spip_log("Téléchargement impossible du cache, $fichier pas accessible en lecture", 'boussole' . _LOG_ERREUR);
		return;
	}

	// Telechargement du fichier cache (.xml)
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate");
	header("Cache-Control: private", false);
	header('Content-Type: texte/plain');
	header("Content-Length: ".filesize($fichier));
	header("Content-Disposition: attachment; filename=\"".basename($fichier)."\"");
	header("Content-Transfer-Encoding: binary");
	@readfile($fichier);
	exit();

}

?>