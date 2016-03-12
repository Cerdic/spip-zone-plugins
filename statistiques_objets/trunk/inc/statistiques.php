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
 * http://code.spip.net/@statistiques_moyenne
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
	$champ_statut = isset($desc['statut']['champ']) ? $desc['statut']['champ'] : 'statut';
	$statut_publie = isset($desc['statut']['publie']) ? $desc['statut']['publie'] : 'publie';
	$classement[$type] = sql_allfetsel(
		$id_table_objet,
		$table_objet_sql,
		$champ_statut.'='.sql_quote($statut_publie).' AND popularite > 0',
		'',
		"popularite DESC",
		'',
		'',
		$serveur
	);
	$classement[$type] = array_map('reset', $classement[$type]);

	return $classement[$type];
}
