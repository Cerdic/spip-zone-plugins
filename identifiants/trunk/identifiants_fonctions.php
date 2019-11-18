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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Retourne une liste des tables pour lesquelles on peut gérer les identifiants.
 *
 * Ce sont tout simplement les tables sélectionnées dans la config.
 * Pour être extra sûrs, on retire les tables ayant nativement la colonne `identifiant`,
 * bien qu'elles ne soient en théorie pas sélectionnables dans la config.
 *
 * @note
 * On la met ici plutôt que dans inc/identifiants.php,
 * car elle est utilisée dans declarer_tables_objets_sql,
 * et donc ça évite de tout charger en permanence.
 *
 * @return Array
 */
function identifiants_lister_tables_identifiables() {

	static $tables;
	if (is_array($tables)) {
		return $tables;
	}

	include_spip('inc/config');
	$tables_selectionnees = array_filter(lire_config('identifiants/objets', array()));
	$tables_natives       = identifiants_lister_tables_natives();
	$tables               = array_diff($tables_selectionnees, $tables_natives);

	return $tables;
}


/**
 * Renvoie la liste des tables disposant nativement d'une colonne `identifiant`
 *
 * @note
 * On la met ici plutôt que dans inc/identifiants.php,
 * car elle est utilisée dans declarer_tables_objets_sql,
 * et donc ça évite de tout charger en permanence.
 *
 * @return Array
 */
function identifiants_lister_tables_natives() {

	include_spip('inc/config');
	$tables_repertoriees = lire_config('identifiants/tables_repertoriees', array());
	$tables_natives      = array_keys(array_filter($tables_repertoriees));

	return $tables_natives;
}


/**
 * Balise IDENTIFIANT
 *
 * Retourne l'identifiant de l'objet du contexte
 * ou l'identifiant de l'objet,id_objet passé en paramètre
 */
function balise_IDENTIFIANTZ_dist($p) {
	if (!$_objet = interprete_argument_balise(1, $p)) {
		$_objet = objet_type($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary, $p);
	} else {
		$_objet = interprete_argument_balise(1, $p);
		$_id = interprete_argument_balise(2, $p);
	}

	$p->code =  'generer_info_entite(' . $_id . ',"' . $_objet . '", "identifiant")';
	$p->interdire_scripts = false;

	return $p;
}


// ====================
// FONCTIONS DEPRECIÉES
// ====================


/**
 * Retourne l'identifiant d'un objet
 *
 * @deprecated version 2.0.0
 * @uses generer_info_entite()
 * @param string $objet
 *     Le type de l'objet
 * @param int $id_objet
 *     L'identifiant numérique de l'objet
 * @return String|Null
 */
function identifiant_objet($objet, $id_objet) {
	include_spip('inc/filtres');
	return generer_info_entite($id_objet, $objet, 'identifiant');
}
