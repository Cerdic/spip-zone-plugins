<?php

/**
 * Plugin Z-commerce pour Spip 2.0
 * Licence GPL (c) 2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_instituer_commande($arg=null) {
	
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_commande, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_commande = intval($id_commande);
	
	spip_log("action_instituer_commande id_commande= $id_commande et statut= $statut",'commandes');


	include_spip('action/editer_commande');

	instituer_commande($id_commande, array('statut' => $statut));
}


?>
