<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_evenement_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_evenement, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_evenement = intval($id_evenement);

	include_spip('action/editer_evenement');

	revisions_evenements($id_evenement, array('statut' => $statut));

}

?>
