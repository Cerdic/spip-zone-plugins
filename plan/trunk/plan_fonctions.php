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
	return defined('_PLAN_LIMITER_LISTES') ? _PLAN_LIMITER_LISTES : 10;
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

