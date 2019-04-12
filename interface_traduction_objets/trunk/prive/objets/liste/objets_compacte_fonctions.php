<?php
/**
 * Fonctions pour le plugin
 *
 * @plugin     Interface de traduction pour objets
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Interface_traduction_objets\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function liste_compacte_requete_objet($contexte) {
	include_spip('action/editer_liens');

	if (!is_array($contexte)) {
		$contexte = unserialize($contexte);
	}
	$objet = $contexte['objet'];
	$id_objet = $contexte['id_objet'];
	$table_objet_sql = $contexte['table_objet_sql'];
	$id_table_objet = $contexte['id_table_objet'];
	$champ = [$id_table_objet . ' as id'];
	$from = $table_objet_sql;
	$where = [];
	$left_join = [];
	$join = '';

	// Existence d'un champ date.
	if (isset($contexte['champ_date'])) {
		$champ[] = $contexte['champ_date'] . ' as date';
	}

	// Existence d'un champ rang.
	if (isset($contexte['champ_rang'])) {
		$champ[] = $contexte['champ_rang'];
	}

	/*
	* Des requêtes conditionnelles dépendant du contexte.
	*/
	// Page auteur.
	if (isset($contexte['id_auteur'])) {
		if (isset($desc['field']['id_auteur'])) {
			$where[] = 'id_auteur=' . $contexte['id_auteur'];
		}
		else {
			$left_join[] = 'spip_auteurs_liens';
			$where[] = 'objet LIKE ' . sql_quote($objet) . ' AND id_auteur=' . $contexte['id_auteur'];
		}
	}

	// Autres liaisons
	elseif ($objet_associable = objet_associable($exec)) {
		$table_liens = $objet_associable[1];
		$id_table_liens = $objet_associable[0];

		$left_join[] = $table_liens;
		$where[] = $table_liens . '.objet LIKE ' . sql_quote($objet) . ' AND ' . $table_liens . '.' . $id_table_liens . '=' . $contexte[$id_table_liens];
	}

	$on = '';
	if (count($left_join) > 0) {
		foreach ($left_join AS $table_jointure) {
			$on = ' ON ' . $table_objet_sql . '.' . $id_table_objet . '=' . $table_jointure . '.id_objet';
			$join .= ' LEFT JOIN ' . $table_jointure . $on;
		}
	}

	// Si on est dans une rubrique on prend les objets de la rubrique
	if (isset($contexte['id_rubrique'])) {
		$where[] = $table_objet_sql . '.id_rubrique=' . $contexte['id_rubrique'];
	}


	// Si pas dans une rubrique ou secteur_langue pas activé,
	// on prend les objets non traduits et ceux de références si traduit.
	if (!isset($contexte['id_rubrique']) OR !test_plugin_actif('secteur_langue')){
		$objets = sql_allfetsel(
			'id_trad,' . $id_table_objet,
			$from . $join,
			$where,
			'',
			$id_table_objet . ' desc');

		$id_objets = [];
		foreach ($objets AS $row) {
			$id_trad = $row['id_trad'];
			$id_objet = $row[$id_table_objet];
			if ($id_trad > 0 and $id_trad == $id_objet) {
				$id_objets[$id_trad] = $id_objet;
			}
			elseif ($id_trad == 0) {
				$id_objets[$id_objet] = $id_objet;
			}
		}
		if (count($id_objets) == 0) {
			$id_objets = [-1];
		}
		$where[] = $table_objet_sql . '.' .$id_table_objet . ' IN (' . implode(',', $id_objets) . ')';
	}

	// On passe le résultat de la requête dans le contexte.
	return sql_allfetsel($champ, $from . $join, $where, '', id_table_objet($objet) . ' desc');
}
