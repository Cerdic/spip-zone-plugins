<?php
/**
 * Gestion de l'action de suppression de niveau de Xiti
 *
 * @package SPIP\Xiti\Action
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/autoriser');

/**
 * Supprimer définitivement un niveau 2 de Xiti
 *
 * @param int $id_xiti_niveau identifiant numérique du niveau 2
 * @return int|false 0 si réussite, false dans le cas ou le niveau n'existe pas
 */
function xiti_niveau_supprimer($id_xiti_niveau) {
	$valide = sql_getfetsel('id_xiti_niveau', 'spip_xiti_niveaux', 'id_xiti_niveau='.intval($id_xiti_niveau));
	if ($valide && autoriser('supprimer', 'xiti_niveau', $valide)) {
		sql_delete('spip_xiti_niveaux_liens', 'id_xiti_niveau='.intval($id_xiti_niveau));
		sql_delete('spip_xiti_niveaux', 'id_xiti_niveau='.intval($id_xiti_niveau));
		$id_xiti_niveau = 0;
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_xiti_niveau/$id_xiti_niveau'");
		return $id_xiti_niveau;
	}
	return false;
}
