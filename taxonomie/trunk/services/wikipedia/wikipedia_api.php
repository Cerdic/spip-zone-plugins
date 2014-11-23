<?php
/**
 * Ce fichier contient l'ensemble des constantes et functions implémentant le service web de wikipedia.
 *
 * @package SPIP\TAXONOMIE\WIKIPEDIA
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE'))
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE', 'http://%langue%.wikipedia.org/w/api.php');
if (!defined('_TAXONOMIE_WIKIPEDIA_URL_CITATION'))
	/**
	 * Préfixe des URL du service web de ITIS.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_URL_CITATION', 'http://fr.wikipedia.org');
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
function wikipedia_get($recherche, $section='') {
	global $spip_lang;
	$information = '';

	// Normaliser la recherche: trim et mise en lettres minuscules
	$recherche = strtolower(trim($recherche));

	// Construire l'URL de la function de recherche par nom vernaculaire
	$url = wikipedia_api2url('json', 'query', $spip_lang, $recherche);

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération de la section demandée. Si vide on renvoie tout le texte


	return $information;
}


/**
 * @param $format
 * @param $area
 * @param $api
 * @param $key
 *
 * @return string
 */
function wikipedia_api2url($format, $action, $langue, $recherche) {

	// Construire l'URL de l'api sollicitée
	$url = str_replace('%langue%', $langue, _TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE)
		. '&action=' . $action
		. '&prop=revisions&rvprop=content&continue=&redirects=1'
		. '&format=' . $format
		. '&titles=' . rawurlencode(ucfirst($recherche));

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
