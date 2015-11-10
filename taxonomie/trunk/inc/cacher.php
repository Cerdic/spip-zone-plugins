<?php
/**
 * Ce fichier contient les fonctions qui permettent de construire, vérifier ou créer
 * les fichiers cache des services taxonomiques et les dossiers les contenant.
 *
 * @package SPIP\BOUSSOLE\Outils\Cache
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_NOMDIR_CACHE'))
	/**
	 * Nom du dossier contenant les fichiers caches des éléments de taxonomie */
	define('_TAXONOMIE_NOMDIR_CACHE', 'cache-taxonomie/');
if (!defined('_TAXONOMIE_DIR_CACHE'))
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles */
	define('_TAXONOMIE_DIR_CACHE', _DIR_VAR . _TAXONOMIE_NOMDIR_CACHE);


/**
 * Ecriture des informations complètes d'une boussole dans un cache au format XML et de son SHA.
 *
 * @param string	$cache
 * 		Contenu XML du fichier cache à créer.
 * @param string	$alias_boussole
 * 		Alias de la boussole dont le cache va être créé.
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function ecrire_cache_taxonomie($cache, $service, $tsn, $code_langue='', $action='') {
	// Création du dossier cache si besoin
	sous_repertoire(_DIR_VAR, trim(_TAXONOMIE_DIR_CACHE, '/'));

	// Ecriture du fichier cache
	$fichier_cache = nommer_cache_taxonomie($service, $tsn, $code_langue, $action);
	ecrire_fichier($fichier_cache, $cache);

	return true;
}


function nommer_cache_taxonomie($service, $tsn, $code_langue='', $action='') {
	// Ecriture du fichier cache
	$fichier_cache = _TAXONOMIE_DIR_CACHE
		. $service
		. ($action ? '_' . $action : '')
		. '_' . $tsn
		. ($code_langue ? '_' . $code_langue : '');

	return $fichier_cache;
}

/**
 * Vérifie l'existence du fichier cache d'une boussole et si oui retourne
 * son chemin complet.
 *
 * @param string	$alias_boussole
 * 		Alias de la boussole dont on teste l'existence du cache.
 *
 * @return string
 * 		Chemin du fichier cache si il existe ou chaine vide sinon.
 */
function cache_taxonomie_existe($service, $tsn, $code_langue='', $action='') {
	// Contruire le nom du fichier cache
	$fichier_cache = nommer_cache_taxonomie($service, $tsn, $code_langue, $action);

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache))
		$fichier_cache = '';

	return $fichier_cache;
}


/**
 * Supprime tous les fichiers caches.
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function supprimer_caches(){
	include_spip('inc/flock');

	if ($fichiers_cache = glob(_TAXONOMIE_DIR_CACHE . "*.*")) {
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
	}

	return true;
}


/**
 * Etablit la liste de tous les caches y compris celui de la liste des boussoles
 * et construit un tableau avec la liste des fichiers et l'alias de la boussole
 * associée.
 *
 * @return array
 * 		Tableau des caches recensés :
 *
 * 		- fichier : chemin complet du fichier cache,
 * 		- alias : alias de la boussole ou vide si on est en présence de la liste des boussoles.
 */
function trouver_caches(){
	$caches = array();

	$fichier_liste = cache_liste_existe();
	if ($fichier_liste)
		$caches[] = array('fichier' => $fichier_liste, 'alias' => '');

	$pattern_cache = _BOUSSOLE_DIR_CACHE . str_replace(_BOUSSOLE_PATTERN_ALIAS, '*', _BOUSSOLE_CACHE);
	$fichiers_cache = glob($pattern_cache);
	if ($fichiers_cache) {
		foreach($fichiers_cache as $_fichier) {
			$alias_boussole = str_replace(_BOUSSOLE_PREFIXE_CACHE, '', basename($_fichier, '.xml'));
			$caches[] = array('fichier' => $_fichier, 'alias' => $alias_boussole);
		}
	}

	return $caches;
}


?>
