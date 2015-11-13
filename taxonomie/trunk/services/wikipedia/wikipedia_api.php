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

if (!defined('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT'))
	/**
	 * Période de renouvellement du cache de Wikipedia : 30 jours
	 */
	define('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT', 86400*30);

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
 * Cette phrase de recherche est toujours le nom scientifique du taxon dans l'utilisation qui en est faite
 * par le plugin Taxonomie.
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
 *      Section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0. Cet argument est
 *      optionnel.
 *
 * @return string
 *      Texte trouvé rédigé en mediawiki ou chaine vide sinon. Pour traduire le texte en SPIP
 *      il est nécessaire d'utiliser le plugin Convertisseur. Néanmoins, le texte même traduit
 *      doit être remanié manuellement.
 */
function wikipedia_get($tsn, $recherche, $langue, $section=null) {
	$information = array();

	// Si le cache est absent ou invalide on le recrée en utilisant le service web Wikipedia
	// sinon on le litet on revoie le tableau du contenu désérialisé.
	include_spip('inc/taxonomer');
	if (!$file_cache = cache_taxonomie_existe('wikipedia', $tsn, $langue)
	OR !filemtime($file_cache)
	OR (time()-filemtime($file_cache)>_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT)) {
		// Normaliser la recherche: trim et mise en lettres minuscules
		$recherche = strtolower(trim($recherche));

		// Construire l'URL de la function de recherche par nom vernaculaire.
		// L'encodage de la recherche est effectuée dans la fonction.
		$url = api2url_wikipedia('json', 'query', $langue, $recherche, $section);

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

		// Mise en cache
		ecrire_cache_taxonomie(serialize($information), 'wikipedia', $tsn, $langue);
	} else {
		// Lecture et désérialisation du cache
		lire_fichier($file_cache, $information);
		$information = unserialize($information);
	}

	return $information;
}


// --------------------------------------------------------------------------
// ------------ API du web service WIKIPEDIA - Fonctions annexes ------------
// --------------------------------------------------------------------------

/**
 * Renvoie la langue telle que le service Wikipedia la désigne à partir du code de langue
 * de SPIP.
 *
 * @api
 *
 * @param string    $language_code
 *      Code de langue de SPIP. La variable globale $wikipedia_language définit le transcodage langue Wikipedia
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
 * Construit la phrase de crédits précisant que les données fournies proviennent d'une page de Wikipedia.
 *
 * @api
 *
 * @param int   $id_taxon
 *      Id du taxon nécessaire pour construire l'url de la page Wikipedia concernée.
 * @param array $informations
 *      Tableau des informations complémentaires sur la source. Pour Wikipedia ce tableau fourni le champ
 *      rempli (descriptif).
 *
 * @return string
 *      Phrase de crédit.
 */
function wikipedia_credit($id_taxon, $informations) {
	// On recherche le tsn du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon='. sql_quote($id_taxon));

	// On crée l'url du taxon sur le site de Wikipedia
	$url = str_replace('%langue%', 'fr', _TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL)
		. rawurlencode($taxon['nom_scientifique']);
	$link = '<a href="' . $url . '"><em>' . ucfirst($taxon['nom_scientifique']) . '</em></a>';

	// La liste des champs concernés (a priori le descriptif)
	include_spip('inc/taxonomer');
	$champs = implode(', ', array_map('traduire_champ_taxon', $informations['champs']));

	// On établit la citation
	$credit = _T('taxonomie:credit_wikipedia', array('champs' => strtolower($champs),'url_taxon' => $link));

	return $credit;
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
function api2url_wikipedia($format, $action, $langue, $recherche, $section) {

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
