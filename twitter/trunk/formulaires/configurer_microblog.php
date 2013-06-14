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
	if (!trim(_request('twitter_consumer_secret')) AND _request('twitter_consumer_key')){
		include_spip("inc/config");
		set_request('twitter_consumer_secret',lire_config("microblog/twitter_consumer_secret"));
	}

	set_request('erreur_code');
	set_request('erreur');

	return $erreurs;
}



function twitter_masquer_secret($secret){
	$affiche = "";
	if (strlen($secret))
		$affiche = substr($secret,0,4).str_pad("*",strlen($secret)-8,"*").substr($secret,-4);
	return $affiche;
}

function twitter_affiche_erreur_config($erreur, $erreur_code){
	if (!$erreur)
		return "";

	static $status_string = array(
		200 => '200 OK',
		204 => '204 No Content',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		304 => '304 Not Modified',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		503 => '503 Service Unavailable'
	);

	switch($erreur){
		case "auth_denied":
			$err = "Ajout du compte refusé.";
			break;
		case "old_token":
			$err = "Le jeton de sécurité a expiré, recommencez l'opération.";
			break;
		case "erreur_conf_app":
		default:
			$err = "Erreur de configuration de l'Application.";
			break;
	}

	if ($erreur_code)
		$err .= "<br />Le serveur a repondu <b>".(isset($status_string[$erreur_code])?$status_string[$erreur_code]:$erreur_code)."</b>";
	if ($erreur_code==401)
		$err .= "<br />Avez-vous bien rempli le champ \"Callback URL\" de votre application Twitter ?";

	return "<p>$err</p>";
}