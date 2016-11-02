<?php
/**
 * Ce fichier contient l'action `telecharger_log` utilisée pour télécharger un fichier log de SPIP.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action de téléchargement d'un log contenu dans _DIR_LOG.
 * Cette action est possible dans le privé lorsqu'un fichier log est en cours d'affichage.
 *
 * @return void
 */
function action_telecharger_log_dist() {

	// Securisation: le nom du fichier est attendu en argument
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$fichier = $securiser_action();
	if (!@is_readable($fichier)) {
		// On loge l'erreur dans le log par défaut de SPIP
		spip_log("Téléchargement impossible du fichier log, $fichier: pas accessible en lecture", _LOG_ERREUR);

		return;
	}

	// Telechargement du fichier log (.log*)
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Cache-Control: private', false);
	header('Content-Type: texte/plain');
	header('Content-Length: ' . filesize($fichier));
	header('Content-Disposition: attachment; filename="' . basename($fichier) . '"');
	header('Content-Transfer-Encoding: binary');
	@readfile($fichier);
	exit();
}
