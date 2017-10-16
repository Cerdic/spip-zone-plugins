<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function mailsubscriber_cle_action($action,$email,$jeton){
	$arg = "$action-$email-$jeton";
	include_spip("inc/securiser_action");
	$hash = calculer_cle_action($arg);
	return $hash;
}

function mailsubscriber_url_subscribe($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("subscribe_mailsubscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsubscriber_cle_action("subscribe",$email,$jeton),$sep);
	return $url;
}

function mailsubscriber_url_unsubscribe($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("unsubscribe_mailsubscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsubscriber_cle_action("unsubscribe",$email,$jeton),$sep);
	return $url;
}

function mailsubscriber_url_confirm($email,$jeton,$sep="&amp;"){
	$url = generer_url_action("confirm_mailsubscriber","email=".urlencode($email),false,true);
	$url = parametre_url($url,"arg",mailsubscriber_cle_action("confirm",$email,$jeton),$sep);
	return $url;
}

/**
 * Un filtre pour lister les listes de diffusion dans un squelette
 *
 * @Note
 * Les listes sont stockées différement entre la branche 1 et 2
 *
 * @uses mailsubscribers_listes
 * @param array $options
 *     voir mailsubscribers_listes()
 * @return array|int
 */
function filtre_mailsubscribers_listes_dist($options = array()) {
	include_spip('inc/mailsubscribers');

	return mailsubscribers_listes($options);
}
