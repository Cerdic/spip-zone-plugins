<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service web de Wikipedia.
 *
 * @package SPIP\TAXONOMIE\SERVICES\WIKIPEDIA
 * @todo    phpdoc : exemples
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL')) {
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 */
	define('_TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL', 'https://%langue%.wikipedia.org/w/api.php');
}

if (!defined('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL')) {
	/**
	 * URL de base pour construire une page de Wikipedia dans une langue donnée
	 */
	define('_TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL', 'https://%langue%.wikipedia.org/wiki/');
}

if (!defined('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT')) {
	/**
	 * Période de renouvellement du cache de Wikipedia (30 jours)
	 */
	define('_TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT', 86400 * 30);
}

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
 * Renvoie, à partir d'une phrase de recherche, soit le texte de la page ou d'une section de la page,
 * soit la liste des langues de la page.
 * Cette phrase de recherche est toujours le nom scientifique du taxon dans l'utilisation qui en est faite
 * par le plugin Taxonomie.
 * Le résultat de la requête est mis en cache pour une durée de plusieurs jours afin d'être servi à nouveau
 * sans accès à Wikipedia.
 *
 * @api
 * @uses cache_taxonomie_existe()
 * @uses cache_taxonomie_ecrire()
 * @uses wikipedia_build_url()
 * @uses service_requeter_json()
 *
 * @param string   $resource
 *      Chaine indiquant le type d'information à récupérer pour le taxon donné:
 *      - `text`      : le texte de l'article ou d'une section de l'article
 * 		- `languages` : la liste des langues de l'article concerné
 * @param int      $tsn
 *      Identifiant ITIS du taxon, le TSN. Etant donné que ce service s'utilise toujours sur un taxon
 *      existant le TSN existe toujours. Il sert à créer le fichier cache.
 * @param string   $search
 *      Chaine de recherche qui est en généralement le nom scientifique du taxon.
 * @param array    $options
 *      Tableau d'options qui peut contenir les index suivants :
 *      - `language` : langue au sens de Wikipedia qui préfixe l'url du endpoint. Vaut `fr`, `en`, `es`...
 *      - `section`  : section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0 ou `null`
 *                     pour tout la page.
 *      Cet argument est optionnel.
 *
 * @return string|array
 *      Texte trouvé rédigé en mediawiki ou chaine vide sinon. Pour traduire le texte en SPIP
 *      il est nécessaire d'utiliser le plugin Convertisseur. Néanmoins, le texte même traduit
 *      doit être remanié manuellement.
 */
