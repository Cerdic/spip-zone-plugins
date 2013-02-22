<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Formulaire de création et d'édition d'un point géolocalisé
 */

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Chargement des valeurs par défaut du formulaire
 * 
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $recherche
 */
function formulaires_rechercher_gis_charger_dist($objet='', $id_objet='', $retour='', $recherche=''){
	$valeurs['recherche_gis'] = _request('recherche_gis');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	return $valeurs;
}

/**
 * Vérification des valeurs du formulaire
 * 
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $recherche
 */
function formulaires_rechercher_gis_verifier_dist($objet='', $id_objet='', $retour='', $recherche=''){
	return $erreurs;
}

/**
 * Traitement des valeurs du formulaire
 * 
 * @param string $objet Le type d'objet SPIP auquel il est attaché
 * @param int $id_objet L'id_objet de l'objet auquel il est attaché
 * @param string $retour L'url de retour
 * @param string $recherche
 */
function formulaires_rechercher_gis_traiter_dist($objet='', $id_objet='', $retour='', $recherche=''){
	return;
}

?>