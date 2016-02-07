<?php
/**
 * fonctions utiles au plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Fonctions
 */


/**
 * Retourne l'identifiant d'un objet
 *
 * On cherche dans la table de l'objet si une colonne `identifiant` existe,
 * sinon on va voir dans la table `spip_identifiants`.
 *
 * @param string $objet
 *     Le type de l'objet
 * @param int $id_objet
 *     L'identifiant numérique de l'objet
 * @return string | null
 */
function identifiant_objet($objet, $id_objet) {

	include_spip('base/connect_sql');

	// Cherchons d'abord si l'objet existe bien
	if (
		$objet
		and $id_objet  = intval($id_objet)
		and $objet     = objet_type($objet)
		and $table_sql = table_objet_sql($objet)
		and $cle_objet = id_table_objet($objet)
		and $ligne     = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet")
	){
		// soit c'est un champ normalisé de la table de l'objet
		if (isset($ligne['identifiant'])) {
			$identifiant = $ligne['identifiant'];
		}
		// sinon on cherche dans la table spip_identifiants
		else {
			$identifiant = sql_getfetsel('identifiant', 'spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.$id_objet);
		}
	}

	return $identifiant;
}


/**
 * Retourne une liste de tables possédant une colonne « identifiant »
 *
 * @return Array
 */
function tables_avec_identifiant() {

	include_spip('base/objets');

	$tables_avec_identifiant = array();

	if ($tables = lister_tables_objets_sql()) {
		foreach($tables as $table=>$infos) {
			if (array_key_exists('identifiant', $infos['field'])) {
				$tables_avec_identifiant[] = $table;
			}
		}
	}
	return $tables_avec_identifiant;
}
