<?php
/**
 * Ce fichier contient l'action `supprimer_log` utilisée pour supprimer un fichier log de SPIP.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Action de suppression d'un log contenu dans _DIR_LOG.
 * Cette action est possible dans le privé lorsqu'un fichier log est en cours d'affichage.
 *
 * @return void
 */
function action_supprimer_log_dist() {

	// Securisation: le nom du fichier est attendu en argument
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$fichier = $securiser_action();
	if (!@is_readable($fichier)) {
		// On loge l'erreur dans le log par défaut de SPIP
		spip_log("Suppression impossible du fichier log, $fichier: pas accessible en lecture", _LOG_ERREUR);
		return;
	}

	include_spip('inc/autoriser');
	if (autoriser('voir', 'simplog')) {
		spip_unlink($fichier);
		// On redirige vers la page d'accueil de simplog, le fichier affiché n'existant plus.
		redirige_url_ecrire('simplog');
	}
}
