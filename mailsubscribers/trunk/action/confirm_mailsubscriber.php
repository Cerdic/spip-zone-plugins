<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Confirmer l'inscription d'un email deja en base
 * (appelle lors du double-optin : delegue a subscribe le changement de statut en valide)
 *
 * @param string $email
 */
function action_confirm_mailsubscriber_dist($email=null){
	include_spip('mailsubscribers_fonctions');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut','spip_mailsubscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsubscriber_cle_action("confirm",$row['email'],$row['jeton'])){
			$row = false;
		}
	}
	else {
		$row = sql_fetsel('id_mailsubscriber,email,jeton,statut','spip_mailsubscribers','email='.sql_quote($email));
	}
	if (!$row){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// il suffit de rejouer subscribe en forcant le simple-optin
	$subscribe_mailsubscriber = charger_fonction("subscribe_mailsubscriber","action");
	$subscribe_mailsubscriber($email,false);

}
