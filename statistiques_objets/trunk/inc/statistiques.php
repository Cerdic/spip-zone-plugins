<?php
/**
 * Surcharge
 *
 * Fonctions modifiées :
 * - classement_populaires()
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Calculer la moyenne glissante sur un nombre d'echantillons donnes
 *
 * @param int|bool $valeur
 * @param int $glisse
 * @return float
 */
function moyenne_glissante($valeur = false, $glisse = 0) {
	static $v = array();
	// pas d'argument, raz de la moyenne
	if ($valeur === false) {
		$v = array();

		return 0;
	}

	// argument, on l'ajoute au tableau...
	// surplus, on enleve...
	$v[] = $valeur;
	if (count($v) > $glisse) {
		array_shift($v);
	}

	return round(statistiques_moyenne($v), 2);
}

/**
 * Calculer la moyenne d'un tableau de valeurs
 *
 * https://code.spip.net/@statistiques_moyenne
 *
 * @param array $tab
 * @return float
 */
function statistiques_moyenne($tab) {
	if (!$tab) {
		return 0;
	}
	$moyenne = 0;
	foreach ($tab as $v) {
		$moyenne += $v;
	}

	return $moyenne / count($tab);
}

/**
 * Construire un tableau par popularite
 *   classemnt => id_truc
 *
 * Modification : prise en compte du statut de publication propre à chaque type d'objet
 *
 * @param string $type
 * @param string $serveur
 * @return array
 */
function classement_populaires($type, $serveur = '') {

	static $classement = array();
	if (isset($classement[$type])) {
		return $classement[$type];
	}
	include_spip('inc/objets'); // au cas-où
	$table_objet_sql = table_objet_sql($type, $serveur);
	$id_table_objet = id_table_objet($type, $serveur);
	$trouver_table = charger_fonction('trouver_table','base');
	$desc = $trouver_table($table_objet_sql);
	$champ_statut = isset($desc['statut']['champ']) ? $desc['statut']['champ'] : '';
	$statut_publie = isset($desc['statut']['publie']) ? $desc['statut']['publie'] : '';
	$where = array('popularite > 0');
	if (
		$champ_statut
		and $statut_publie
	){
		$where[] = $champ_statut.'='.sql_quote($statut_publie);
	}
	$classement[$type] = sql_allfetsel(
		$id_table_objet,
		$table_objet_sql,
		$where,
		'',
		"popularite DESC",
		'',
		'',
		$serveur
	);
	$classement[$type] = array_column($classement[$type], $id_table_objet);

	return $classement[$type];
}


/**
 * Identifier l'objet éditorial du contexte d'appel de la page en cours,
 * s'il s'agit de la page d'un objet éditorial.
 *
 * On se repose sur la clé `page` (ou `type-page` avec Zcore), sinon sur les clés `id_{objet}`.
 *
 * @param array $contexte
 *     Contexte d'appel de la page, retrouvé automatiquement sinon.
 * @return array
 *     - Couples ['objet' => 'patate'] ['id_objet' => N] si c'est la page d'un objet éditorial
 *     - `false` si ce n'est pas la page d'un objet éditorial, ou qu'on n'a pas pu l'identifier
 */
function identifier_objet_contexte($contexte = array()) {

	include_spip('base/objets');

	// fallback si autre page, ou pas d'objet trouvé
	$objet_contexte = false;

	// fallback contexte
	if (!$contexte
		and isset($GLOBALS['contexte'])
	) {
		$contexte = $GLOBALS['contexte'];
	}

	// Page courante dans le contexte : clés `page` ou `type-page` (Zcore).
	if (isset($contexte['page'])) {
		$page = $contexte['page'];
	} elseif (isset($contexte['type-page'])) {
		$page = $contexte['type-page'];
	}

	// Cas 1 : le contexte indique la page courante.
	// =============================================
	// Si on trouve également la clé `id_{objet}`, alors c'est la page d'un objet éditorial.
	if (isset($page)
		and $objet = objet_type($page)
		and $id_table_objet = id_table_objet($objet)
		and isset($contexte[$id_table_objet])
		and $id_objet = $contexte[$id_table_objet]
	) {
		$objet_contexte = array('objet' => $objet, 'id_objet' => $id_objet);
	}

	// Cas 2 : pas de chance, le contexte n'indique pas la page courante.
	// ==================================================================
	// On se base sur les clés `id_{objet}` trouvées dans le contexte.
	// s'il y a 1 clé, ou 2 clés dont 1 id_rubrique, on peut en déduire `id_{objet}`.
	// Dans les autres cas, on est coincés !
	if (!isset($page)) {

		// récupérer les clés `id_{objet}`, et identifier celle de l'objet si on peut.
		$ids_tables_objets = preg_grep('/^id_.*/', array_keys($contexte));
		$nb_ids = count($ids_tables_objets);
		// 1 clé : c'est celle de l'objet
		if ($nb_ids === 1) {
			$id_table_objet = $ids_tables_objets[0];
		}
		// 2 clés : si l'une d'elle est `id_rubrique`, l'autre est celle de l'objet
		if ($nb_ids === 2
			and in_array('id_rubrique', $ids_tables_objets)
		) {
			$k_id_rubrique  = array_search('id_rubrique', $ids_tables_objets);
			$k_ids          = array_keys($ids_tables_objets);
			unset($k_ids[$k_id_rubrique]);
			$k_ids          = array_values($k_ids); //raz des clés
			$k_id_objet     = $k_ids[0];
			$id_table_objet = $ids_tables_objets[$k_id_objet];
		}

		// On a identifié un `id_{objet}` : on peut en déduire l'objet
		if (isset($id_table_objet)
			and isset($contexte[$id_table_objet])
		) {
			$objet          = objet_type($id_table_objet);
			$id_objet       = $contexte[$id_table_objet];
			$objet_contexte = array('objet' => $objet, 'id_objet' => $id_objet);
		}

	}

	return $objet_contexte;
}
