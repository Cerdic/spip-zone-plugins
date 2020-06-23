<?php
/**
 * Ce fichier contient l'ensemble des fonctions de service spécifiques à une ou plusieurs collections.
 *
 * @package SPIP\ISOCODE\EZCOLLECTION\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['isocode']['pays']['champs'] = array(
	'code_alpha2'     => 'code',
	'code_alpha3'     => 'code_a3',
	'code_num'        => 'code_num',
	'label'           => 'nom',
	'capital'         => 'capitale',
	'area'            => 'superficie',
	'population'      => 'population',
	'code_continent'  => 'continent',
	'code_num_region' => 'zone',
	'tld'             => 'tld',
	'code_4217_3'     => 'code_devise',
	'currency_en'     => 'nom_devise',
	'phone_id'        => 'indicatif_uit'
);

// -----------------------------------------------------------------------
// -------------------------- COLLECTION PAYS ----------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des pays de la table spip_iso3166countries éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 *
 * @param array $conditions    Conditions à appliquer au select
 * @param array $filtres       Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration Configuration de la collection utile pour savoir quelle fonction appeler pour
 *                             construire chaque filtre.
 *
 * @return array Tableau des plugins dont l'index est le préfixe du plugin.
 *               Les champs de type id ou maj ne sont pas renvoyés.
 */
function pays_collectionner($conditions, $filtres, $configuration) {

	// Initialisation de la collection
	$pays = array();

	// Récupérer la liste des pays (filtrée ou pas).
	// Si la liste est filtrée par continent ou région, on renvoie aussi les informations sur ce continent ou
	// cette région.
	$from = array('spip_iso3166countries');
	// -- Tous le champs sauf les labels par langue et la date de mise à jour.
	$description_table = sql_showtable('spip_iso3166countries');
	$champs = array_keys($description_table['field']);
	$champs = array_diff($champs, array('label_fr', 'label_en', 'maj'));
	// -- Traduire les champs de Isocode en champs pour Géographie
	$select = array();
	foreach ($champs as $_champ) {
		$select[] = "${_champ} as {$GLOBALS['isocode']['pays']['champs'][$_champ]}";
	}

	// -- Initialisation du where avec les conditions sur la table des dépots.
	$where = array();
	// -- Si il y a des critères additionnels on complète le where en conséquence en fonction de la configuration.
	if ($conditions) {
		$where = array_merge($where, $conditions);
	}

	$pays['pays'] = sql_allfetsel($select, $from, $where);

	return $pays;
}

/**
 * Détermine si la valeur du critère de région d'appartenance du pays est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec un code à 3 chiffres.
 *
 * @param string $zone   La valeur du critère région, soit son code ISO 3166-1 numérique (3 chiffres).
 * @param array  $erreur Bloc d'erreur préparé au cas où la vérification retourne une erreur. Dans ce cas, le bloc
 *                       et complété et renvoyé.
 *
 * @return bool `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_zone($zone, &$erreur) {
	$est_valide = true;

	if (!preg_match('#^[0-9]{3}$#', $zone)) {
		$est_valide = false;
		$erreur['type'] = 'zone_nok';
	}

	return $est_valide;
}

/**
 * Détermine si la valeur du continent d'appartenance du pays est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec une code à deux lettres
 * majuscules.
 *
 * @param string $continent La valeur du critère région, soit son code ISO 3166-1 numérique (3 chiffres).
 * @param array  $erreur    Bloc d'erreur préparé au cas où la vérification retourne une erreur. Dans ce cas, le bloc
 *                          et complété et renvoyé.
 *
 * @return bool `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_continent($continent, &$erreur) {
	$est_valide = true;

	if (!preg_match('#^[A-Z]{2}$#', $continent)) {
		$est_valide = false;
		$erreur['type'] = 'continent_nok';
	}

	return $est_valide;
}
