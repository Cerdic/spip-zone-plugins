<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_configurer_microblog_verifier_dist(){

	$erreurs = array();

	// si secret vide, reprendre celui de la config actuelle
	if (!trim(_request('twitter_consumer_secret'))){
		include_spip("inc/config");
		set_request('twitter_consumer_secret',lire_config("microblog/twitter_consumer_secret"));
	}

	return $erreurs;
}



function twitter_masquer_secret($secret){
	$affiche = "";
	if (strlen($secret))
		$affiche = substr($secret,0,4).str_pad("*",strlen($secret)-8,"*").substr($secret,-4);
	return $affiche;
}