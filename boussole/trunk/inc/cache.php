<?php
/**
 * Ce fichier contient les fonctions qui permettent de construire, vérifier ou créer
 * les fichiers cache et les dossiers les contenant.
 *
 * @package SPIP\BOUSSOLE\Outils\Cache
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_BOUSSOLE_NOMDIR_CACHE'))
	/**
	 * Nom du dossier contenant les fichiers caches des boussoles */
	define('_BOUSSOLE_NOMDIR_CACHE', 'cache-boussoles/');
if (!defined('_BOUSSOLE_DIR_CACHE'))
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles */
	define('_BOUSSOLE_DIR_CACHE', _DIR_VAR . _BOUSSOLE_NOMDIR_CACHE);
if (!defined('_BOUSSOLE_CACHE_LISTE'))
	/**
	 * Fichier cache de la liste des boussoles */
	define('_BOUSSOLE_CACHE_LISTE', 'boussoles.xml');
if (!defined('_BOUSSOLE_PREFIXE_CACHE'))
	/**
	 * Pattern remplacement de l'alias d'une boussole */
	define('_BOUSSOLE_PREFIXE_CACHE', 'boussole-');
if (!defined('_BOUSSOLE_PATTERN_ALIAS'))
	/**
	 * Pattern remplacement de l'alias d'une boussole */
	define('_BOUSSOLE_PATTERN_ALIAS', '%alias%');
if (!defined('_BOUSSOLE_CACHE'))
	/**
	 * Fichier cache d'une boussole */
	define('_BOUSSOLE_CACHE', _BOUSSOLE_PREFIXE_CACHE . _BOUSSOLE_PATTERN_ALIAS . '.xml');


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
function ecrire_cache_boussole($cache, $alias_boussole){
	// Création du dossier cache si besoin
	$dossier = sous_repertoire(_DIR_VAR, trim(_BOUSSOLE_NOMDIR_CACHE, '/'));

	// Ecriture du fichier cache
	$fichier_cache = $dossier . str_replace(_BOUSSOLE_PATTERN_ALIAS, $alias_boussole, _BOUSSOLE_CACHE);
	ecrire_fichier($fichier_cache, $cache);

	// Ecriture du sha256 du cache dans un fichier séparé
	$fichier_sha = $fichier_cache . '.sha';
	ecrire_fichier($fichier_sha, sha1_file($fichier_cache));

	return true;
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
function cache_boussole_existe($alias_boussole){
	// Ecriture du fichier cache
	$fichier_cache = _BOUSSOLE_DIR_CACHE . str_replace(_BOUSSOLE_PATTERN_ALIAS, $alias_boussole, _BOUSSOLE_CACHE);

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache))
		$fichier_cache = '';

	return $fichier_cache;
}


/**
 * Ecriture de la liste des boussoles dans un cache au format XML et de son SHA.
 *
 * @param string	$cache
 * 		Contenu XML du fichier cache à créer.
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function ecrire_cache_liste($cache){
	// Création du dossier cache si besoin
	$dossier = sous_repertoire(_DIR_VAR, trim(_BOUSSOLE_NOMDIR_CACHE, '/'));

	// Ecriture du fichier cache
	$fichier_cache = $dossier . _BOUSSOLE_CACHE_LISTE;
	ecrire_fichier($fichier_cache, $cache);

	// Ecriture du sha256 du cache dans un fichier séparé
	$fichier_sha = $fichier_cache . '.sha';
	ecrire_fichier($fichier_sha, sha1_file($fichier_cache));

	return true;
}


/**
 * Vérifie l'existence du fichier cache de la liste et si oui retourne
 * son chemin complet.
 *
 * @return string
 * 		Chemin du fichier cache si il existe ou chaine vide sinon.
 */
function cache_liste_existe(){
	// Ecriture du fichier cache
	$fichier_cache = _BOUSSOLE_DIR_CACHE . _BOUSSOLE_CACHE_LISTE;

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache))
		$fichier_cache = '';

	return $fichier_cache;
}


/**
 * Supprime tous les fichiers caches au format XML et les fichiers SHA associés.
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function supprimer_caches(){
	include_spip('inc/flock');

	if ($fichiers_cache = glob(_BOUSSOLE_DIR_CACHE . "*.*")) {
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
