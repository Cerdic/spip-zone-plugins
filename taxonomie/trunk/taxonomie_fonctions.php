<?php
/**
 * Ce fichier contient les fonctions d'API du plugin Taxonomie utilisées comme filtre dans les squelettes.
 * Les autres fonctions de l'API sont dans le fichier `inc/taxonomie`.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fournit l'ascendance taxonomique d'un taxon donné, par consultation dans la base de données.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param int    $id_taxon
 *        Id du taxon pour lequel il faut fournir l'ascendance.
 * @param int    $tsn_parent
 *        TSN du parent correspondant au taxon id_taxon. Ce paramètre permet d'optimiser le traitement
 *        mais n'est pas obligatoire. Si il n'est pas connu lors de l'appel il faut passer `null`.
 * @param string $ordre
 *        Classement de la liste des taxons : `descendant`(défaut) ou `ascendant`.
 *
 * @return array
 *        Liste des taxons ascendants. Chaque taxon est un tableau associatif contenant les informations
 *        suivantes : `id_taxon`, `tsn_parent`, `nom_scientifique`, `nom_commun`, `rang`, `statut` et l'indicateur
 *        d'espèce `espèce`.
 */
function taxon_informer_ascendance($id_taxon, $tsn_parent = null, $ordre = 'descendant') {

	$ascendance = array();

	// Si on ne passe pas le tsn du parent correspondant au taxon pour lequel on cherche l'ascendance
	// alors on le cherche en base de données.
	// Le fait de passer ce tsn parent est uniquement une optimisation.
	if (is_null($tsn_parent)) {
		$tsn_parent = sql_getfetsel('tsn_parent', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	while ($tsn_parent > 0) {
		$select = array('id_taxon', 'tsn_parent', 'nom_scientifique', 'nom_commun', 'rang_taxon', 'statut', 'espece');
		$where = array('tsn=' . intval($tsn_parent));
		$taxon = sql_fetsel($select, 'spip_taxons', $where);
		if ($taxon) {
			$ascendance[] = $taxon;
			$tsn_parent = $taxon['tsn_parent'];
		}
	}

	if ($ascendance	and ($ordre == 'descendant')) {
		$ascendance = array_reverse($ascendance);
	}

	return $ascendance;
}


/**
 * Fournit les phrases de crédit des sources d'information ayant permis de compléter le taxon.
 * La référence ITIS n'est pas répétée dans le champ `sources` de chaque taxon car elle est
 * à la base de chaque règne. Elle est donc insérée par la fonction.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param int    $id_taxon
 *        Id du taxon pour lequel il faut fournir les crédits
 * @param string $sources_specifiques
 *        Tableau sérialisé des sources possibles autres qu'ITIS (CINFO, WIKIPEDIA...) telles qu'enregistrées
 *        en base de données dans le champ `sources`.
 *        Ce paramètre permet d'optimiser le traitement mais n'est pas obligatoire.
 *
 * @return array
 *        Tableau des phrases de crédits indexées par source.
 */
function taxon_crediter($id_taxon, $sources_specifiques = null) {

	$sources = array();

	// Si on ne passe pas les sources du taxon concerné alors on le cherche en base de données.
	// Le fait de passer ce champ sources est uniquement une optimisation.
	if (is_null($sources_specifiques)) {
		$sources_specifiques = sql_getfetsel('sources', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	// On merge ITIS et les autres sources
	$liste_sources = array('itis' => array());
	if ($sources_specifiques) {
		$liste_sources = array_merge($liste_sources, unserialize($sources_specifiques));
	}

	// Puis on construit la liste des sources pour l'affichage
	foreach ($liste_sources as $_service => $_infos_source) {
		include_spip("services/${_service}/${_service}_api");
		if (function_exists($citer = "${_service}_credit")) {
			$sources[$_service] = $citer($id_taxon, $_infos_source);
		}
	}

	return $sources;
}


/**
 * Affiche la puce de statut d'un taxon sans proposer le formulaire de changement de statut.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param string $statut
 *        Statut du taxon, `prop`, `publie`ou `poubelle`.
 * @param int    $id_taxon
 *        Id du taxon.
 *
 * @return array
 *        Image de la puce.
 */
function taxon_afficher_statut($statut, $id_taxon = 0) {

	// On évite de charger la fonction n fois.
	static $afficher_puce = null;

	if (!$afficher_puce) {
		// Chargement de la fonction d'affichage
		$afficher_puce = charger_fonction('puce_statut', 'inc');
	}

	// On affiche la puce sans proposer le formulaire rapide de changement de statut qui pose un problème avec
	// l'ajax sachant qu'un changement peut en provoquer d'autres, la liste n'est plus à jour.
	$puce = $afficher_puce($id_taxon, $statut, 0, 'taxon', false, false);

	return $puce;
}
