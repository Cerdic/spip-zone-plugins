<?php
/**
 * Gestion de l'action de suppression de niveau de Xiti
 *
 * @package SPIP\Xiti\Action
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_xiti_niveau_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_xiti_niveau) = preg_split(',[^0-9],', $arg);
	include_spip('inc/autoriser');
	if (intval($id_xiti_niveau) and autoriser('supprimer', 'xiti_niveau', $id_xiti_niveau)) {
		include_spip('action/editer_xiti_niveau');
		xiti_niveau_supprimer($id_xiti_niveau);
	}
}
