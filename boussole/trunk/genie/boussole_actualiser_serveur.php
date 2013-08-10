<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_serveur_dist($last) {

	include_spip('inc/cacher');

	// Acquisition de la liste des boussoles disponibles sur le serveur.
	// (on sait déjà que le mode serveur est actif)
	$boussoles = $GLOBALS['serveur_boussoles_disponibles'];
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	if ($boussoles) {
		// Génération du cache de chaque boussole disponible pour l'action serveur_informer_boussole
		foreach($boussoles as $_alias => $_infos) {
			boussole_cacher_xml($_alias, $_infos['prefixe']);
		}

		// Génération du cache de la liste des boussoles disponibles pour l'action serveur_lister_boussoles
		boussole_cacher_liste($boussoles);
	}

	return 1;
}

?>
