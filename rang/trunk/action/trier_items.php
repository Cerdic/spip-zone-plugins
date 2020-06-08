<?php
/**
 * Fonctions utiles au plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * réordonner les rangs de la liste suite à un nouveau classement
 *
 * @param array $tab
 *     tableau des items de la liste suite à une modification du classement
 * @param string $page
 * 		quelle pagination
 * @param string $objet
 *     sur quel objet faire le classement
 * @param string $id_parent
 *     id_parent dans lequel faire le classement
 *
**/
function action_trier_items_dist() {

	include_spip('base/objets');
	include_spip('base/objets_parents');

	$tab		= _request('trier');
	$page 		= intval(_request('debut_liste'));
	$objet		= _request('objet');
	$id_parent	= _request('id_parent');

	$table		= table_objet_sql($objet);
	$id_objet	= id_table_objet($objet);
	$objet_type = objet_type($objet);
	if ($id_parent != 'rien') {
		$parent = type_objet_info_parent($objet_type);
		// on peut avoir plusieurs parents dans certains cas, mais on prent le premier par défaut
		$champ_parent = $parent['0']['champ'];
	}

	spip_log("\nobjet : ".$objet."\nid_parent : ".$id_parent."\nparent : ".$champ_parent."\ntrier :\n".print_r($tab,1), 'rang.' . _LOG_DEBUG);

	// reclassement !
	foreach ($tab as $key => $value) {
		$rang	= $page + $key + 1; // le classement commence à 1, pas à 0
		$id		= intval($value);
		if ($champ_parent) {
			$where = "$id_objet=$id AND $champ_parent=$id_parent";
		} else {
			$where = "$id_objet=$id";
		}
		$res = sql_updateq($table, array('rang' => $rang), $where);
		spip_log($where.' : '.$res, 'rang.' . _LOG_DEBUG);
	}

	include_spip('inc/invalideur');
	suivre_invalideur($objet.'/*');

}
