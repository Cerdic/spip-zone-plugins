<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// exemple #URL_ACTION_AUTEUR{instituer_produit, #ID_PRODUIT-prop, #SELF}
function action_instituer_produit_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_produit, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_produit = intval($id_produit);

	include_spip('action/editer_produit');

	instituer_produit($id_produit, array('statut' => $statut));
}

?>
