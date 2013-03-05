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
function action_confirm_unsubscribe_mailsubscriber_dist($email=null){
	include_spip('mailsubscribers_fonctions');
	if (is_null($email)){
		$securiser_action = charger_fonction("securiser_action","inc");
		$email = $securiser_action();
		$email = explode("-",$email);
		$arg = array_pop($email);
		$email = implode("-",$email);

		$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut','spip_mailsubscribers','email='.sql_quote($email));
		if (!$row
			OR $arg!==mailsubscriber_cle_action("unsubscribe",$row['email'],$row['jeton'])){
			$row = false;
		}
	}

	if (!$row){
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	// il suffit de rejouer subscribe en forcant le simple-optin
	$unsubscribe_mailsubscriber = charger_fonction("unsubscribe_mailsubscriber","action");
	$unsubscribe_mailsubscriber ($email,false);
}