function wikipedia_get_page($resource, $tsn, $search, $options = array()) {

	// Initialisation du tableau de sortie et du tableau d'options
	$information = array();

	// Si le cache est absent ou invalide on le recrée en utilisant le service web Wikipedia
	// sinon on le lit et on renvoie le tableau du contenu désérialisé.
	include_spip('inc/taxonomie_cacher');
	if (!$file_cache = cache_taxonomie_existe('wikipedia', $resource, $tsn, $options)
	or !filemtime($file_cache)
	or (time() - filemtime($file_cache) > _TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT)
	or (_TAXONOMIE_CACHE_FORCER)) {
		// Normaliser la recherche: trim et mise en lettres minuscules
		$search = strtolower(trim($search));

		// Construire l'URL de la function de recherche par nom vernaculaire.
		// L'encodage de la recherche est effectuée dans la fonction.
		$url = wikipedia_build_url('json', 'query', $resource, $search, $options);

		// Acquisition des données spécifiées par l'url
		$requeter = charger_fonction('taxonomie_requeter', 'inc');
		$data = $requeter($url);

		// Récupération de la section demandée.
		if (isset($data['batchcomplete'])
		and isset($data['query']['pages'])) {
			$reponses = $data['query']['pages'];
			$page = reset($reponses);
			$id = key($reponses);
			if (($id > 0) and !isset($page['missing'])) {
				if (($resource == 'text') and isset($page['revisions'][0]['*'])) {
					$information[$resource] = $page['revisions'][0]['*'];
				} elseif (($resource == 'languages')) {
					$information[$resource] = $page['revisions'][0]['langlinks'];
				}

				// Mise en cache
				cache_taxonomie_ecrire(serialize($information), 'wikipedia', $resource, $tsn, $options);
			}
		}
	} else {
		// Lecture et désérialisation du cache
		lire_fichier($file_cache, $contenu);
		$information = unserialize($contenu);
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
 * @param string $language_code
 *        Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 *        La variable globale `$wikipedia_language` définit le transcodage langue Wikipedia vers code SPIP.
 *
 * @return string
 *        Langue au sens de Wikipedia - `fr`, `en`, `es` - ou chaine vide sinon.
 */
function wikipedia_find_language($language_code) {

	if (!$language = array_search($language_code, $GLOBALS['wikipedia_language'])) {
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
 *        Id du taxon nécessaire pour construire l'url de la page Wikipedia concernée.
 * @param array $informations
 *        Tableau des informations complémentaires sur la source. Pour Wikipedia ce tableau fourni le ou
 *        les champs remplis avec Wikipedia.
 *
 * @return string
 *      Phrase de crédit.
 */
function wikipedia_credit($id_taxon, $informations) {
	// On recherche le tsn du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon=' . sql_quote($id_taxon));

	// On crée l'url du taxon sur le site de Wikipedia
	$url = str_replace('%langue%', 'fr', _TAXONOMIE_WIKIPEDIA_PAGE_BASE_URL)
		   . rawurlencode($taxon['nom_scientifique']);
	$link = '<a href="' . $url . '"><em>' . ucfirst($taxon['nom_scientifique']) . '</em></a>';

	// La liste des champs concernés (a priori le descriptif)
	include_spip('inc/taxonomer');
	$champs = implode(', ', array_map('taxon_traduire_champ', $informations['champs']));

	// On établit la citation
	$credit = _T('taxonomie:credit_wikipedia', array('champs' => strtolower($champs), 'url_taxon' => $link));

	return $credit;
}


// ----------------------------------------------------------------
// ------------ Fonctions internes utilisées par l'API ------------
// ----------------------------------------------------------------

/**
 * Construit l'URL de la requête Wikipedia correspondant à la demande utilisateur.
 *
 * @internal
 *
 * @param string   $format
 *        Format du résultat de la requête. Prend les valeurs `json` ou `xml`. Le `json` est recommandé.
 * @param string   $action
 *        Nom de l'action du service Wikipedia. La seule action `query` est utilisée dans cette API.
 * @param string   $resource
 *      Chaine indiquant le type d'information à récupérer pour le taxon donné:
 *      - `text`      : le texte de l'article ou d'une section de l'article
 * 		- `languages` : la liste des langues de l'article concerné
 * @param string   $search
 *        Clé de recherche qui est essentiellement le nom scientifique dans l'utilisation normale.
 *        Cette clé doit être encodée si besoin par l'appelant.
 * @param array    $options
 *      Tableau d'options qui peut contenir les index suivants :
 *      - `language` : langue au sens de Wikipedia qui préfixe l'url du endpoint. Vaut `fr`, `en`, `es`...
 *      - `section`  : section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0 ou `null`
 *                     pour tout la page.
 *      Cet argument est optionnel.
 *
 * @return string
 *        L'URL de la requête au service
 */
function wikipedia_build_url($format, $action, $resource, $search, $options) {

	// Construire la partie standard de l'URL de l'api sollicitée
	$language = !empty($options['language']) ? $options['language'] : 'fr';
	$url = str_replace('%langue%', $language, _TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL) . '?'
		   . 'action=' . $action
		   . '&format=' . $format
		   . '&titles=' . rawurlencode(ucfirst($search))
		   . '&continue=&redirects=1';

	// Finalisation de l'URL suivant le type de ressource demandée.
	switch ($resource) {
		case 'text':
			$url .= '&meta=siteinfo|wikibase'
			     . '&prop=revisions&rvprop=content'
	  		     . (!empty($options['section']) ? '&rvsection=' . $options['section'] : '');
	  		break;
		case 'languages':
			$url .= '&prop=langlinks&lllimit=500';
			break;
	}

	return $url;
}
