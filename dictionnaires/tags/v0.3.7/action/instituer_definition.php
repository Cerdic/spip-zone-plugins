<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// exemple #URL_ACTION_AUTEUR{instituer_definition, #ID_DEFINITION-publie, #SELF}
function action_instituer_definition_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_definition, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_definition = intval($id_definition);

	include_spip('action/editer_definition');

	instituer_definition($id_definition, array('statut' => $statut));
}

?>
