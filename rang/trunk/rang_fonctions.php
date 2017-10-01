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

include_spip('inc/rang_api');

/**
 * Surcharge de la balise `#RANG`
 * 
 * Appelle balise_RANG_dist(), mais renvoie une chaine vide si le rang est null ou zéro
 * 
 */
function balise_RANG($p) {
	$p = balise_RANG_dist($p);
	
	$p->code = "(intval($p->code) == 0 ? '' : $p->code)";
	
	return $p;
}

/**
 * Détecte si l'objet a été selectionné dans la configuration du plugin
 *
 * @param string $objet
 *     article, rubrique, etc.
 *
 * @return bool
 *
 **/
function rang_objet_dans_config($objet) {
	$table = table_objet_sql($objet);
	$liste = explode(',', lire_config('rang/rang_objets'));
	return in_array($table, $liste);
}

/**
 * SURCHARGE :Compte le nombre d'objets associés pour chaque type d'objet, liés
 * à un mot clé donné.
 *
 * @pipeline_appel afficher_nombre_objets_associes_a
 *
 * @param int $id_mot
 *     Identifiant du mot clé
 * @param int $id_groupe
 *     Identifiant du groupe parent
 * @return string[]
 *     Tableau de textes indiquant le nombre d'éléments tel que '3 articles'
 **/
function objets_associes_mot($id_mot, $id_groupe) {
	static $occurrences = array();

	// calculer tous les liens du groupe d'un coup
	if (!isset($occurrences[$id_groupe])) {
		$occurrences[$id_groupe] = calculer_utilisations_mots($id_groupe);
	}

	$associes = array();
	$tables = lister_tables_objets_sql();
	foreach ($tables as $table_objet_sql => $infos) {
		$nb = (isset($occurrences[$id_groupe][$table_objet_sql][$id_mot]) ? $occurrences[$id_groupe][$table_objet_sql][$id_mot] : 0);
		if ($nb) {
			$associes[] = objet_afficher_nb($nb, $infos['type']);
		}
	}

	$associes = pipeline(
		'afficher_nombre_objets_associes_a',
		array('args' => array('objet' => 'mot', 'id_objet' => $id_mot),
		'data' => $associes)
	);

	return $associes;

}