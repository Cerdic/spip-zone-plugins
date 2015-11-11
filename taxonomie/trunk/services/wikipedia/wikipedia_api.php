<?php
/**
 * Ce fichier contient l'ensemble des constantes et functions implémentant le service web de wikipedia.
 *
 * @package SPIP\TAXONOMIE\WIKIPEDIA
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL'))
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 * Le service fournit des données au format JSON
	 */
	define('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL', 'http://%langue%.wikipedia.org/w/api.php');

if (!defined('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL'))
	/**
	 * URL de base pour construire une page de Wikipedia dans une langue donnée
	 */
	define('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL', 'https://%langue%.wikipedia.org/wiki/');

/**
 * Configuration de la correspondance entre langue Wikipedia et code de langue SPIP.
 * La langue du service est l'index, le code SPIP est la valeur.
 */
$GLOBALS['wikipedia_language'] = array(
	'fr' => 'fr',
	'en' => 'en',
	'es' => 'es'
);


// ----------------------------------------------------------------------------
// ------------ API du web service WIKIPEDIA - Actions principales ------------
// ----------------------------------------------------------------------------

/**
 * Renvoie le texte de la page ou d'une section de la page à partir d'une phrase de recherche.
 * Dans le cas du plugin, cette recherche est généralement le nom scientifique du taxon.
 *
 * @api
 *
 * @param int       $tsn
 *      Identifiant ITIS (TSN) du taxon. Etant donné que ce service s'utilise toujours sur un taxon
 *      existant le TSN existe toujours. Il sert à créer le fichier cache.
 * @param string    $recherche
 *      Chaine de recherche qui est en généralement le nom scientifique du taxon.
 * @param string    $langue
 *      Langue au sens de Wikipedia qui préfixe l'url du endpoint. Vaut 'fr', 'en', 'es'...
 * @param int|null  $section
 *      Section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0.
 *
 * @return string
 *      Texte trouvé rédigé en mediawiki ou chaine vide sinon. Pour traduire le texte en SPIP
 *      il est nécessaire d'utiliser le plugin Convertisseur. Néanmoins, le texte même traduit
 *      doit être remanié manuellement.
 */
function wikipedia_get($tsn, $recherche, $langue, $section=null) {
	$information = array();


	// Normaliser la recherche: trim et mise en lettres minuscules
	$recherche = strtolower(trim($recherche));

	// Construire l'URL de la function de recherche par nom vernaculaire.
	// L'encodage de la recherche est effectuée dans la fonction.
	$url = wikipedia_api2url('json', 'query', $langue, $recherche, $section);

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
			$information['texte'] = $page['revisions'][0]['*'];
		}
	}

	return $information;
}


// --------------------------------------------------------------------------
// ------------ API du web service WIKIPEDIA - Fonctions annexes ------------
// --------------------------------------------------------------------------

/**
 * Renvoie la langue telle que le service ITIS la désigne à partir du code de langue
 * de SPIP.
 *
 * @api
 *
 * @param string    $language_code
 *      Code de langue de SPIP. La variable globale $itis_language définit le transcodage langue Wikipedia
 *      vers code SPIP.
 *
 * @return string
 *      Langue au sens de Wikipedia ou chaine vide sinon.
 */
function wikipedia_spipcode2language($language_code) {
	global $wikipedia_language;

	if (!$language = array_search($language_code,  $wikipedia_language)) {
		$language = '';
	}

	return $language;
}


/**
 * @param $id_taxon
 * @return string
 */
function wikipedia_credit($id_taxon) {
	// On recherche le tsn du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon='. sql_quote($id_taxon));

	// On crée l'url du taxon sur le site ITIS
	$url = str_replace('%tsn%', $taxon['tsn'], _TAXONOMIE_ITIS_URL_BASE_CITATION);
	$link = '<a href="' . $url . '"><em>' . ucfirst($taxon['nom_scientifique']) . '</em></a>';

	// On établit la citation
	$citation = _T('taxonomie:citation_itis', array('url' => $link));

	return $citation;
}


// ----------------------------------------------------------------
// ------------ Fonctions internes utilisées par l'API ------------
// ----------------------------------------------------------------

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
	$url = str_replace('%langue%', $langue, _TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL) . '?'
		. 'action=' . $action
		. '&meta=siteinfo|wikibase'
		. '&prop=revisions&rvprop=content'
		. (!is_null($section) ? '&rvsection=' . $section : '')
		. '&continue=&redirects=1'
		. '&format=' . $format
		. '&titles=' . rawurlencode(ucfirst($recherche));

	return $url;
}

?>
