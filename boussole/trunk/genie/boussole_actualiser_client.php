<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_boussole_actualiser_client_dist($last) {

	include_spip('inc/client');
	boussole_actualiser_boussoles();

	return 1;
}

?>
