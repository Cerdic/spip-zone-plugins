<?php
/**
 * Fonctions utiles au plugin Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister des noisettes offertes par le plugin InfoSPIP et consort.
 *
 * @param $repertoire
 * @return array
 */
function lister_noisettes_info_spip($repertoire) {
	$noisettes = find_all_in_path('infos_spip/' . $repertoire . '/', '.html$');

	if (is_array($noisettes) and count($noisettes) > 0) {
		foreach ($noisettes as $key => $value) {
			$noisettes[] = preg_replace("/.html$/", '', $key);
			unset($noisettes[$key]);
		}

		return $noisettes;
	}

	return array();
}

/**
 * Retourne la liste des modules Apache chargés
 * Si la fonction `apache_get_modules()` n'existe pas, on retourne un array().
 *
 * @return array
 */
function lister_modules_apache() {
	if (function_exists('apache_get_modules')) {
		return apache_get_modules();
	}

	return array();
}

/**
 * Retourne la liste de tous les modules compilés et chargés.
 * Si la fonction `get_loaded_extensions()` n'existe pas, on retourne un array().
 *
 * @return array
 */
function lister_extensions_php() {
	if (function_exists('get_loaded_extensions')) {
		$extensions = get_loaded_extensions();
		natcasesort($extensions);

		return $extensions;
	}

	return array();
}

/**
 * Récupérer le charset de la base de données.
 *
 * @return bool|string
 */
function sgbd_character_set_name() {
	include_spip('base/abstract_sql');
	$character_set = sql_fetsel('default_character_set_name', 'information_schema.SCHEMATA',
		'schema_name=' . sql_quote($GLOBALS['db_ok']['db']));

	return (isset($character_set['default_character_set_name'])) ? $character_set['default_character_set_name'] : false;
}

/**
 * Récupérer la collation de la base de données.
 *
 * @return bool|string
 */
function sgbd_collation_name() {
	include_spip('base/abstract_sql');
	$collation = sql_fetsel('default_collation_name', 'information_schema.SCHEMATA',
		'schema_name=' . sql_quote($GLOBALS['db_ok']['db']));

	return (isset($collation['default_collation_name'])) ? $collation['default_collation_name'] : false;
}

/**
 * Récupérer un tableau sur les informations de la BDD.
 * On utilise la globale `db_ok`
 *
 * @param string $info
 * @return array|mixed
 */
function sgbd_get_infos($info = '') {
	/*
	 * - bdd_host
	 * - bdd_port
	 * - bdd_type
	 * - sql_version
	 * - bdd_nom
	 * - bdd_prefixe
	 */
	$db_ok = $GLOBALS['db_ok'];
	$info_bdd = array(
		'host' => '',
		'port' => '3306',
		'type' => 'mysql',
		'version' => sql_version(),
		'db' => '',
		'prefixe' => 'spip',
		'charset' => '',
	);
	if (isset($db_ok) and is_array($db_ok) and count($db_ok)) {
		foreach (array_keys($info_bdd) as $index) {
			if (isset($db_ok[$index])) {
				$info_bdd[$index] = $db_ok[$index];
			}
		}
	}

	if (!empty($info) and isset($info_bdd[$info])) {
		return $info_bdd[$info];
	}

	return $info_bdd;
}

/**
 * Rechercher dans le fichier `info_spip/html/noisette.html` le tag `<template>` qui contiendra la chaine de langue du nom de la noisette.
 *
 * @param string $noisette Nom de la noisette
 * @return string Chaine de langue traduite.
 */
function nommer_noisettes_info_spip($noisette) {
	include_spip('inc/utils');
	$noisettes = find_all_in_path('infos_spip/html/', $noisette . '.html$');
	if (is_array($noisettes) and count($noisettes)) {
		include_spip('inc/flock');
		$content = spip_file_get_contents($noisettes[$noisette . '.html']);
		preg_match(',<template>(.*)</template>,', $content, $template);
		if (is_array($template) and count($template) and isset($template[1])) {
			$nom_noisette = preg_replace(',<:,', '', $template[1]);
			$nom_noisette = preg_replace(',:>,', '', $nom_noisette);

			return _T($nom_noisette);
		}
	}

	return _T($noisette);
}