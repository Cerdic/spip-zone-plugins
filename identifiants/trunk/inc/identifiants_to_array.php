<?php
/**
 * Itérateur « identifiants » du plugin Identifiants
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie une liste des pages et le chemin de leurs squelettes.
 *
 * @param String $table
 *     Nom d'une table ou d'un objet pour restreindre la sélection
 * @param String $par
 *     Tri
 * @param String|bool $sens
 *     1 : ascendant
 *     0 : descendant
 * @return array
 *     Tableau associatif issu de la requête SQL
 */
function inc_identifiants_to_array_dist($table = '', $par = '', $sens = 1) {

	include_spip('base/abstract_sql');
	include_spip('base/objets');
	$res                  = array();
	$tables_identifiables = identifiants_lister_tables_identifiables();
	$table                = $table ? array(table_objet_sql($table)) : $tables_identifiables;
	$tables               = array_intersect($table, $tables_identifiables);
	$trouver_table        = charger_fonction('trouver_table', 'base');
	$sens                 = $sens ? 'ASC' : 'DESC';

	foreach ($tables as $table) {
		$desc        = $trouver_table($table);
		$objet       = objet_type($table);
		$cle_objet   = id_table_objet($objet);
		$nom_objet   = _T($desc['texte_objet']);
		$champ_titre = isset($desc['titre']) ? $desc['titre'] : '';
		$select      = array(
			'identifiant',
			sql_quote($objet).' AS objet',
			sql_quote($nom_objet).' AS nom_objet',
			$cle_objet.' AS id_objet'
		);
		$from        = array($table);
		$where       = array('identifiant != ' . sql_quote(''));
		$orderby     = array();
		$groupby     = array($cle_objet);
		if ($champ_titre) {
			$select[] = $champ_titre;
		}
		if (isset($desc['field'][$par])) {
			$orderby[] = $par . ' ' . $sens;
		}
		$res_table = sql_allfetsel($select, $from, $where, $groupby, $orderby);
		$res = array_merge($res, $res_table);
	}

	// TODO : essayer de faire un order by global si possible ?

	return $res;
}
