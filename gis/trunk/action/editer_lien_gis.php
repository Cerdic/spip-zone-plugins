<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function action_editer_lien_gis_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($action,$id_gis, $objet, $id_objet) = explode('/', $arg);

	include_spip('inc/autoriser');
	if (intval($id_gis) and autoriser('lier', 'gis', $id_gis, $GLOBALS['visiteur_session'], array('objet' => $objet,'id_objet'=>$id_objet))) {
		include_spip('action/editer_gis');
		if ($action == 'lier') {
			lier_gis($id_gis, $objet, $id_objet);
		} elseif ($action == 'delier') {
			delier_gis($id_gis, $objet, $id_objet);
		}
	}
}
