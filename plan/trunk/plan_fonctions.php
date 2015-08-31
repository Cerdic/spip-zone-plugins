<?php
/**
 * Fonctions pour le plugin Plan du site dans l’espace privé
 *
 * @plugin     Plan du site dans l’espace privé
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Plan\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Retourne le nombre d'éléments d'une liste d'objet qui fait qu'on
 * n'affiche pas le contenu par défaut, mais seulement en ajax
 * après clic…
 *
 * @return int nombre
**/
function plan_limiter_listes() {
	return defined('_PLAN_LIMITER_LISTES') ? _PLAN_LIMITER_LISTES : 50;
}

/**
 * Pour SPIP 3.0, on chargera un jquery plus récent… la loose !
 *
 * @return bool
**/
function plan_charger_jquery_recent() {
	return version_compare($GLOBALS['spip_version_branche'], '3.1.0-dev', '<');
}

/**
 * Trouve les objets qui peuvent s'afficher dans le plan de page, dans une rubrique
 *
 * @return array [table -> chemin du squelette]
**/
function plan_lister_objets_rubrique() {
	static $liste = null;
	if (is_null($liste)) {
		$liste = array();
		$tables = lister_tables_objets_sql();
		unset($tables['spip_rubriques']);
		foreach ($tables as $cle => $desc) {
			if (isset($desc['field']['id_rubrique'])) {
				if (trouver_fond('prive/squelettes/inclure/plan2-' . $desc['table_objet'])) {
					$liste[$cle] = $desc['table_objet'];
				}
			}
		}
	}

	return $liste;
}

/**
 * Trouve les objets qui peuvent s'afficher dans le plan de page, dans une rubrique
 * ainsi que leurs statuts utilisables
 *
 * @return array
**/
function plan_lister_objets_rubrique_statuts() {
	static $liste = null;
	if (is_null($liste)) {
		$objets = plan_lister_objets_rubrique();
		include_spip('inc/session');
		include_spip('inc/puce_statut');
		$liste = array();
		foreach ($objets as $table => $null) {
			$desc = lister_tables_objets_sql($table);
			$statuts = array_keys($desc['statut_textes_instituer']);
			if ($table == 'spip_articles') {
				$autorises = statuts_articles_visibles(session_get('statut'));
				$statuts = array_intersect($statuts, $autorises);
			}
			$objet = $desc['table_objet'];
			// obtenir titre et image du statut
			$_statuts = array();
			foreach ($statuts as $statut) {
				$_statuts[$statut] = array(
					'image' => statut_image($objet, $statut),
					'titre' => statut_titre($objet, $statut),
				);
			}
			$liste[ $objet ] = $_statuts;
		}
	}
	return $liste;
}
