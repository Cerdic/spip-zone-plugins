<?php
/**
 * Ce fichier contient l'ensemble des constantes et des fonctions de gestion des caches de Taxonomie.
 *
 * @package SPIP\TAXONOMIE\CACHE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprime tout ou partie des fichiers cache taxonomiques.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param array|string $caches
 *        Liste des fichiers à supprimer ou vide si tous les fichiers cache doivent être supprimés.
 *        Il est possible de passer un seul fichier comme une chaine.
 *
 * @return boolean
 *        Toujours à `true`.
 */
function cache_taxonomie_supprimer($caches = array()) {

	include_spip('inc/flock');

	if ($caches) {
		$fichiers_cache = is_string($caches) ? array($caches) : $caches;
	} else {
		$fichiers_cache = glob(_DIR_VAR . 'cache-taxonomie/' . '*.*');
	}

	if ($fichiers_cache) {
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
	}

	return true;
}

/**
 * Répertorie les fichiers caches issu de l'utilisation de l'API d'un service donné.
 * La fonction renvoie une description de chaque fichier cache, à savoir, à minima, l'action lancée, le TSN
 * du taxon concerné et le nom du fichier cache.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string $service
 *        Alias en minuscules du service pour lequel on veut lister les caches créés ou chaine vide si on souhaite
 *        tous les caches sans distinction de service.
 *
 * @return array
 *        Tableau des descriptions des fichiers cache créés par le service indexé par le chemin complet de
 *        chaque fichier cache.
 */
function cache_taxonomie_repertorier($service = '') {

	// Initialisation de la liste des descriptions des caches du service
	$descriptions_cache = array();

	// Tableau des taxons pour éviter de faire des appels SQL à chaque cache.
	static $taxons = array();

	// On constitue la liste des services requis par l'appel
	include_spip('inc/taxonomie');
	$services = taxon_lister_services();
	if ($service) {
		if (array_key_exists($service, $services)) {
			$services = array($service => $services[$service]);
		} else {
			$services = array();
		}
	}

	if ($services) {
		foreach ($services as $_service => $_titre) {
			// Récupération des fichiers cache du service.
			$pattern_cache = _TAXONOMIE_CACHE_DIR . $_service . '_*.txt';
			$fichiers_cache = glob($pattern_cache);

			if ($fichiers_cache) {
				foreach ($fichiers_cache as $_fichier_cache) {
					// On raz la description pour éviter de garder des éléments du cache précédent et on initialise avec
					// le nom du fichier qui peut servir d'id, le chemin complet et le service.
					$description = array();
					$description['nom_cache'] = basename($_fichier_cache, '.txt');
					$description['fichier_cache'] = $_fichier_cache;

					// On extrait le service qui sert toujours d'index principal du tableau
					$description['service'] = $_service;
					$description['titre_service'] = $_titre;

					// On décompose le nom pour récupérer l'action et le TSN correspondant ainsi que la langue.
					// Le nom du fichier est composé d'éléments séparés par un underscore. La structure est toujours
					// composée dans l'ordre du service, de l'action et du TSN et peut être complétée par la langue.
					$elements = explode('_', $description['nom_cache']);
					$description['action'] = $elements[1];
					$description['tsn'] = intval($elements[2]);
					if (isset($elements[3])) {
						$description['langue'] = $elements[3];
					}

					// On rajoute le nom scientifique du taxon pour un affichage plus compréhensible
					if (!isset($taxons[$description['tsn']])) {
						// Si pas encore stocké, on cherche le nom scientifique du taxon et on le sauvegarde.
						$where = array('tsn=' . $description['tsn']);
						$taxons[$description['tsn']] = sql_getfetsel('nom_scientifique', 'spip_taxons', $where);
					}
					$description['nom_scientifique'] = $taxons[$description['tsn']];


					// On structure le tableau par service.
					$descriptions_cache[$_service]['titre_service'] = $_titre;
					$descriptions_cache[$_service]['caches'][] = $description;
				}
			}
		}
	}

	return $descriptions_cache;
}
