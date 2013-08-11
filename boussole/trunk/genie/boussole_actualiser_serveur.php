<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_serveur_dist($last) {

	include_spip('inc/cacher');
	boussole_actualiser_caches();

	return 1;
}

?>
