<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// exemple #URL_ACTION_AUTEUR{instituer_quickvote, #ID_QUICKVOTE-1, #SELF}
function action_instituer_quickvote_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_quickvote, $statut) = preg_split('/\W/', $arg);

	$id_quickvote = intval($id_quickvote);

	include_spip('action/editer_quickvote');

	instituer_quickvote($id_quickvote, array('actif' => $statut));
}

?>