<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('action/editer_gis');

/**
 * Interface C(r)UD pour GIS
 */

/**
 * Create :
 * Crée un point géolocalisé
 *
 * @param $dummy
 * @param array $set : Le contenu des champs à mettre en base
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id créé
 */
function crud_gis_create_dist($dummy, $set = null) {
	if (autoriser('voir', 'gis') and $id = gis_inserer()) {
		$err = gis_modifier($id, $set);
	} else {
		$err = _T('crud:erreur_creation', array('objet' => 'gis'));
	}
	return array('success' => ($err and strlen($err) > 0) ? false : true, 'message' => $err, 'result' => array('id' => $id));
}

/**
 * Update :
 * Met à jour un point géolocalisé
 *
 * @param $dummy
 * @param array $set : Le contenu des champs à mettre en base
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id créé
 */
function crud_gis_update_dist($id, $set = null) {
	$id_gis = sql_getfetsel('id_gis', 'spip_gis', 'id_gis=' . intval($id));
	if (!$id_gis) {
		$err = _T('gis:erreur_gis_inconnu', array('id'=>$id));
	} elseif (autoriser('modifier', 'gis', $id)) {
		$err = gis_modifier($id, $set);
	} else {
		$err = _L('update error');
	}
	return array('success'=>($err && strlen($err)>0)?false:true,'message'=>$err,'result'=>array('id'=>$id));
}

/**
 * Delete :
 * Supprime un point géolocalisé
 *
 * @param $dummy
 * @param int $id : L'identifiant numérique du point à supprimer
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id supprimé
 */
function crud_gis_delete_dist($id) {
	if (autoriser('supprimer', 'gis', $id)) {
		$err = gis_supprimer($id);
	}
	return array('success' => is_numeric($err) ? true : false, 'message' => $err, 'result' => array('id' => $id));
}
