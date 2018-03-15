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
 * @param string $rubrique optionel
 *     dans quelle rubrique faire le classement
 * @return array
 *     note : sans ce return (a priori inutile, la fonction plante (???)
**/
function action_trier_items_dist() {

	include_spip('base/objets');

	$tab		= _request('trier');
	$page 		= _request('debut_liste');
	$objet		= _request('objet');
	$id_parent	= _request('id_parent');

	$table		= table_objet_sql($objet);
	$definition_table = lister_tables_objets_sql($table);
	$id_objet_parent = $definition_table['parent']['0']['champ'];
	$id_objet	= id_table_objet($objet);

	// reclassement !
	foreach ($tab as $key => $value) {
		$rang	= $page + $key + 1; // le classement commence à 1, pas à 0
		$id		= intval(substr($value, 3));
		if ($id_parent == 'rien') {
			$where = "$id_objet=$id";
		}
		else {
			$where = "$id_objet=$id AND $id_objet_parent=$id_parent";
		}
		sql_updateq($table, array('rang' => $rang), $where);
	}
	$msg = 'ok';

	include_spip('inc/invalideur');
	suivre_invalideur("rang");

	return $msg;
}

/**
 * supprimer un mot, puis réordonner
 *
 * @param string $id
 *     id de la forme mot_xx
 * @return array
 *     note : sans ce return (a priori inutile, la fonction plante (???)
**/
function rang_supprimer_item($id) {
	// suppression du mot
	$new_id = intval(substr($id, 4));
	sql_delete('spip_mots', "id_mot=$new_id");

	//récupérer le tableau id/rang actuel
	$id_grp = sql_getfetsel('id_groupe', 'spip_groupes_mots');
	$res = sql_select('id_mot', 'spip_mots', "id_groupe=$id_grp", '', 'rang');
	while ($tab = sql_fetch($res)){
		$new_tab[] = 'id_mot_'.$tab['id_mot'];
	}

	//  réordonnement !
	$tab_result = rang_trier_items($new_tab);

	return $tab_result;
}
