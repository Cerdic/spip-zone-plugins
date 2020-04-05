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
	 	$parent	= type_objet_info_parent($objet_type);
		$parent_champ = $parent['champ'];
	}

	spip_log("\nobjet : ".$objet."\nid_parent : ".$id_parent."\nparent : ".$parent_champ."\ntrier".print_r($tab,1), 'rang.' . _LOG_DEBUG);

	// reclassement !
	foreach ($tab as $key => $value) {
		$rang	= $page + $key + 1; // le classement commence à 1, pas à 0
		$id		= intval($value);
		if (!$id_parent || $id_parent == 'rien') {
			$where = "$id_objet=$id";
		}
		else {
			$where = "$id_objet=$id AND $parent_champ=$id_parent";
		}
		$res = sql_updateq($table, array('rang' => $rang), $where);
		spip_log($res, 'rang.' . _LOG_DEBUG);
	}

	include_spip('inc/invalideur');
	suivre_invalideur($objet.'/*');

}
