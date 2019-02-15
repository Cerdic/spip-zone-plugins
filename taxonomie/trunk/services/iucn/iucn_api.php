<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service web de IUCN Red List.
 *
 * @package SPIP\TAXONOMIE\SERVICES\IUCN
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_TAXONOMIE_IUCN_ENDPOINT_BASE_URL')) {
	/**
	 * Préfixe des URL du service web de WIKIPEDIA.
	 */
	define('_TAXONOMIE_IUCN_ENDPOINT_BASE_URL', 'http://apiv3.iucnredlist.org/api/v3/');
}

if (!defined('_TAXONOMIE_IUCN_TAXON_BASE_URL')) {
	/**
	 * URL de base d'une page d'un taxon sur le site de la red list IUCN.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_IUCN_TAXON_BASE_URL', 'http://apiv3.iucnredlist.org/api/v3/website/');
}

if (!defined('_TAXONOMIE_ITIS_SITE_URL')) {
	/**
	 * URL de la page d'accueil du site ITIS.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_ITIS_SITE_URL', 'http://www.iucnredlist.org/');
}

if (!defined('_TAXONOMIE_IUCN_CACHE_TIMEOUT')) {
	/**
	 * Période de renouvellement du cache de Wikipedia (6 mois)
	 */
	define('_TAXONOMIE_IUCN_CACHE_TIMEOUT', 86400 * 30 * 6);
}

$GLOBALS['iucn_categorie'] = array(
	/**
	 * Configuration des catégories IUCN.
	 */
	// Extinct
	'EX'    => array('item' => 'extinct', 'versions' => array('3.1', '2.3')),
	// Extinct in the Wild
	'EW'    => array('item' => 'extinct_in_the_wild', 'versions' => array('3.1', '2.3')),
	// Critically Endangered
	'CR'    => array('item' => 'critically_endangered', 'versions' => array('3.1', '2.3')),
	// Critically Endangered (possibly extinct) - BirdLife
	'PE'    => array('item' => 'possibly_extinct', 'versions' => array()),
	// Endangered
	'EN'    => array('item' => 'endangered', 'versions' => array('3.1', '2.3')),
	'E'     => array('item' => 'endangered', 'versions' => array()),
	// Vulnerable
	'VU'    => array('item' => 'vulnerable', 'versions' => array('3.1', '2.3')),
	'V'     => array('item' => 'vulnerable', 'versions' => array()),
	// Threatened
	'T' => array('item' => 'threatened', 'versions' => array()),
	// Lower Risk (conservation dependent)
	'LR/cd' => array('item' => 'lower_risk_conservation_dependent', 'versions' => array('2.3')),
	// Near Threatened
	'NT'    => array('item' => 'near_threatened', 'versions' => array('3.1')),
	// Lower Risk (near threatened)
	'LR/nt' => array('item' => 'lower_risk_near_threatened', 'versions' => array('2.3')),
	// Least Concern
	'LC'    => array('item' => 'least_concern', 'versions' => array('3.1')),
	// Lower Risk (least concern)
	'LR/lc' => array('item' => 'lower_risk_least_concern', 'versions' => array('2.3')),
	// Data Deficient
	'DD'    => array('item' => 'data_deficient', 'versions' => array('3.1', '2.3')),
	// Not Evaluated
	'NE'    => array('item' => 'not_evaluated', 'versions' => array('3.1', '2.3')),
	// Not Recognized - BirdLife
	'NR'    => array('item' => 'not_recognized', 'versions' => array()),
);

$GLOBALS['iucn_language'] = array(
	/**
	 * Configuration de la correspondance entre langue IUCN et code de langue SPIP.
	 * La langue du service est l'index, le code SPIP est la valeur.
	 */
	'fre' => 'fr',
	'eng' => 'en',
	'spa' => 'es'
);

$GLOBALS['iucn_webservice'] = array(
	/**
	 * Variable globale de configuration de l'api des actions du service web IUCN Red List
	 */
	'species'     => array(
		'assessment'   => array(
			'list'     => 'result',
			'index'    => array(
				'nom_scientifique' => 'scientific_name',
				'code'             => 'category',
				'critere'          => 'criteria',
				'annee'            => 'published_year'
			),
		),
		'common_name' => array(
			'list'     => 'result',
			'index'    => array(
				'nom_commun' => 'taxonname',
				'langage'    => 'language'
			),
		),
		'history' => array(
			'list'     => 'result',
			'index'    => array(
				'annee'     => 'year',
				'categorie' => 'category',
				'code'      => 'code'
			),
		)
	)
);


// -----------------------------------------------------------------------
// ------------ API du web service IUCN - Actions principales ------------
// -----------------------------------------------------------------------


