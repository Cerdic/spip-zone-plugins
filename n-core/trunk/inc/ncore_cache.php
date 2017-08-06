<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_NCORE_DIRCACHE')) {
	define('_NCORE_DIRCACHE', _DIR_CACHE . 'ncore/');
}
if (!defined('_NCORE_DIR_CACHE')) {
	define('_NCORE_NOMCACHE_NOISETTE_AJAX', 'noisettes_ajax.php');
}
if (!defined('_NCORE_DIR_CACHE')) {
	define('_NCORE_NOMCACHE_NOISETTE_CONTEXTE', 'noisettes_contexte.php');
}
if (!defined('_NCORE_DIR_CACHE')) {
	define('_NCORE_NOMCACHE_NOISETTE_INCLUSION', 'noisettes_inclusion.php');
}


/**
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture des md5 et de stockage des descriptions de noisettes
 * 		spécifiques au service.
 * @param string	$nom_cache
 * 		Nom et extension du fichier cache.
 *
 * @return array
 * 		Contenu du fichier sous la forme d'un tableau ou tableau vide sinon.
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
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture des md5 et de stockage des descriptions de noisettes
 * 		spécifiques au service.
 * @param string	$nom_cache
 * 		Nom et extension du fichier cache.
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
