<?php

// fonction honteusement dupliquee de notifications_pipeline
function ng_url_suivi($email){
	if (!$email) return "";
	include_spip("inc/securiser_action");
	$key = calculer_cle_action("abonner_notifications $email");
	$url = url_absolue(generer_url_public('notifications', "email=$email&key=$key"));
	return $url;
}