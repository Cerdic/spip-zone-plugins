<?php
/**
 * Ce fichier contient les fonctions de gestion des caches utilisés par N-Core.
 *
 * @package SPIP\NCORE\CACHE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_NCORE_DIRCACHE')) {
	/**
	 * Dossier racine dans lesquels tous les caches de N-Core seront rangés.
	 * Les caches sont répartis suivant le plugin dans un sous-dossier `/${plugin}`.
	 */
	define('_NCORE_DIRCACHE', _DIR_CACHE . 'ncore/');
}
if (!defined('_NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE')) {
	/**
	 * Cache des signatures des fichiers YAML de types de noisette.
	 * Contient le tableau sérialisé `[type_noisette] = signature`.
	 */
	define('_NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE', 'type_noisette_signatures.php');
}
if (!defined('_NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION')) {
	/**
	 * Cache des descriptions des types de noisettes issues des fichiers YAML.
	 * Contient le tableau sérialisé `[type_noisette] = tableau de la description complète`.
	 * Chaque description contient aussi l'identifiant de la noisette déjà utilisé en index et la signature.
	 */
	define('_NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION', 'type_noisette_descriptions.php');
}
if (!defined('_NCORE_NOMCACHE_TYPE_NOISETTE_AJAX')) {
	/**
	 * Cache du paramétrage ajax des noisettes.
	 * Contient le tableau sérialisé `[type_noisette] = true/false`.
	 */
	define('_NCORE_NOMCACHE_TYPE_NOISETTE_AJAX', 'type_noisette_ajax.php');
}
if (!defined('_NCORE_NOMCACHE_TYPE_NOISETTE_INCLUSION')) {
	/**
	 * Cache du paramétrage d'inclusion dynamique des noisettes.
	 * Contient le tableau sérialisé `[type_noisette] = true/false`.
	 */
	define('_NCORE_NOMCACHE_TYPE_NOISETTE_INCLUSION', 'type_noisette_inclusions.php');
}
if (!defined('_NCORE_NOMCACHE_TYPE_NOISETTE_CONTEXTE')) {
	/**
	 * Cache des contextes des types de noisette issus des fichiers YAML.
	 * Contient le tableau sérialisé `[type_noisette] = tableau des éléments du contexte`.
	 */
	define('_NCORE_NOMCACHE_TYPE_NOISETTE_CONTEXTE', 'type_noisette_contextes.php');
}


/**
 * Lit le cache spécifié d'un plugin donné et renvoie le contenu sous forme de tableau
 * éventuellement vide.
 *
 * @api
 * @uses lire_fichier_securise()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $nom_cache
 *        Nom et extension du fichier cache.
 *
 * @return array
 *        Contenu du fichier sous la forme d'un tableau éventuellement vide.
 */
function cache_lire($plugin, $nom_cache) {

	// Initialisation du contenu du cache
	$cache = array();

	// Détermination du nom du cache en fonction du plugin appelant et du type
	$fichier_cache = _NCORE_DIRCACHE . "${plugin}/${nom_cache}";

	include_spip('inc/flock');
	if (lire_fichier_securise($fichier_cache, $contenu)) {
		$cache = unserialize($contenu);
	}

	return $cache;
}


/**
 * Ecrit le contenu d'un tableau dans le cache spécifié d'un plugin donné.
 *
 * @api
 * @uses ecrire_fichier_securise()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $nom_cache
 *        Nom et extension du fichier cache.
 * @param array  $contenu_cache
 *        Contenu sous forme de tableau à stocker dans un fichier cache après sérialisation.
 *
 * @return void
 */
function cache_ecrire($plugin, $nom_cache, $contenu_cache) {

	// Création du répertoire du cache si besoin
	$dir_cache = sous_repertoire(_DIR_CACHE, 'ncore');
	$dir_cache = sous_repertoire($dir_cache, $plugin);

	// Détermination du nom du cache en fonction du plugin appelant et du type
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
 * Supprime le cache cache spécifié d'un plugin donné.
 *
 * @api
 * @uses supprimer_fichier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $nom_cache
 *        Nom et extension du fichier cache.
 *
 * @return void
 */
function cache_supprimer($plugin, $nom_cache) {

	// Détermination du nom du cache en fonction du plugin appelant et du type
	$fichier_cache = _NCORE_DIRCACHE . "${plugin}/${nom_cache}";

	// Suppression du fichier cache
	include_spip('inc/flock');
	supprimer_fichier($fichier_cache);
}
