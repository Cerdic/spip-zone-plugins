<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_produit_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$id_produit = $arg;

	if ($id_produit = intval($id_produit)) {
		spip_log("produit $id_produit -> poubelle", 'commandes');
		sql_updateq('spip_produits', array('statut' => 'poubelle'), 'id_produit = '.intval($id_produit));
	}
}
