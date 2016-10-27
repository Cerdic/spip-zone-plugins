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
 *     URL de base : spip.php?page=X, ou spip.php?page=X&amp;truc=machin
 * @return string $url
 *     URL personnalisée si elle existe,
 *     sinon l'URL de base
 */
function url_page_personnalisee($url) {

	// On parse les query strings pour retrouver la page
	// Attention aux entités HTML, convertir les ampersands en '&'
	$rawurl = html_entity_decode($url);
	$query  = parse_url($rawurl, PHP_URL_QUERY);
	parse_str($query, $queries);
	if (isset($queries['page'])
		and $url_personnalisee = sql_getfetsel('url', 'spip_urls', array('page = ' . sql_quote($queries['page'])))
	){
		$url = $url_personnalisee;
		// Remettre les query strings éventuelles
		unset($queries['page']);
		foreach($queries as $k => $v){
			$url = parametre_url($url, $k, $v, '&amp;');
		}
	}

	return $url;
}


/**
 * Trouver le fond d'une page
 *
 * @uses trouver_fond()
 *
 * @param string $page
 *     Nom de la page (sans chemin, ni extension .html)
 * @return string | boolean
 *     Chemin du squelette s'il existe
 *     False sinon
 */
function trouver_fond_page($page) {

	// Cherchons d'abord dans les dossiers de ZCore/ZPIP
	$zcore     = defined('_DIR_PLUGIN_ZCORE');
	$z         = defined('_DIR_PLUGIN_Z');
	$dossier_z =
		($zcore ? 'content' :
		($z ? 'contenu' :
		false));
	if ($dossier_z) {
		// ajouter le préfixe 'page-' pour Zpip si nécessaire
		if ($z
			and substr($page, 0, strlen('page-')) != 'page-'
		) {
			$page = "page-$page";
		}
		$fond = trouver_fond($page, $dossier_z);
	}
	// Sinon cherchons dans le dossier des squelettes
	if (!$fond) {
		// Supprimer le préfixe « page- » pour Zpip
		if ($z
			and substr($page, 0, strlen('page-')) == 'page-'
		) {
			$page = substr($page, strlen('page-'));
		}
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
