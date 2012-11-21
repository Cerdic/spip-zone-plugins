<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Confirmer l'inscription d'un email deja en base
 * (appelle lors du double-optin : delegue a suscribe le changement de statut en valide)
 *
 * @param string $email
 */
function action_confirm_mailsuscriber_dist($email=null){
	include_spip('mailsuscribers_fonctions');
	if (is_null($email)){
		$email = _request('email');
		$arg = _request('arg');
		$row = sql_fetsel('id_mailsuscriber,email,jeton,lang,statut','spip_mailsuscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsuscriber_cle_action("confirm",$row['email'],$row['jeton'])){
			$row = false;
		}
	}
	else {
		$row = sql_fetsel('id_mailsuscriber,email,jeton,statut','spip_mailsuscribers','email='.sql_quote($email));
	}
	if (!$row){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// il suffit de rejouer suscribe en forcant le simple-optin
	$suscribe_mailsuscriber = charger_fonction("suscribe_mailsuscriber","action");
	$suscribe_mailsuscriber($email,false);

}