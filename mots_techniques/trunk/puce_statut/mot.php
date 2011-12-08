<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher la puce statut d'un mot :
 * en fait juste une icone independante du statut
 * mais change en fonction du groupe : technique ou non.
 *
 * @param int $id
 * @param string $statut
 * @param int $id_rubrique
 * @param string $type
 * @param string $ajax
 * @return string
 */
// http://doc.spip.org/@puce_statut_mot_dist
function puce_statut_mot_dist($id, $statut, $id_groupe, $type, $ajax='', $menu_rapide=_ACTIVER_PUCE_RAPIDE) {
	static $icones = array();
	
	if (!isset($icones[$id_groupe])) {
		$technique = sql_getfetsel('technique', 'spip_groupes_mots', 'id_groupe='.intval($id_groupe));
		if ($technique == 'oui') {
			$icones[$id_groupe] = chemin_image("mot-technique-16.png");
		} else {
			$icones[$id_groupe] = chemin_image("mot-16.png");
		}
	}
	return "<img src='" . $icones[$id_groupe] . "' width='16' height='16' alt=''  />";
}


