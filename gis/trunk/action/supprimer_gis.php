<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function action_supprimer_gis_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_gis) = preg_split(',[^0-9],', $arg);
	include_spip('inc/autoriser');
	if (intval($id_gis) and autoriser('supprimer', 'gis', $id_gis)) {
		include_spip('action/editer_gis');
		supprimer_gis($id_gis);
	}
}
