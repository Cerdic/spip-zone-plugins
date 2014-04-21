<?php
/**
 * Ce fichier contient l'action `serveur_telecharger_cache` utilisée par un site serveur
 * pour télécharger un cache.
 *
 * @package SPIP\BOUSSOLE\Serveur\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de téléchargement d'un cache d'une boussole hébergée par un site serveur ou de
 * la liste des boussoles hébergées.
 *
 * Cette action est possible dans le privé à partir de la liste des caches affichée dans
 * l'onglet fonction serveur.
 *
 */
function action_serveur_telecharger_cache_dist(){

	// Securisation: le nom du fichier est attendu en argument
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$fichier = $securiser_action();
	if (!@is_readable($fichier)) {
		spip_log("Téléchargement impossible du cache, $fichier pas accessible en lecture", _BOUSSOLE_LOG . _LOG_ERREUR);
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