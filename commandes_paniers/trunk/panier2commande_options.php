<?php
#var_dump($GLOBALS['visiteur_session']);
#die('?');

// si on a une transformation panier=>commande en attente et qu'on est connecte,
// creer la commande
if (  isset($_COOKIE['spip_pwl']) AND $_COOKIE['spip_pwl']
	AND isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur']){

	include_spip('inc/filtres');
	$arg = reset(decoder_contexte_ajax($_COOKIE['spip_pwl'],'spip_pwl'));
	$commandes_paniers = charger_fonction("commandes_paniers","action");
	$commandes_paniers($arg);
	include_spip('inc/cookie');
	spip_setcookie("spip_pwl",'',0);
}

/**
 * Creer la commande si connecte ou renvoyer vers la page de login
 * @param null $arg
 */
function action_commandes_paniers_if_loged_dist($arg=null){
	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si on est identifie, on peut passer a la commande directement
	if (isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur']){
		$commandes_paniers = charger_fonction("commandes_paniers","action");
		$commandes_paniers($arg);
	}
	// sinon on note le arg pour creer la commande des qu'on est idenfie
	// et on redirige vers la page d'idendification
	else {
		include_spip('inc/cookie');
		include_spip('inc/filtres');
		spip_setcookie("spip_pwl",encoder_contexte_ajax(array($arg),'spip_pwl'));

		$GLOBALS['redirect'] = parametre_url(generer_url_public('qui'),'url',_request('redirect'));
	}
}