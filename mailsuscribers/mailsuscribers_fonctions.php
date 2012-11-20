<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function mailsuscriber_cle_action($action,$email,$jeton){
	$arg = "$action-$email-$jeton";
	include_spip("inc/securiser_action");
	$hash = calculer_cle_action($arg);
	return $hash;
}

function mailsuscriber_url_suscribe($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("suscribe_mailsuscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsuscriber_cle_action("suscribe",$email,$jeton),$sep);
	return $url;
}

function mailsuscriber_url_unsuscribe($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("unsuscribe_mailsuscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsuscriber_cle_action("unsuscribe",$email,$jeton),$sep);
	return $url;
}

function mailsuscriber_url_confirm($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("confirm_mailsuscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsuscriber_cle_action("confirm",$email,$jeton),$sep);
	return $url;
}

?>