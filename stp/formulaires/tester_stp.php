<?php


function formulaires_tester_stp_charger_dist() {
	$val = array();

	include_spip('inc/stp_depot_local');
	stp_actualiser_paquets_locaux();
	
	return $val;
}


function formulaires_tester_stp_verifier_dist() {
	$err = array();
	$err['message_ok'] = "Et voilà !";
	return $err;
}

?>
