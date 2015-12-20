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
 * Calcul de la balise #IDENTIFIANT
 *
 * @example
 * `#IDENTIFIANT{#OBJET,#ID_OBJET}`
 * @balise
 * @return string
 */
function balise_IDENTIFIANT_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else {
		$_id = interprete_argument_balise(2,$p);
	}
	$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$p->code = "identifiant_objet(".$_type.", intval(".$_id."), ".sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Retourne l'identifiant d'un objet
 *
 * @param string $objet
 *     Le type de l'objet
 * @param int $id_objet
 *     L'identifiant de l'objet
 * @return string | null
 */
function identifiant_objet($objet, $id_objet) {

	// Cherchons d'abord si l'objet existe bien
	if ($objet
		and $id_objet = intval($id_objet)
		and include_spip('base/connect_sql')
		and $objet = objet_type($objet)
		and $table_sql = table_objet_sql($objet)
		and $cle_objet = id_table_objet($objet)
		and $ligne = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet")
	){
		// 1) Fonction précise pour ce type d'objet : identifiant_<objet>() dans identifiant/<objet>.php
		if ($fonction = charger_fonction('identifiant', "identifiant/$objet", true)){
			// On passe la ligne SQL en paramètre pour ne pas refaire la requête
			$identifiant = $fonction($id_objet, $ligne);
		}
		// 2) Sinon champ normalisé
		elseif ($ligne['identifiant']) {
			$identifiant = $ligne['identifiant'];
		}
		// 3) Sinon identifiant dans la table spip_identifiants
		else {
			$identifiant = sql_getfetsel('identifiant', 'spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
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
