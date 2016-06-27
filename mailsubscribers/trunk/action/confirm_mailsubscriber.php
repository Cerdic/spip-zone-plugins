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
	include_spip('inc/mailsubscribers');
	if (is_null($email)){
		list($email,$arg) = mailsubscribers_args_action();

		$row = false;
		if (!$email
			OR !$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut','spip_mailsubscribers','email='.sql_quote($email))){
			spip_log("confirm_mailsubscriber : email $email pas dans la base spip_mailsubscribers","mailsubscribers");
		}
		else {
			$cle = mailsubscriber_cle_action("confirm",$row['email'],$row['jeton']);
			if ($arg!==$cle){
				spip_log("confirm_mailsubscriber : cle $arg incorrecte pour email $email","mailsubscribers");
				$row = false;
			}
		}
	}
	else {
		$row = sql_fetsel('id_mailsubscriber,email,jeton,statut','spip_mailsubscribers','email='.sql_quote($email));
	}
	if (!$row){
		include_spip('inc/minipres');
		echo minipres(_T('info_email_invalide').'<br />'.entites_html($email));
		exit;
	}

	// il suffit de rejouer subscribe en forcant le simple-optin
	$subscribe_mailsubscriber = charger_fonction("subscribe_mailsubscriber","action");
	$subscribe_mailsubscriber($email,false);

}