/**
 * Renvoie l'ensemble des informations sur un taxon désigné par son identifiant unique TSN.
 *
 * @api
 * @uses cache_est_valide()
 * @uses itis_build_url()
 * @uses inc_taxonomie_requeter_dist()
 * @uses cache_ecrire()
 * @uses cache_lire()
 *
 * @param array  $search
 *        Tableau contenant le taxon à cherché sous une forme textuelle et numérique:
 *        - `name` : chaine de recherche qui est en généralement le nom scientifique du taxon.
 *        - `tsn`  : identifiant ITIS du taxon, le TSN. Etant donné que ce service s'utilise toujours sur un taxon
 *                   existant le TSN existe toujours. Il sert à créer le fichier cache.
 *
 * @return array
 *        Si le taxon est trouvé, le tableau renvoyé possède les index associatifs suivants:
 *        - `nom_scientifique`  : le nom scientifique complet du taxon tel qu'il doit être affiché (avec capitales).
 *        - `rang`              : le nom anglais du rang taxonomique du taxon
 *        - `regne`             : le nom scientifique du règne du taxon en minuscules
 */
function iucn_get_assessment($search) {

	$assessment = array();

	if (!empty($search['scientific_name'] and !empty($search['tsn']))) {
		// Construction des options permettant de nommer le fichier cache.
		// -- inutile de préciser la durée de conservation car on utilise la valeur par défaut à savoir 6 mois.
		include_spip('inc/cache');
		$cache = array(
			'service'  => 'iucn',
			'action'   => 'assessment',
			'tsn'      => $search['tsn']
		);

		if ((!$file_cache = cache_est_valide('taxonomie', $cache))
		or (defined('_TAXONOMIE_CACHE_FORCER') ? _TAXONOMIE_CACHE_FORCER : false)) {
			// Construire l'URL de l'api sollicitée
			$url = iucn_build_url('species', 'assessment', $search['scientific_name']);

			// Acquisition des données spécifiées par l'url
			$requeter = charger_fonction('taxonomie_requeter', 'inc');
			$data = $requeter($url);

			// Récupération des informations choisies parmi l'enregistrement reçu à partir de la configuration
			// de l'action.
			$api = $GLOBALS['iucn_webservice']['species']['assessment'];
			include_spip('inc/filtres');
			$data = $api['list'] ? table_valeur($data, $api['list'], null) : $data;
			if (!empty($data)) {
				foreach ($api['index'] as $_destination => $_keys) {
					$element = $_keys ? table_valeur($data, $_keys, null) : $data;
					$assessment[$_destination] = is_string($element) ? trim($element) : $element;
				}
			}

			// Ajout de la catégorie dans un format non abrégé permettant de calculer le libellé traduit.
			$assessment['categorie'] = isset($GLOBALS['iucn_categorie'][$assessment['code']])
				? $GLOBALS['iucn_categorie'][$assessment['code']]['item']
				: '';

			// Mise en cache systématique pour gérer le cas où la page cherchée n'existe pas.
			cache_ecrire('taxonomie', $cache, $assessment);
		} else {
			// Lecture et désérialisation du cache
			$assessment = cache_lire('taxonomie', $file_cache);
		}
	}

	return $assessment;
}


// ---------------------------------------------------------------------
// ------------ API du web service IUCN - Fonctions annexes ------------
// ---------------------------------------------------------------------

/**
 * Renvoie la langue telle que le service Wikipedia la désigne à partir du code de langue
 * de SPIP.
 *
 * @api
 *
 * @param string $spip_language
 *        Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 *        La variable globale `$iucn_language` définit le transcodage langue IUCNs vers code SPIP.
 *
 * @return string
 *        Langue au sens de IUCN - `fre`, `eng`, `spa` - ou chaine vide sinon.
 */
function iucn_find_language($spip_language) {

	if (!$language = array_search($spip_language, $GLOBALS['iucn_language'])) {
		$language = 'fre';
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
 *        Tableau des informations complémentaires sur la source. Pour IUCN ce tableau fourni le ou
 *        les champs remplis avec IUCN.
 *
 * @return string
 *      Phrase de crédit.
 */
function iucn_credit($id_taxon, $informations) {
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
 * Construit l'URL de la requête IUCN correspondant à la demande utilisateur.
 *
 * @internal
 *
 * @param string $group
 *        Groupe d'actions du même type. Prend les valeurs:
 *        - `species` : groupe des actions de recherche du TSN à partir du nom commun ou scientifique
 * @param string $action
 *        Nom de l'action du service IUCN. Les valeurs pour le groupe species sont, par exemple, `assessment`,
 *        `commonname` et `history`.
 * @param string $key
 *        Clé de recherche qui dépend de l'action demandée. Ce peut être le nom scientifique, le TSN, etc.
 *        Cette clé doit être encodée si besoin par l'appelant.
 *
 * @return string
 *        L'URL de la requête au service
 */
function iucn_build_url($group, $action, $key) {

	// On récupère le token enregistré pour l'accès à l'API
	include_spip('inc/config');
	$token = lire_config('taxonomie/iucn_token', '');

	// Construire la partie standard de l'URL de l'api sollicitée
	$url = _TAXONOMIE_IUCN_ENDPOINT_BASE_URL
		. $group
		. '/' . rawurlencode($action)
		. '?token=' . $key;

	return $url;
}
