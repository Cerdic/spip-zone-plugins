<?php
/**
 * Plugin Grappes
 *
 * fichier action/trier_objets
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_trier_objets_dist() {
	$id_grappe = _request('id_grappe');
	$objet = _request('objet');
	$sort = explode(',', _request('sort'));

	//$classement = sql_allfetsel('*', 'spip_grappes_liens', 'id_grappe=' . intval($id_grappe),'','rang_lien');
	foreach ($sort as $cle => $valeur) {
		sql_updateq('spip_grappes_liens', array('rang_lien' => $cle+1 ), 'id_grappe = ' . intval($id_grappe) . ' AND objet = ' . sql_quote($objet) . ' AND id_objet='.intval($valeur));
	}

	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_grappe/$id_grappe'");
}
