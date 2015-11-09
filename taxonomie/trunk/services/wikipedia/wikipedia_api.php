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
	define('_TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE', 'http://%langue%.wikipedia.org/w/api.php?');
if (!defined('_TAXONOMIE_WIKIPEDIA_URL_CITATION'))
	/**
	 * Préfixe des URL du service web de ITIS.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_URL_CITATION', 'http://fr.wikipedia.org');
if (!defined('_TAXONOMIE_WIKIPEDIA_URL_BASE_PAGE'))
	/**
	 * Préfixe des URL du service web de ITIS.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_URL_BASE_PAGE', 'https://%langue%.wikipedia.org/wiki/');
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
function wikipedia_get($recherche, $section=null) {
	global $spip_lang;
	$information = '';

	// Normaliser la recherche: trim et mise en lettres minuscules
	$recherche = strtolower(trim($recherche));

	// Construire l'URL de la function de recherche par nom vernaculaire
	$url = wikipedia_api2url('json', 'query', $spip_lang, $recherche, $section);

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération de la section demandée.
	if (isset($data['batchcomplete'])
	AND isset($data['query']['pages'])) {
		$reponses = $data['query']['pages'];
		$page = reset($reponses);
		$id = key($reponses);
		if (($id > 0)
		AND !isset($page['missing'])
		AND isset($page['revisions'][0]['*'])) {
			$information = $page['revisions'][0]['*'];
		}
	}

	return $information;
}

/**
 * @param $id_taxon
 * @return string
 */
function wikipedia_citation($id_taxon) {
	// On recherche le tsn du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon='. sql_quote($id_taxon));

	// On crée l'url du taxon sur le site ITIS
	$url = str_replace('%tsn%', $taxon['tsn'], _TAXONOMIE_ITIS_URL_BASE_CITATION);
	$link = '<a href="' . $url . '"><em>' . ucfirst($taxon['nom_scientifique']) . '</em></a>';

	// On établit la citation
	$citation = _T('taxonomie:citation_itis', array('url' => $link));

	return $citation;
}


/**
 * @param $format
 * @param $area
 * @param $api
 * @param $key
 *
 * @return string
 */
function wikipedia_api2url($format, $action, $langue, $recherche, $section) {

	// Construire l'URL de l'api sollicitée
	$url = str_replace('%langue%', $langue, _TAXONOMIE_WIKIPEDIA_URL_BASE_REQUETE)
		. 'action=' . $action
		. '&meta=siteinfo|wikibase'
		. '&prop=revisions&rvprop=content'
		. (!is_null($section) ? '&rvsection=' . $section : '')
		. '&continue=&redirects=1'
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
