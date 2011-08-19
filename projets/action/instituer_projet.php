<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_projet_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_projet, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_projet = intval($id_projet);

	include_spip('action/editer_projet');

	$c = array('statut' => $statut);

	instituer_projet($id_projet, $c);
}
?>
