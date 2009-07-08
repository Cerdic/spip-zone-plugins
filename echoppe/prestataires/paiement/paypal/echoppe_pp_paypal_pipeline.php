<?php

function echoppe_pp_paypal_descriptif($flux){
	
	$infos = array();
	
	$info['nom'] = "PayPal Sandbox";
	$info['url'] = "https://developer.paypal.com/";
	$info['descriptif'] = "Logique de création des formulaire de paiement paypal";
	$info['logo'] = "https://developer.paypal.com/en_US/i/logo/new_logo_ic.gif";
	$info['prefix'] = "paypal_sandbox";
	$info['version'] = "0.1";
	$info['avertissement_user'] = "<multi>[fr]Attention, ceci est un {{test}}[en]Warning, this is a {{test}}</multi>";
	
	$flux[] = $infos;
	
	return $flux;
	
}


?>
