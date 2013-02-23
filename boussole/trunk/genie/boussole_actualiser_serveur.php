<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_serveur_dist($last) {

	include_spip('inc/cacher');

	$boussoles = array();
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	if ($boussoles) {
		foreach($boussoles as $_prefixe => $_alias) {
			boussole_cacher($_alias, $_prefixe);
		}
	}

	return 1;
}

?>
