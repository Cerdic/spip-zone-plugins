<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cic_dist($args, $retours){
	
	$tpe="";
	$soc="";
	$key="";
	$motdepasse="";
	$retourok="";
	$retourko="";
	
	$dir="/";
	$serveur="http://ssl.paiement.cic-banques.fr";
	
	$retours['redirect'] = $serveur;
	
	return $retours;
}

?>
