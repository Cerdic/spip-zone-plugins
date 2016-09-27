<?php
/**
 * Fonctions utiles au plugin URLs Pages Personnalisées
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Urls_pages_personnalisees\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Prendre en compte les URLs personnalisées pour la balise #URL_PAGE
 *
 * On utilise l'URL personnalisée si présente en base :
 * `spip.php?page=X => une-belle-url-pour-la-page-X`
 *
 * @see http://www.openstudio.fr/Pages-personnalisees-et-reecriture.html
 *
 * @param string $url
 *     URL de base : spip.php?page=X
 * @return string $url
 *     URL personnalisée si elle existe,
 *     sinon l'URL de base
 */
function url_page_personnalisee($url) {

	if (!function_exists('url_de_base')){
		include_spip('inc/utils');
	}
	$url_de_base = url_de_base();
	// retrouver la page d'après spip.php?page=X
	$query = parse_url($url, PHP_URL_QUERY);
	parse_str(parse_str($query));
	if (isset($page)
		and $url_personnalisee = sql_getfetsel('url', 'spip_urls', array('page = ' . sql_quote($page)))
	){
		$url = rtrim($url_de_base, '/') . '/' . $url_personnalisee;
	}

	return $url;
}


/**
 * Trouver le fond d'une page
 *
 * @uses trouver_fond()
 *
 * @return string | boolean
 *     Chemin du squelette s'il existe
 *     False sinon
 */
function trouver_fond_page($page) {

	// D'abord dans les dossiers de ZCore/ZPIP
	$dossier_z =
		(defined('_DIR_PLUGIN_ZCORE') ? 'content' :
		(defined('_DIR_PLUGIN_ZPIP') ? 'contenu' :
		false));
	if ($dossier_z) {
		$fond = trouver_fond($page, $dossier_z);
	}
	// Sinon dans le dossier des squelettes
	if (!$fond) {
		$fond = trouver_fond($page);
	}

	return $fond;
}


/**
 * Renvoie une liste des pages et le chemin de leurs squelettes.
 *
 * @uses inc_pages_to_array()
 */
function urls_pages_lister_pages(){
	$pages_to_array = charger_fonction('fonds_pages_to_array', 'inc');
	return $pages_to_array();
}


/**
 * Renvoie une liste des objets éditoriaux et de leurs surnoms
 *
 * @return array
 */
if (!function_exists('lister_objets_types')) {
function lister_objets_types(){

	include_spip('base/objets');
	$objets = array();
	$tables_objets = lister_tables_objets_sql();
	foreach($tables_objets as $table => $desc) {
		// type de base
		if (isset($desc['type'])
			and ($type = $desc['type'])
		) {
			$objets[] = $type;
		} else {
			$objets[] = objet_type($table);
		}
		// surnoms
		if (isset($desc['type_surnoms'])
			and count($type_surnoms = $desc['type_surnoms'])
		) {
			$objets = array_merge($objets, $type_surnoms);
		}
	}

	return $objets;
}
}
