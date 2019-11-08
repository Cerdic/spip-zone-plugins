<?php
/**
 * Ce fichier contient l'ensemble des fonctions de service spécifiques à une ou plusieurs collections.
 *
 * @package SPIP\ISOCODE\EZCOLLECTION\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// -------------------------- COLLECTION PAYS ----------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des plugins de la table spip_plugins éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 * Chaque objet plugin est présenté comme un tableau dont tous les champs sont accessibles comme un
 * type PHP simple, entier, chaine ou tableau.
 *
 * @uses plugin_normaliser_champs()
 *
 * @param array $filtres
 *      Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration
 *      Configuration de la collection plugins utile pour savoir quelle fonction appeler pour construire chaque filtre.
 *
 * @return array
 *      Tableau des plugins dont l'index est le préfixe du plugin.
 *      Les champs de type id ou maj ne sont pas renvoyés.
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
	$select = array_keys($description_table['field']);
	$select = array_diff($select, array('label_fr', 'label_en', 'maj'));

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
 * @param string $region
 *        La valeur du critère région, soit son code ISO 3166-1 numérique (3 chiffres).
 * @param string $extra
 *        Message complémentaire à renvoyer dans la réponse en cas d'erreur.
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_region($region, &$erreur) {

	$est_valide = true;

	if (!preg_match('#^[0-9]{3}$#', $region)) {
		$est_valide = false;
		$erreur['type'] = 'region_nok';
	}

	return $est_valide;
}


/**
 * Détermine si la valeur du continent d'appartenance du pays est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec une code à deux lettres
 * majuscules.
 *
 * @param string $prefixe
 *        La valeur du préfixe
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_continent($continent, &$erreur) {

	$est_valide = true;

	if (!preg_match('#^[A-Z]{2}$#', $continent)) {
		$est_valide = false;
		$erreur['type'] = 'continent_nok';
	}

	return $est_valide;
}
