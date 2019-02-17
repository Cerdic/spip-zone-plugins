<?php
/**
 * Ce fichier contient la fonction standard de chargement et fourniture des données météo.
 * Elle s'applique à tous les services et à tous les types de données.
 *
 * @package SPIP\SPIPERIPSUM\CACHE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoyer le contenu du fichier cache des données d'évangile à la date et à la langue choisies
 * après l'avoir éventuellement mis à jour.
 *
 * @uses cache_est_valide()
 * @uses cache_ecrire()
 * @uses cache_lire()
 *
 * @param string $langue
 *        Le lieu concerné par la méteo exprimé selon les critères requis par le service.
 * @param string $jour
 *        Le type de données météorologiques demandé :
 *            - `conditions`, la valeur par défaut
 *            - `previsions`
 *            - `infos`
 * @param string $service
 *        Le nom abrégé du service :
 *            - `evangelizo` pour le service Evangelizo.
 *
 * @return array
 *        Le contenu du fichier cache contenant les données à jour demandées.
 */
function inc_spiperipsum_charger_dist($langue, $jour, $service = 'evangelizo') {

	// Traitement des cas des arguments facultatifs passés vides (ce qui est différent de non passés à l'appel)
	if (!$service) {
		$service = 'evangelizo';
	}

	// Vérification de la date fournie
	$date = ($jour == _SPIPERIPSUM_JOUR_DEFAUT) ? date('Y-m-d') : $jour;

	// En fonction du service, on inclut le fichier d'API.
	// Le principe est que chaque service propose la même liste de fonctions d'interface dans un fichier unique.
	include_spip("services/${service}");

	// Utilisation de la fonction de chargement du service.
	$coder = "${service}_coder_langue";
	$code_langue = $coder($langue);

	// -- Constituer le tableau minimal
	include_spip('inc/cache');
	$cache = array(
		'sous_dossier' => $service,
		'date'         => $date,
		'langage'      => $code_langue
	);

	// Mise à jour du cache avec les nouvelles données si:
	// - le fichier cache n'existe pas
	// - la période de validité du cache est échue
	if ((!$fichier_cache = cache_est_valide('spiperipsum', $cache))
	or (defined('_SPIPERIPSUM_FORCER_CHARGEMENT') ? _SPIPERIPSUM_FORCER_CHARGEMENT : false)) {
		// Utilisation de la fonction de chargement du service.
		$charger = "${service}_charger";
		$tableau = $charger($code_langue, $date);

		// Mise à jour du cache
 		cache_ecrire('spiperipsum', $cache, $tableau);
	} else {
		// Lecture des données du fichier cache valide
		$tableau = cache_lire('spiperipsum', $fichier_cache);
	}

	return $tableau;
}
