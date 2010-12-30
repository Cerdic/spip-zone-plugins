<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cic_dist($args, $retours){
	
	$retours['redirect'] = find_in_path("paiement/cic/paiement.php");
	
	return $retours;
}

?>
