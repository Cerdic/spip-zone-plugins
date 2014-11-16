<?php
/**
 * Ce fichier contient l'ensemble des constantes et functions implémentant le service de taxonomie ITIS.
 *
 * @package SPIP\TAXONOMIE\ITIS
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE'))
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE', 'http://%langue%.wikipedia.org/w/api.php');
if (!defined('_TAXONOMIE_WIKIPEDIA_LANGUE_DEFAUT'))
	/**
	 * Langue par défaut pour les api utilisant des noms communs
	 */
	define('_TAXONOMIE_WIKIPEDIA_LANGUE_DEFAUT', 'en');


/**
 *
 * @param $nom_commun
 * @return array
 */
function wikipedia_get($api, $recherche) {
	$tsn = 0;

	// Normaliser la recherche: trim et mise en lettres minuscules
	$recherche = strtolower(trim($recherche));

	// Construire l'URL de la function de recherche par nom vernaculaire
	$url = itis_api2url('json', 'search', $api, rawurlencode($recherche));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération du TSN du taxon recherché
	$api = $itis_webservice['search'][$api];
	if (isset($data[$api['list']])
	AND $data[$api['list']]) {
		foreach ($data[$api['list']] as $_data) {
			if ($_data
			AND (strcasecmp($_data[$api['index']], $recherche) == 0)) {
				// On est sur le bon taxon, on renvoie le TSN
				$tsn = intval($_data['tsn']);
				break;
			}
		}
	}

	return $tsn;
}


/**
 * @param $format
 * @param $area
 * @param $api
 * @param $key
 *
 * @return string
 */
function wikipedia_api2url($format, $area, $api, $key) {
	global $itis_webservice;

	// Construire l'URL de l'api sollicitée
	$url = _TAXONOMIE_ITIS_URL_BASE_REQUETE
		 . ($format=='json' ? 'jsonservice/' : 'services/ITISService/')
		 . $itis_webservice[$area][$api]['function'] . '?'
		 . $itis_webservice[$area][$api]['argument'] . '=' . $key;

	return $url;
}

/**
 * @param $language_code
 *
 * @return string
 */
function wikipedia_code2language($language_code) {
	static $itis_languages = array(
		'fr' => 'french',
		'en' => 'english');

	$language = _TAXONOMIE_ITIS_LANGUE_DEFAUT;
	if (array_key_exists($language_code,  $itis_languages)) {
		$language = $itis_languages[$language_code];
	}

	return $language;
}

?>