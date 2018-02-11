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
