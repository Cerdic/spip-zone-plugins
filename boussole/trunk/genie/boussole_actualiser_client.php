<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * @uses action_client_actualiser_boussoles_dist()
 *
 * @param $last
 * @return int
 */
function genie_boussole_actualiser_client_dist($last) {

	include_spip('inc/client');
	boussole_actualiser_boussoles();

	return 1;
}

?>
