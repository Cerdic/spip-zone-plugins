<?php
/**
 * Ce fichier contient les fonctions de gestion des caches utilisés par N-Core.
 *
 * @package SPIP\NCORE\CACHE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_NCORE_DIRCACHE')) {
	/**
	 * Dossier racine dans lesquels tous les caches de N-Core seront écrits.
	 * Les caches sont répartis suivant le service dans un sous-dossier `/${service}`.
	 */
	define('_NCORE_DIRCACHE', _DIR_CACHE . 'ncore/');
}
if (!defined('_NCORE_NOMCACHE_NOISETTE_SIGNATURE')) {
	/**
	 * Cache des signatures des fichiers YAML de noisettes. Contient le tableau sérialisé `[noisette] = signature`.
	 */
	define('_NCORE_NOMCACHE_NOISETTE_SIGNATURE', 'noisettes_signature.php');
}
if (!defined('_NCORE_NOMCACHE_NOISETTE_DESCRIPTION')) {
	/**
	 * Cache des descriptions de noisettes issues des fichiers YAML. Contient le tableau sérialisé
	 * `[noisette] = tableau de la description complète`. Chaque description contient aussi l'identifiant
	 * de la noisette déjà utilisé en index et la signature.
	 */
	define('_NCORE_NOMCACHE_NOISETTE_DESCRIPTION', 'noisettes_description.php');
}
if (!defined('_NCORE_NOMCACHE_NOISETTE_AJAX')) {
	/**
	 * Cache du paramétrage ajax des noisettes. Contient le tableau sérialisé `[noisette] = true/false`.
	 */
	define('_NCORE_NOMCACHE_NOISETTE_AJAX', 'noisettes_ajax.php');
}
if (!defined('_NCORE_NOMCACHE_NOISETTE_INCLUSION')) {
	/**
	 * Cache du paramétrage d'inclusion dynamique des noisettes. Contient le tableau sérialisé `[noisette] = true/false`.
	 */
	define('_NCORE_NOMCACHE_NOISETTE_INCLUSION', 'noisettes_inclusion.php');
}
if (!defined('_NCORE_NOMCACHE_NOISETTE_CONTEXTE')) {
	/**
	 * Cache des contextes de noisettes issues des fichiers YAML. Contient le tableau sérialisé `[noisette] = tableau des éléments du contexte`.
	 */
	define('_NCORE_NOMCACHE_NOISETTE_CONTEXTE', 'noisettes_contexte.php');
}


/**
 * Lit le cache spécifié pour un service donné et renvoie le contenu sous forme de tableau éventuellement
 * vide.
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$nom_cache
 * 		Nom et extension du fichier cache.
 *
 * @return array
 * 		Contenu du fichier sous la forme d'un tableau éventuellement vide.
 */
function cache_lire($service, $nom_cache) {

	// Initialisation du contenu du cache
	$cache = array();

	// Détermination du nom du cache en fonction du service appelant et du type
	$fichier_cache = _NCORE_DIRCACHE . "${service}/${nom_cache}";

	include_spip('inc/flock');
	if (lire_fichier_securise($fichier_cache, $contenu)) {
		$cache = unserialize($contenu);
	}

	return $cache;
}


/**
 * Ecrit le contenu d'un tableau dans le cache spécifié pour un service donné.
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$nom_cache
 * 		Nom et extension du fichier cache.
 * @param array     $contenu_cache
 * 		Contenu sous forme de tableau à stocker dans un fichier cache après sérialisation.
 *
 * @return void
 */
function cache_ecrire($service, $nom_cache, $contenu_cache) {

	// Création du répertoire du cache si besoin
	$dir_cache = sous_repertoire(_DIR_CACHE, 'ncore');
	$dir_cache = sous_repertoire($dir_cache, $service);

	// Détermination du nom du cache en fonction du service appelant et du type
	$fichier_cache = "${dir_cache}${nom_cache}";

	// On vérifie que le contenu est bien un tableau. Si ce n'est pas le cas on le transforme en tableau.
	if (!is_array($contenu_cache)) {
		$contenu_cache = $contenu_cache ? array($contenu_cache) : array();
	}

	// Ecriture du fichier cache
	include_spip('inc/flock');
	ecrire_fichier_securise($fichier_cache, serialize($contenu_cache));
}


/**
 * Supprime le cache cache spécifié pour un service donné.
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$nom_cache
 * 		Nom et extension du fichier cache.
 *
 * @return void
 */
function cache_supprimer($service, $nom_cache) {

	// Détermination du nom du cache en fonction du service appelant et du type
	$fichier_cache = _NCORE_DIRCACHE . "${service}/${nom_cache}";

	// Suppression du fichier cache
	include_spip('inc/flock');
	supprimer_fichier($fichier_cache);
}
