<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service web de Wikipedia.
 *
 * @package SPIP\TAXONOMIE\WIKIPEDIA
 * @todo phpdoc : décider sur les globales
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL'))
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 */
	define('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL', 'http://%langue%.wikipedia.org/w/api.php');

if (!defined('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL'))
	/**
	 * URL de base pour construire une page de Wikipedia dans une langue donnée
	 */
	define('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL', 'https://%langue%.wikipedia.org/wiki/');

if (!defined('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT'))
	/**
	 * Période de renouvellement du cache de Wikipedia (30 jours)
	 */
	define('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT', 86400*30);

$GLOBALS['wikipedia_language'] = array(
	/**
	 * Configuration de la correspondance entre langue Wikipedia et code de langue SPIP.
	 * La langue du service est l'index, le code SPIP est la valeur.
	 */
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
 * Le résultat de la requête est mis en cache pour une durée de plusieurs jours afin d'être servi à nouveau
 * sans accès à Wikipedia.
 *
 * @api
 * @uses cache_taxonomie_existe()
 * @uses ecrire_cache_taxonomie()
 * @uses api2url_wikipedia()
 * @uses url2json_data()
 *
 * @param int		$tsn
 * 		Identifiant ITIS du taxon, le TSN. Etant donné que ce service s'utilise toujours sur un taxon
 * 		existant le TSN existe toujours. Il sert à créer le fichier cache.
 * @param string	$search
 * 		Chaine de recherche qui est en généralement le nom scientifique du taxon.
 * @param string	$language
 * 		Langue au sens de Wikipedia qui préfixe l'url du endpoint. Vaut `fr`, `en`, `es`...
 * @param int|null	$section
 * 		Section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0 ou `null` pour tout la page.
 * 		Cet argument est optionnel.
 *
 * @return string
 * 		Texte trouvé rédigé en mediawiki ou chaine vide sinon. Pour traduire le texte en SPIP
 * 		il est nécessaire d'utiliser le plugin Convertisseur. Néanmoins, le texte même traduit
 * 		doit être remanié manuellement.
 */
function wikipedia_get($tsn, $search, $language, $section=null) {
	$information = array();

	// Si le cache est absent ou invalide on le recrée en utilisant le service web Wikipedia
	// sinon on le litet on revoie le tableau du contenu désérialisé.
	include_spip('inc/taxonomer');
	if (!$file_cache = cache_taxonomie_existe('wikipedia', $tsn, $language)
	OR !filemtime($file_cache)
	OR (time()-filemtime($file_cache)>_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT)) {
		// Normaliser la recherche: trim et mise en lettres minuscules
		$search = strtolower(trim($search));

		// Construire l'URL de la function de recherche par nom vernaculaire.
		// L'encodage de la recherche est effectuée dans la fonction.
		$url = api2url_wikipedia('json', 'query', $language, $search, $section);

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
		ecrire_cache_taxonomie(serialize($information), 'wikipedia', $tsn, $language);
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
 * @param string	$language_code
 * 		Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 * 		La variable globale `$wikipedia_language` définit le transcodage langue Wikipedia vers code SPIP.
 *
 * @return string
 * 		Langue au sens de Wikipedia - `fr`, `en`, `es` - ou chaine vide sinon.
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
 * @param int	$id_taxon
 * 		Id du taxon nécessaire pour construire l'url de la page Wikipedia concernée.
 * @param array	$informations
 * 		Tableau des informations complémentaires sur la source. Pour Wikipedia ce tableau fourni le ou
 * 		les champs remplis avec Wikipedia.
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
 * Construit l'URL de la requête Wikipedia correspondant à la demande utilisateur.
 *
 * @param string	$format
 * 		Format du résultat de la requête. Prend les valeurs `json` ou `xml`. Le `json` est recommandé.
 * @param string	$action
 * 		Nom de l'action du service Wikipedia. La seule action `query` est utilisée dans cette API.
 * @param string	$language
 * 		Langue au sens de Wikipedia en minuscules. Prend les valeurs `fr`, `en`, `es`, etc.
 * @param string	$search
 * 		Clé de recherche qui est essentiellement le nom scientifique dans l'utilisation normale.
 * 		Cette clé doit être encodée si besoin par l'appelant.
 * @param int|null	$section
 * 		Section de la page à renvoyer. Valeur entière de 0 à n ou null si on veut toute la page.
 *
 * @return string
 * 		L'URL de la requête au service
 */
function api2url_wikipedia($format, $action, $language, $search, $section) {

	// Construire l'URL de l'api sollicitée
	$url = str_replace('%langue%', $language, _TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL) . '?'
		. 'action=' . $action
		. '&meta=siteinfo|wikibase'
		. '&prop=revisions&rvprop=content'
		. (!is_null($section) ? '&rvsection=' . $section : '')
		. '&continue=&redirects=1'
		. '&format=' . $format
		. '&titles=' . rawurlencode(ucfirst($search));

	return $url;
}

?>
