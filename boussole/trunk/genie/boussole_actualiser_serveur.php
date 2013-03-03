<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_serveur_dist($last) {

	include_spip('inc/cacher');

	// Acquisition de la liste des boussoles disponibles sur le serveur
	$boussoles = array();
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	// Regénération du cache de chaque boussole disponible
	if ($boussoles) {
		foreach($boussoles as $_alias => $_infos) {
			boussole_cacher($_alias, $_infos['prefixe']);
		}
	}

	return 1;
}

?>
