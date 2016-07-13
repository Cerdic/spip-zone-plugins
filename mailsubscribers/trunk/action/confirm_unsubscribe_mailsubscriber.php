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
 * @param string $identifiant
 */
function action_confirm_unsubscribe_mailsubscriber_dist($email = null, $identifiant = null) {
	include_spip('mailsubscribers_fonctions');
	if (is_null($email)) {
		$securiser_action = charger_fonction("securiser_action", "inc");
		$email = $securiser_action();
		$email = explode("-", $email);
		$identifiant = array_pop($email);
		$email = mailsubscriber_base64url_decode(implode("-", $email));
	}

	// il suffit de rejouer unsubscribe en forcant le simple-optin
	$unsubscribe_mailsubscriber = charger_fonction("unsubscribe_mailsubscriber", "action");
	$unsubscribe_mailsubscriber ($email, $identifiant, false);
}

function mailsubscriber_base64url_decode($data) {
	return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}