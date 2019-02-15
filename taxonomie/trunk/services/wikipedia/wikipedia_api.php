<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service web de Wikipedia.
 *
 * @package SPIP\TAXONOMIE\SERVICES\WIKIPEDIA
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
 * Renvoie, à partir d'une phrase de recherche, soit le texte de la page ou d'une section de la page avec ou pas
 * la liste des autres pages possibles, soit la liste des langues de la page.
 * Cette phrase de recherche est toujours le nom scientifique du taxon dans l'utilisation qui en est faite
 * par le plugin Taxonomie.
 * Le résultat de la requête est mis en cache pour une durée de plusieurs jours afin d'être servi à nouveau
 * sans accès à Wikipedia.
 *
 * @api
 * @uses cache_est_valide()
 * @uses wikipedia_build_url()
 * @uses inc_taxonomie_requeter()
 * @uses cache_ecrire()
 * @uses cache_lire()
 *
 * @param array  $search
 *        Tableau contenant le taxon à cherché sous une forme textuelle et numérique:
 *        - `name` : chaine de recherche qui est en généralement le nom scientifique du taxon.
 *        - `tsn`  : identifiant ITIS du taxon, le TSN. Etant donné que ce service s'utilise toujours sur un taxon
 *                   existant le TSN existe toujours. Il sert à créer le fichier cache.
 * @param string $spip_language
 *        Code de langue SPIP dans lequel on souhaite récupérer la page Wikipedia.
 * @param int    $section
 *        Section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0 ou `null` pour tout la page.
 * @param array  $options
 *        Tableau d'options qui peut contenir les index suivants :
 *        - `reload`  : force le recalcul du cache.
 *        Cet argument est optionnel.
 *
 * @return array
 *        Texte trouvé rédigé en mediawiki ou chaine vide sinon. Pour traduire le texte en SPIP
 *        il est nécessaire d'utiliser le plugin Convertisseur. Néanmoins, le texte même traduit
 *        doit être remanié manuellement.
 */
function wikipedia_get_page($search, $spip_language, $section = null, $options = array()) {

	// Initialisation du tableau de sortie et du tableau d'options
	$information = array();

	// Si le cache est absent ou invalide on le recrée en utilisant le service web Wikipedia
	// sinon on le lit et on renvoie le tableau du contenu désérialisé.
	if (!empty($search['name'] and !empty($search['tsn']))) {
		// Détermination de la langue Wikipedia
		$language = wikipedia_find_language($spip_language);

		// Construction des options permettant de nommer le fichier cache.
		// -- on précise la durée de conservation car ce service utilise 1 mois et pas 6 mois (par défaut).
		include_spip('inc/cache');
		$cache = array(
			'service'      => 'wikipedia',
			'action'       => 'get',
			'tsn'          => $search['tsn'],
			'language'     => $spip_language,
			'conservation' => _TAXONOMIE_WIKIPEDIA_CACHE_TIMEOUT
		);
		if ($section !== null) {
			$cache['section'] = $section;
		}

		if (!empty($options['reload'])
		or (!$file_cache = cache_est_valide('taxonomie', $cache))
		or (defined('_TAXONOMIE_CACHE_FORCER') ? _TAXONOMIE_CACHE_FORCER : false)) {
			// Normaliser la recherche: trim et mise en lettres minuscules
			$title = strtolower(trim($search['name']));

			// Calcul de l'url de la requête: on supprime
			$url = wikipedia_build_url($title, $language, $section);

			// Acquisition des données spécifiées par l'url
			$requeter = charger_fonction('taxonomie_requeter', 'inc');
			$data = $requeter($url);

			// Récupération de la section demandée.
			if (isset($data['query']['pages'])) {
				$reponses = $data['query']['pages'];
				$page = reset($reponses);
				$id = key($reponses);
				if (($id > 0) and !isset($page['missing'])) {
					$information['text'] = isset($page['revisions'][0]['*']) ? $page['revisions'][0]['*'] : '';
					$information['links'] = isset($page['links']) ? $page['links'] : array();
					$information['languages'] = isset($page['langlinks']) ? $page['langlinks'] : array();
				}

				// Mise en cache systématique pour gérer le cas où la page cherchée n'existe pas.
				cache_ecrire('taxonomie', $cache, $information);
			}
		} else {
			// Lecture et désérialisation du cache
			$information = cache_lire('taxonomie', $file_cache);
		}
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
 * @param string $spip_language
 *        Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 *        La variable globale `$wikipedia_language` définit le transcodage langue Wikipedia vers code SPIP.
 *
 * @return string
 *        Langue au sens de Wikipedia - `fr`, `en`, `es` - ou chaine vide sinon.
 */
function wikipedia_find_language($spip_language) {

	if (!$language = array_search($spip_language, $GLOBALS['wikipedia_language'])) {
		$language = 'fr';
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
	$link = '<a class="nom_scientifique_inline" href="' . $url . '" rel="noreferrer">' . ucfirst($taxon['nom_scientifique']) . '</a>';

	// La liste des champs concernés (a priori le descriptif)
	include_spip('inc/taxonomie');
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
 * @param string $search
 *        Clé de recherche qui est essentiellement le nom scientifique dans l'utilisation normale.
 *        Cette clé doit être encodée si besoin par l'appelant.
 * @param string $language
 *        Code de langue au sens de Wikipedia qui préfixe l'url du endpoint. Vaut `fr`, `en`, `es` pour l'instant.
 * @param array  $section
 *        Section de page dont le texte est à renvoyer. Entier supérieur ou égal à 0 ou `null`
 *        pour tout la page.
 *        Cet argument est optionnel.
 *
 * @return string
 *        L'URL de la requête au service
 */
function wikipedia_build_url($search, $language, $section = null) {

	// Construire la partie standard de l'URL de l'api sollicitée
	$url = str_replace('%langue%', $language, _TAXONOMIE_WIKIPEDIA_ENDPOINT_BASE_URL)
		. '?'
		. 'action=query'
		. '&format=json'
		. '&continue=&redirects=1'
		. '&prop=revisions|links|langlinks&rvprop=content&pllimit=500&lllimit=500&llprop=url'
		. '&titles=' . rawurlencode(ucfirst($search));

	// Choix d'une section précise si demandé.
	$url .= (!empty($section) ? '&rvsection=' . $section : '');

	return $url;
}
