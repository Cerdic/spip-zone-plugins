<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function selection_supprimer($id_selection) {
	include_spip('action/editer_liens');
	$id_selection = intval($id_selection);

	if ($id_selection > 0) {
		$ok = sql_delete(
			'spip_selections',
			'id_selection = '.$id_selection
		);

		if ($ok) {
			objet_optimiser_liens(array('selection'=>'*'), '*');
		}
	}

	return $ok;
}

function selection_associer($id_selection, $objets, $qualif = null) {
	include_spip('action/editer_liens');
	$res = objet_associer(array('selection'=>$id_selection), $objets, $qualif);
	include_spip('inc/invalideur');
	suivre_invalideur("id='selection/$id_selection'");
	return $res;
}

/**
 * Dissocier un point géolocalisé des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * un * pour $id_auteur,$objet,$id_objet permet de traiter par lot
 *
 * @param int $id_gis
 * @param array $objets
 * @return string
 */
function selection_dissocier($id_selection, $objets) {
	include_spip('action/editer_liens');
	$res = objet_dissocier(array('gis' => $id_selection), $objets);
	include_spip('inc/invalideur');
	suivre_invalideur("id='selection/$id_selection'");
	return $res;
}

function lier_selection($id_selection, $objet, $id_objet) {
	if ($id_objet and $id_gis
		and preg_match('/^[a-z0-9_]+$/i', $objet) # securite
		and !sql_getfetsel('id_selection', 'spip_selections_liens', "id_gis=$id_selection AND id_objet=$id_objet AND objet=".sql_quote($objet))
		and autoriser('lier', 'selection', $id_selection, $GLOBALS['visiteur_session'], array('objet' => $objet,'id_objet'=>$id_objet))
	) {
		selection_associer($id_gis, array($objet=>$id_objet));
		return true;
	}
	return false;
}

function delier_selection($id_selection, $objet, $id_objet) {
	//$objet = objet_type($objet);
	if ($id_objet and $id_gis
		and preg_match('/^[a-z0-9_]+$/i', $objet) # securite
		and autoriser('delier', 'selection', $id_selection, $GLOBALS['visiteur_session'], array('objet' => $objet,'id_objet'=>$id_objet))
	) {
		selection_dissocier($id_selection, array($objet => $id_objet));
		return true;
	}
	return false;
}
