<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_editer_lien_selection_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($action,$id_selection, $objet, $id_objet) = explode('/', $arg);

	include_spip('inc/autoriser');
	if (intval($id_selection) and autoriser('lier', 'selection', $id_selection, $GLOBALS['visiteur_session'], array('objet' => $objet,'id_objet'=>$id_objet))) {
		include_spip('action/editer_gis');
		if ($action == 'lier') {
			lier_selection($id_selection, $objet, $id_objet);
		} elseif ($action == 'delier') {
			delier_selection($id_selection, $objet, $id_objet);
		}
	}
}
