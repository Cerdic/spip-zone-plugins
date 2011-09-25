<?php

/**
 * Plugin Z-commerce pour Spip 2.0
 * Licence GPL (c) 2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_instituer_contacts_abonnement($arg=null) {
	
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_contacts_abonnement, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_contacts_abonnement = intval($id_contacts_abonnement);

	include_spip('action/editer_contacts_abonnement');

	instituer_contacts_abonnement($id_contacts_abonnement, array('statut_abonnement' => $statut));
}


?>
