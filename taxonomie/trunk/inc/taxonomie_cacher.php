<?php
/**
 * Ce fichier contient l'ensemble des constantes et des fonctions de gestion des caches de Taxonomie.
 *
 * @package SPIP\TAXONOMIE\CACHE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_TAXONOMIE_CACHE_NOMDIR')) {
	/**
	 * Nom du dossier contenant les fichiers caches des éléments de taxonomie
	 *
	 * @package SPIP\TAXONOMIE\CACHE
	 */
	define('_TAXONOMIE_CACHE_NOMDIR', 'cache-taxonomie/');
}
if (!defined('_TAXONOMIE_CACHE_DIR')) {
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles
	 *
	 * @package SPIP\TAXONOMIE\CACHE
	 */
	define('_TAXONOMIE_CACHE_DIR', _DIR_VAR . _TAXONOMIE_CACHE_NOMDIR);
}
if (!defined('_TAXONOMIE_CACHE_FORCER')) {
	/**
	 * Indicateur permettant de forcer le recalcul du cache systématiquement.
	 * A n'utiliser que temporairement en mode debug par exemple.
	 *
	 * @package SPIP\TAXONOMIE\CACHE
	 */
	define('_TAXONOMIE_CACHE_FORCER', false);
}


/**
 * Ecrit le contenu issu d'un service taxonomique dans un fichier texte afin d'optimiser le nombre
 * de requêtes adressées au service.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string $cache
 *        Contenu du fichier cache. Si le service appelant manipule un tableau il doit le sérialiser avant
 *        d'appeler cette fonction.
 * @param string $service
 * @param string $action
 * @param int    $tsn
 * @param array  $options
 *
 * @return boolean
 *        Toujours à vrai.
 */
function cache_taxonomie_ecrire($cache, $service, $action, $tsn, $options) {

	// Création du dossier cache si besoin
	sous_repertoire(_DIR_VAR, trim(_TAXONOMIE_CACHE_NOMDIR, '/'));

	// Ecriture du fichier cache
	$fichier_cache = cache_taxonomie_nommer($service, $action, $tsn, $options);
	ecrire_fichier($fichier_cache, $cache);

	return true;
}


/**
 * Construit le nom du fichier cache en fonction du service, de l'action, du taxon concernés et
 * d'autres critères optionnels.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string $service
 * @param string $action
 * @param int    $tsn
 * @param array  $options
 *
 * @return string
 */
function cache_taxonomie_nommer($service, $action, $tsn, $options) {

	// Construction du chemin complet d'un fichier cache
	$fichier_cache = _TAXONOMIE_CACHE_DIR
					 . $service
					 . ($action ? '_' . $action : '')
					 . '_' . $tsn;

	// On complète le nom avec les options éventuelles
	if ($options) {
		foreach ($options as $_option => $_valeur) {
			if ($_valeur) {
				$fichier_cache .= '_' . $_valeur;
			}
		}
	}

	// On rajoute l'extension texte
	$fichier_cache .= '.txt';

	return $fichier_cache;
}

/**
 * Vérifie l'existence du fichier cache pour un taxon, un service et une actions donnés.
 * Si le fichier existe la fonction retourne son chemin complet.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string $service
 * @param string $action
 * @param int    $tsn
 * @param array  $options
 *
 * @return string
 *        Chemin du fichier cache si il existe ou chaine vide sinon.
 */
function cache_taxonomie_existe($service, $action, $tsn, $options = array()) {

	// Contruire le nom du fichier cache
	$fichier_cache = cache_taxonomie_nommer($service, $action, $tsn, $options);

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache)) {
		$fichier_cache = '';
	}

	return $fichier_cache;
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
		$fichiers_cache = glob(_TAXONOMIE_CACHE_DIR . '*.*');
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
	include_spip('taxonomie_fonctions');
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
