<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service de taxonomie ITIS.
 *
 * @package SPIP\TAXONOMIE\SERVICES\ITIS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_TAXONOMIE_ITIS_ENDPOINT_BASE_URL')) {
	/**
	 * Préfixe des URL du service web de ITIS.
	 */
	define('_TAXONOMIE_ITIS_ENDPOINT_BASE_URL', 'http://www.itis.gov/ITISWebService/');
}

if (!defined('_TAXONOMIE_ITIS_TAXON_BASE_URL')) {
	/**
	 * URL de base d'une page d'un taxon sur le site d'ITIS.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_ITIS_TAXON_BASE_URL', 'http://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=');
}

if (!defined('_TAXONOMIE_ITIS_SITE_URL')) {
	/**
	 * URL de la page d'accueil du site ITIS.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_ITIS_SITE_URL', 'http://www.itis.gov');
}

if (!defined('_TAXONOMIE_ITIS_REGEXP_RANKNAME')) {
	/**
	 * Ligne d'un fichier ITIS hiérachie généré.
	 * Il est indispensable de respecter les majuscules des noms de groupe pour éviter de matcher
	 * les suborder, infrakingdom...
	 */
	define('_TAXONOMIE_ITIS_REGEXP_RANKNAME', '#(%rank_list%):\s*(([A-Z]\s+)?[\w-]+)\s*(.+)\s*\[(\d+)\]\s*$#');
}

if (!defined('_TAXONOMIE_ITIS_CACHE_TIMEOUT')) {
	/**
	 * Période de renouvellement du cache de Wikipedia (365 jours)
	 */
	define('_TAXONOMIE_ITIS_CACHE_TIMEOUT', 86400 * 30 * 6);
}


$GLOBALS['itis_language'] = array(
	/**
	 * Variable globale de configuration de la correspondance entre langue ITIS
	 * et code de langue SPIP. La langue du service est l'index, le code SPIP est la valeur.
	 */
	'afrikaans'  => 'af',
	'arabic'     => 'ar',
	'chinese'    => 'zh',
	'dutch'      => 'nl',
	'english'    => 'en',
	'fijan'      => 'fj',
	'french'     => 'fr',
	'german'     => 'de',
	'greek'      => 'el',
	'hausa'      => 'ha',
	'hindi'      => 'hi',
	'icelandic'  => 'is',
	'japanese'   => 'ja',
	'korean'     => 'ko',
	'italian'    => 'it',
	'malagasy'   => 'mg',
	'portuguese' => 'pt',
	'spanish'    => 'es',
);

$GLOBALS['itis_webservice'] = array(
	/**
	 * Variable globale de configuration de l'api des actions du service web ITIS
	 */
	'search'     => array(
		'commonname'     => array(
			'function' => 'searchByCommonName',
			'argument' => 'srchKey',
			'list'     => 'commonNames',
			'index'    => array(
				'tsn'        => 'tsn',
				'nom_commun' => 'commonName',
				'langage'    => 'language'
			),
			'compare'  => 'commonName'
		),
		'commonnamebegin' => array(
			'function' => 'searchByCommonNameBeginsWith',
			'argument' => 'srchKey',
			'list'     => 'commonNames',
			'index'    => array(
				'tsn'        => 'tsn',
				'nom_commun' => 'commonName',
				'langage'    => 'language'
			),
			'compare'  => 'commonName'
		),
		'commonnameend' => array(
			'function' => 'searchByCommonNameEndsWith',
			'argument' => 'srchKey',
			'list'     => 'commonNames',
			'index'    => array(
				'tsn'        => 'tsn',
				'nom_commun' => 'commonName',
				'langage'    => 'language'
			),
			'compare'  => 'commonName'
		),
		'scientificname' => array(
			'function' => 'searchByScientificName',
			'argument' => 'srchKey',
			'list'     => 'scientificNames',
			'index'    => array(
				'tsn'              => 'tsn',
				'nom_scientifique' => 'combinedName',
				'regne'            => 'kingdom'
			),
			'compare'  => 'combinedName'
		)
	),
	'vernacular' => array(
		'vernacularlanguage' => array(
			'function' => 'getTsnByVernacularLanguage',
			'argument' => 'language',
			'list'     => 'vernacularTsns',
			'index'    => array('tsn' => 'commonName')
		)
	),
	'getfull'    => array(
		'record' => array(
			'function' => 'getFullRecordFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'index'    => array(
				'nom_scientifique'  => 'scientificName/combinedName',
				'rang_taxon'        => 'taxRank/rankName',
				'regne'             => 'kingdom/kingdomName',
				'tsn_parent'        => 'parentTSN/parentTsn',
				'auteur'            => 'taxonAuthor/authorship',
				'nom_commun'        => 'commonNameList/commonNames',
				'credibilite'       => 'credibilityRating/credRating',
				'usage_valide'      => 'usage/taxonUsageRating',
				'zone_geographique' => 'geographicDivisionList/geoDivisions'
			)
		)
	),
	'get'        => array(
		'scientificname' => array(
			'function' => 'getScientificNameFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'string',
			'index'    => 'combinedName',
		),
		'kingdomname'    => array(
			'function' => 'getKingdomNameFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'string',
			'index'    => 'kingdomName',
		),
		'parent'         => array(
			'function' => 'getHierarchyUpFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'string',
			'index'    => 'parentTsn',
		),
		'rankname'       => array(
			'function' => 'getTaxonomicRankNameFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'string',
			'index'    => 'rankName',
		),
		'author'         => array(
			'function' => 'getTaxonAuthorshipFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'string',
			'index'    => 'authorship',
		),
		'coremetadata'   => array(
			'function' => 'getCoreMetadataFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'array',
			'index'    => array(''),
		),
		'experts'        => array(
			'function' => 'getExpertsFromTSN',
			'argument' => 'tsn',
			'list'     => 'experts',
			'type'     => 'array',
			'index'    => array(''),
		),
		'commonnames'    => array(
			'function' => 'getCommonNamesFromTSN',
			'argument' => 'tsn',
			'list'     => 'commonNames',
			'type'     => 'array',
			'index'    => array(
				'langue'     => 'language',
				'nom_commun' => 'commonName',
			),
		),
		'othersources'   => array(
			'function' => 'getOtherSourcesFromTSN',
			'argument' => 'tsn',
			'list'     => 'otherSources',
			'type'     => 'array',
			'index'    => array(''),
		),
		'hierarchyfull'  => array(
			'function' => 'getFullHierarchyFromTSN',
			'argument' => 'tsn',
			'list'     => 'hierarchyList',
			'type'     => 'array',
			'index'    => array(
				'rang_taxon'       => 'rankName',
				'tsn'              => 'tsn',
				'nom_scientifique' => 'taxonName',
				'tsn_parent'       => 'parentTsn',
			),
		),
		'hierarchydown'  => array(
			'function' => 'getHierarchyDownFromTSN',
			'argument' => 'tsn',
			'list'     => 'hierarchyList',
			'type'     => 'array',
			'index'    => array(''),
		),
		'hierarchyup'  => array(
			'function' => 'getHierarchyUpFromTSN',
			'argument' => 'tsn',
			'list'     => '',
			'type'     => 'array',
			'index'    => array(''),
		),
	),
);


// -----------------------------------------------------------------------
// ------------ API du web service ITIS - Actions principales ------------
// -----------------------------------------------------------------------

/**
 * Recherche un taxon dans la base ITIS par son nom commun ou scientifique
 * et retourne son identifiant unique nommé TSN ou 0 si le taxon n'existe pas.
 * Selon le critère de correspondance de la recherche (stricte ou pas) la fonction
 * retourne un ou plusieurs taxons.
 *
 * @api
 * @uses itis_build_url()
 * @uses inc_taxonomie_requeter_dist()
 *
 * @param string $action
 *        Recherche par nom commun ou par nom scientifique. Prend les valeurs `commonname`, `scientificname`
 *        ou `commonnamebegin`.
 * @param string $search
 *        Nom à rechercher précisément. Seul le taxon dont le nom coincidera exactement sera retourné.
 * @param bool   $strict
 *        `true` indique une correspondance stricte de la chaine recherchée ce qui a pour conséquence de renvoyer
 *        une seule valeur de TSN. `false` indique une correspondance partielle et peut donc renvoyer plusieurs TSN.
 *
 * @return array
 *        Si la recherche est stricte, la fonction retourne l'identifiant unique TSN dans la base ITIS
 *        ou 0 si la recherche échoue.
 *        Sinon, la fonction retourne une liste de couples de valeurs (TNS, valeur trouvée).
 */
function itis_search_tsn($action, $search, $strict = true) {

	$tsns = array();

	// Normaliser la recherche: trim et mise en lettres minuscules
	$search = strtolower(trim($search));

	// Construire l'URL de la fonction de recherche
	$url = itis_build_url('json', 'search', $action, rawurlencode($search));

	// Acquisition des données spécifiées par l'url
	$requeter = charger_fonction('taxonomie_requeter', 'inc');
	$data = $requeter($url);

	// Récupération du TSN du taxon recherché
	$api = $GLOBALS['itis_webservice']['search'][$action];
	if (!empty($data[$api['list']])) {
		// La recherche peut renvoyer plusieurs taxons. Suivant le critère de correspondance de la recherche
		// on renvoie le "bon" taxon ou tous les taxons trouvés.
		foreach ($data[$api['list']] as $_data) {
			if ($_data) {
				if (($action == 'commonnamebegin')
				or !$strict
				or ($strict and (strcasecmp($_data[$api['compare']], $search) === 0))) {
					$tsn = array();
					foreach ($api['index'] as $_key => $_destination) {
						if ($_key == 'langage') {
							$tsn[$_key] = $GLOBALS['itis_language'][strtolower($_data[$_destination])];
						} elseif ($_key == 'tsn') {
							$tsn[$_key] = intval($_data[$_destination]);
						} else {
							$tsn[$_key] = $_data[$_destination];
						}
					}
					$tsns[] = $tsn;
					if ($strict) {
						break;
					}
				}
			}
		}
	}

	return $tsns;
}


/**
 * Renvoie l'ensemble des informations sur un taxon désigné par son identifiant unique TSN.
 *
 * @api
 *
 * @uses cache_est_valide()
 * @uses itis_build_url()
 * @uses inc_taxonomie_requeter_dist()
 * @uses cache_ecrire()
 * @uses cache_lire()
 *
 * @param int $tsn
 *        Identifiant unique du taxon dans la base ITIS, le TSN
 *
 * @return array
 *        Si le taxon est trouvé, le tableau renvoyé possède les index associatifs suivants:
 *        - `nom_scientifique`  : le nom scientifique complet du taxon tel qu'il doit être affiché (avec capitales).
 *        - `rang`              : le nom anglais du rang taxonomique du taxon
 *        - `regne`             : le nom scientifique du règne du taxon en minuscules
 *        - `tsn_parent`        : le TSN du parent du taxon ou 0 si le taxon est un règne
 *        - `auteur`            : la citation d’auteurs et la date de publication
 *        - `nom_commun`        : un tableau indexé par langue (au sens d'ITIS en minuscules, `english`, `french`,
 *                                `spanish`...) fournissant le nom commun dans chacune des langues
 *        - `credibilite`       : Information sur la crédibilité des informations du taxon
 *        - `usage_valide`      : Indication sur la validité de l'utilisation du taxon
 *        - `zone_geographique` : un tableau indexé par langue unique `english` des zones géographiques où le taxon
 *                                est localisé. Les zones sont libellées en anglais.
 */
function itis_get_record($tsn) {

	$record = array();

	if (intval($tsn)) {
		// Construction des options permettant de nommer le fichier cache.
		// -- inutile de préciser la durée de conservation car on utilise la valeur par défaut à savoir 6 mois.
		include_spip('inc/cache');
		$cache = array(
			'service'  => 'itis',
			'action'   => 'record',
			'tsn'      => $tsn
		);

		if ((!$file_cache = cache_est_valide('taxonomie', $cache))
		or (defined('_TAXONOMIE_CACHE_FORCER') ? _TAXONOMIE_CACHE_FORCER : false)) {
			// Construire l'URL de l'api sollicitée
			$url = itis_build_url('json', 'getfull', 'record', strval($tsn));

			// Acquisition des données spécifiées par l'url
			$requeter = charger_fonction('taxonomie_requeter', 'inc');
			$data = $requeter($url);

			// Récupération des informations choisies parmi l'enregistrement reçu à partir de la configuration
			// de l'action.
			$api = $GLOBALS['itis_webservice']['getfull']['record'];
			include_spip('inc/filtres');
			$data = $api['list'] ? table_valeur($data, $api['list'], null) : $data;
			if (!empty($data)) {
				foreach ($api['index'] as $_destination => $_keys) {
					$element = $_keys ? table_valeur($data, $_keys, null) : $data;
					$record[$_destination] = is_string($element) ? trim($element) : $element;
				}
			}

			// Insérer de base le tsn.
			$record['tsn'] = intval($tsn);

			// Passer en minuscules le rang et le règne exprimé en anglais.
			$record['rang_taxon'] = strtolower($record['rang_taxon']);
			$record['regne'] = strtolower($record['regne']);

			// On réorganise le sous-tableau des noms communs
			$names = array();
			if (!empty($record['nom_commun']) and is_array($record['nom_commun'])) {
				foreach ($record['nom_commun'] as $_name) {
					if (!empty($_name) and isset($GLOBALS['itis_language'][strtolower($_name['language'])])) {
						$langue_spip = $GLOBALS['itis_language'][strtolower($_name['language'])];
						$names[$langue_spip] = trim($_name['commonName']);
					}
				}
			}
			// Et on modifie l'index des noms communs avec le tableau venant d'être construit.
			$record['nom_commun'] = $names;

			// L'indicateur d'usage est mis à true/false.
			$record['usage_valide'] = ($record['usage_valide'] == 'valid') or ($record['usage_valide'] == 'accepted');

			// On réorganise le sous-tableau des zones géographiques.
			$zones = array();
			if (!empty($record['zone_geographique']) and is_array($record['zone_geographique'])) {
				foreach ($record['zone_geographique'] as $_zone) {
					$zones[] = trim($_zone['geographicValue']);
				}
			}
			// Et on modifie l'index des zones géographiques avec le tableau venant d'être construit
			// en positionnant celles-ci sous un idne 'english' car les zones sont libellées en anglais.
			unset($record['zone_geographique']);
			$record['zone_geographique'][$GLOBALS['itis_language']['english']] = $zones;

			// Mise en cache systématique pour gérer le cas où la page cherchée n'existe pas.
			cache_ecrire('taxonomie', $cache, $record);
		} else {
			// Lecture et désérialisation du cache
			$record = cache_lire('taxonomie', $file_cache);
		}
	}

	return $record;
}


/**
 * Renvoie les informations demandées sur un taxon désigné par son identifiant unique TSN.
 *
 * @api
 *
 * @uses cache_est_valide()
 * @uses itis_build_url()
 * @uses inc_taxonomie_requeter_dist()
 * @uses cache_ecrire()
 * @uses cache_lire()
 *
 * @param string $action
 *        Type d'information demandé. Prend les valeurs
 *        - `scientificname` : le nom scientifique du taxon
 *        - `kingdomname`    : le règne du taxon
 *        - `parent`         : le taxon parent dont son TSN
 *        - `rankname`       : le rang taxonomique du taxon
 *        - `author`         : le ou les auteurs du taxon
 *        - `coremetadata`   : les métadonnées (à vérifier)
 *        - `experts`        : les experts du taxon
 *        - `commonnames`    : le ou les noms communs
 *        - `othersources`   : les sources d'information sur le taxon
 *        - `hierarchyfull`  : la hiérarchie complète jusqu'au taxon et ses descendants directs
 *        - `hierarchyup`    : la hiérarchie limitée au parent direct
 * @param int    $tsn
 *        Identifiant unique du taxon dans la base ITIS (TSN)
 *
 * @return string|int|array
 *        Chaine ou tableau caractéristique du type d'information demandé.
 */
function itis_get_information($action, $tsn) {

	$information = array();

	if (intval($tsn)) {
		// Construction des options permettant de nommer le fichier cache.
		// -- inutile de préciser la durée de conservation car on utilise la valeur par défaut à savoir 6 mois.
		include_spip('inc/cache');
		$cache = array(
			'service'  => 'itis',
			'action'   => $action,
			'tsn'      => $tsn
		);

		if ((!$file_cache = cache_est_valide('taxonomie', $cache))
		or (defined('_TAXONOMIE_CACHE_FORCER') ? _TAXONOMIE_CACHE_FORCER : false)) {
			// Construire l'URL de l'api sollicitée
			$url = itis_build_url('json', 'get', $action, strval($tsn));

			// Acquisition des données spécifiées par l'url
			$requeter = charger_fonction('taxonomie_requeter', 'inc');
			$data = $requeter($url);

			// On vérifie que le tableau est complet sinon on retourne un tableau vide
			$api = $GLOBALS['itis_webservice']['get'][$action];
			include_spip('inc/filtres');
			$data = $api['list'] ? table_valeur($data, $api['list'], null) : $data;
			$type = $api['type'];
			$index = $api['index'];

			// TODO : problème si l'information est une chaine
			if ($type == 'string') {
				// L'information est limitée à une chaine ou un entier unique.
				// On renvoie la valeur seule.
				$information = '';
				if (!empty($data[$index])) {
					$information = $data[$index];
					if (in_array($action, array('rankname', 'kingdomname'))) {
						$information = strtolower($information);
					} elseif ($action == 'parent') {
						$information = intval($information);
					}
				}
			} else {
				// L'information demandée est un tableau.
				$information = array();
				if (!empty($data)) {
					// On vérifie si une fonction de post-formatage existe
					$format = "itis_format_$action";
					if (!function_exists($format)) {
						$format = '';
					}
					foreach ($data as $_data) {
						$item = array();
						// On construit le tableau de l'item brut correspondant à la configuration de l'action
						foreach ($index as $_key_information => $_key_data) {
							$item[$_key_information] = $_data[$_key_data];
						}
						// Si un formatage existe on formate l'item avant de l'ajouter au tableau de sortie.
						$information[] = $format ? $format($item) : $item;
					}
				}
			}

			// Mise en cache systématique pour gérer le cas où la page cherchée n'existe pas.
			cache_ecrire('taxonomie', $cache, $information);
		} else {
			// Lecture et désérialisation du cache
			$information = cache_lire('taxonomie', $file_cache);
		}
	}

	return $information;
}


/**
 * Renvoie la liste des noms communs définis pour certains taxons dans une langue donnée mais
 * tout règne confondu.
 * Peu de taxons sont traduits dans la base ITIS, seules le français, l'anglais et
 * l'espagnol sont réellement utilisables.
 * Pour l'anglais, le nombre de taxons est très important car les 4 règnes non supportés par
 * le plugin Taxonomie sont fortement traduits.
 *
 * @api
 * @uses itis_build_url()
 * @uses inc_taxonomie_requeter_dist()
 *
 * @param $language
 *        Langue au sens d'ITIS écrite en minuscules. Vaut `french`, `english`, `spanish`...
 *
 * @return array
 *        Tableau des noms communs associés à leur TSN. Le format du tableau est le suivant:
 *        - l'index représente le TSN du taxon,
 *        - la valeur fournit le tableau des noms communs, chaque nom étant préfixé du code de langue
 *        de SPIP (ex: `[fr]bactéries`)
 */
function itis_list_vernaculars($language) {

	$vernaculars = array();

	// Construire l'URL de l'api sollicitée
	$url = itis_build_url('json', 'vernacular', 'vernacularlanguage', $language);

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$requeter = charger_fonction('taxonomie_requeter', 'inc');
	$data = $requeter($url, _INC_DISTANT_MAX_SIZE * 7);

	$api = $GLOBALS['itis_webservice']['vernacular']['vernacularlanguage'];
	if (!empty($data[$api['list']])) {
		$tag_language = '[' . $GLOBALS['itis_language'][$language] . ']';
		$destination = reset($api['index']);
		$key = key($api['index']);
		foreach ($data[$api['list']] as $_data) {
			if (!empty($_data[$destination])
				and !empty($_data[$key])
			) {
				$vernaculars[$_data[$key]][] = $tag_language . $_data[$destination];
			}
		}
	}

	return $vernaculars;
}


// -----------------------------------------------------------------------------------------------
// ------------ API du web service ITIS - Fonctions de lecture des fichiers de taxons ------------
// -----------------------------------------------------------------------------------------------

/**
 * Lit le fichier hiérarchique ITIS des taxons d'un règne et renvoie la liste des taxons retenus.
 *
 * @api
 *
 * @param string $kingdom
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param array  $ranks_hierarchy
 *        Liste des rangs disponibles pour le règne concerné structurée comme une hiérarchie du règne aux rangs
 *        inférieurs. Cette liste contient tous les rangs principaux, secondaires et intercalaires.
 *        Le tableau est de la forme [nom anglais du rang en minuscules] = détails du rang
 * @param string $sha_file
 *        Sha calculé à partir du fichier de taxons correspondant au règne choisi. Le sha est retourné
 *        par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 *        Chaque élément du tableau est un taxon. Un taxon est un tableau associatif dont chaque
 *        index correspond à un champ de la table `spip_taxons`. Le tableau est ainsi prêt pour une
 *        insertion en base de données.
 */
function itis_read_hierarchy($kingdom, $ranks_hierarchy, &$sha_file) {

	$hierarchy = array();
	$sha_file = false;

	if ($ranks_hierarchy) {
		// Extraire de la liste les rangs du règne au genre, seuls rangs disponibles dans le fichier ITIS.
		include_spip('inc/taxonomie');
		$id_genre = $ranks_hierarchy[_TAXONOMIE_RANG_GENRE]['id'];
		foreach ($ranks_hierarchy as $_rang => $_description) {
			if ($_description['id'] <= $id_genre) {
				$ranks[$_rang] = $_description['id'];
			}
		}

		// Classer la liste des rangs de manière à aller du règne au genre.
		asort($ranks);

		// Construire la regexp à partir de la liste des rangs maintenant connue.
		$rank_list = implode('|', array_map('ucfirst', array_keys($ranks)));
		$regexp = str_replace('%rank_list%', $rank_list, _TAXONOMIE_ITIS_REGEXP_RANKNAME);

		$file = find_in_path("services/itis/${kingdom}_genus.txt");
		if (file_exists($file) and ($sha_file = sha1_file($file))) {
			$lines = file($file);
			if ($lines) {
				$parents = array();
				$rank_position = 0;
				foreach ($ranks as $_rank_name => $_rank_id) {
					$parents[$_rank_id] = 0;
					$rank_position++;
					$ranks[$_rank_name] = $rank_position;
				}
				$max_rank_position = $rank_position;
				// Scan du fichier ligne par ligne
				include_spip('inc/charsets');
				foreach ($lines as $_line) {
					$taxon = array(
						'regne'       => $kingdom,
						'nom_commun'  => '',
						'descriptif'  => '',
						'indicateurs' => '',
						'edite'       => 'non',
						'importe'     => 'oui',
						'espece'      => 'non',
						'statut'      => 'publie',
					);
					if (preg_match($regexp, $_line, $match)) {
						// Initialisation du taxon
						// -- rang et nom scientifique en minuscules
						$taxon['rang_taxon'] = strtolower($match[1]);
						$taxon['nom_scientifique'] = $match[2];
						// -- Importer le nom de l'auteur qui est en ISO-8859-1 dans le charset du site
						$taxon['auteur'] = trim(importer_charset(trim($match[4]), 'iso-8859-1'), '[]');
						$tsn = intval($match[5]);
						$taxon['tsn'] = $tsn;

						// Vérifier si il existe un indicateur spécial dans le nom scientifique comme
						// un X pour indiquer un taxon hybride.
						if (strtolower(trim($match[3])) == 'x') {
							$taxon['indicateurs'] = 'hybride';
						}

						// Recherche du parent
						$taxon_rank_position = $ranks[$taxon['rang_taxon']];
						if ($taxon_rank_position == $ranks[_TAXONOMIE_RANG_REGNE]) {
							// On traite à part le cas du règne qui ne se rencontre qu'une fois en début de fichier
							$taxon['tsn_parent'] = 0;
						} else {
							// On recherche le premier parent donc la position n'est pas 0.
							for ($i = $taxon_rank_position - 1; $i >= 1; $i--) {
								if ($parents[$i]) {
									$taxon['tsn_parent'] = $parents[$i];
									break;
								}
							}
						}

						// Insertion du taxon dans la hiérarchie
						$hierarchy[$tsn] = $taxon;

						// Stockage du TSN du rang venant d'être inséré
						$parents[$taxon_rank_position] = $tsn;
						// On vide les position de rangs d'après
						for ($i = $taxon_rank_position + 1; $i <= $max_rank_position; $i++) {
							$parents[$i] = 0;
						}
					} else {
						// On trace la ligne qui n'est pas détectée comme une ligne de taxon.
						spip_log("Ligne non phrasée: ${_line}", 'taxonomie');
					}
				}
			}
		}
	}

	return $hierarchy;
}


/**
 * Lit le fichier des noms communs - tout règne confondu - d'une langue donnée et renvoie un tableau
 * de tous ces noms indexés par leur TSN.
 * La base de données ITIS contient souvent plusieurs traductions d'une même langue pour un taxon donné. Cette
 * fonction met à jour séquentiellement les traductions sans s'en préoccuper. De fait, c'est la dernière traduction
 * rencontrée qui sera fournie dans le tableau de sortie.
 *
 * @api
 *
 * @param string $language
 *        Langue au sens d'ITIS écrite en minuscules. Vaut `french`, `english`, `spanish` etc.
 * @param string $sha_file
 *        Sha calculé à partir du fichier des noms communs choisi. Le sha est retourné
 *        par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 *        Tableau des noms communs d'une langue donnée indexé par TSN. Le nom commun est préfixé
 *        par le tag de langue SPIP pour être utilisé simplement dans une balise `<multi>`.
 */
function itis_read_vernaculars($language, &$sha_file) {

	$vernaculars = array();
	$sha_file = false;

	// Ouvrir le fichier de nom communs correspondant au code de langue spécifié
	$file = find_in_path("services/itis/vernaculars_${language}.csv");
	if (file_exists($file) and ($sha_file = sha1_file($file))) {
		// Lecture du fichier csv comme un fichier texte sachant que :
		// - le délimiteur de colonne est une virgule
		// - le caractère d'encadrement d'un texte est le double-quotes
		$lines = file($file);
		if ($lines) {
			// Créer le tableau de sortie à partir du tableau issu du csv (TSN, nom commun)
			$tag_language = '[' . $GLOBALS['itis_language'][$language] . ']';
			foreach ($lines as $_line) {
				list($tsn, $name) = explode(',', trim($_line));
				$vernaculars[intval($tsn)] = $tag_language . trim($name, '"');
			}
		}
	}

	return $vernaculars;
}


/**
 * Lit le fichier des rangs d'un règne donné et construit la hiérarchie de ces mêmes rangs.
 *
 * @api
 *
 * @param string $kingdom
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param string $sha_file
 *        Sha calculé à partir du fichier de taxons correspondant au règne choisi. Le sha est retourné
 *        par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 *        Tableau des rangs identifiés par leur nom scientifique en anglais et organisé comme une hiérarchie
 *        du règne au rang de plus bas niveau.
 */
function itis_read_ranks($kingdom, &$sha_file) {

	$ranks = array();
	$sha_file = false;

	// Ouvrir le fichier des rangs du règne spécifié.
	$file = find_in_path("services/itis/${kingdom}_ranks.json");
	if (file_exists($file) and ($sha_file = sha1_file($file))) {
		// Lecture du fichier json et décodage en tableau.
		include_spip('inc/flock');
		lire_fichier($file, $content);
		if ($content) {
			$itis_ranks = json_decode($content, true);
			if ($itis_ranks) {
				// Le fichier est toujours classé du règne au rang fils le plus bas dans l'arborescence.
				// On peut donc être assuré que le parent d'un rang donné a toujours été préalablement
				// traité sauf le premier, le règne.
				include_spip('inc/taxonomie');
				$rank_ids = array();
				foreach ($itis_ranks as $_rank) {
					$rank_name = strtolower($_rank['rank_name']);
					// -- Sauvegarde de l'id qui servira lors de la lecture du fichier hiérarchique des taxons.
					$ranks[$rank_name]['id'] = $_rank['rank_id'];
					// -- Détermination des parents
					if (isset($rank_ids[$_rank['dir_parent_rank_id']]) and isset($rank_ids[$_rank['req_parent_rank_id']])) {
						// Cas des rangs enfant du règne.
						$ranks[$rank_name]['parent'] = $rank_ids[$_rank['dir_parent_rank_id']];
						$ranks[$rank_name]['parent_principal'] = $rank_ids[$_rank['req_parent_rank_id']];
					} else {
						// Cas du règne qui n'a pas de parent.
						$ranks[$rank_name]['parent'] = '';
						$ranks[$rank_name]['parent_principal'] = '';
					}
					// -- Détermination du type de rang
					$ranks[$rank_name]['type'] = rang_informer_type($rank_name);

					// -- Sauvegarde de l'id ITIS du rang traité pour les descendants.
					$rank_ids[$_rank['rank_id']] = $rank_name;
				}
			}
		}
	}

	return $ranks;
}


// ---------------------------------------------------------------------
// ------------ API du web service ITIS - Fonctions annexes ------------
// ---------------------------------------------------------------------

/**
 * Renvoie la langue telle que le service ITIS la désigne à partir du code de langue
 * de SPIP.
 *
 * @api
 *
 * @param string $spip_language
 *        Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 *        La variable globale `$GLOBALS['itis_language']` définit le transcodage langue ITIS vers code SPIP.
 *
 * @return string
 *      Langue au sens d'ITIS en minuscules - `french`, `english`, `spanish` - ou chaine vide sinon.
 */
function itis_find_language($spip_language) {

	if (!$language = array_search($spip_language, $GLOBALS['itis_language'])) {
		$language = '';
	}

	return $language;
}


/**
 * Construit la phrase de crédits précisant que les données fournies proviennent de la base de données
 * d'ITIS.
 *
 * @api
 *
 * @param int   $id_taxon
 *        Id du taxon nécessaire pour construire l'url de la page ITIS fournissant une information complète sur
 *        le taxon.
 * @param array $informations
 *        Tableau des informations complémentaires sur la source. Pour ITIS ce tableau est vide.
 *
 * @return string
 *        Phrase de crédit.
 */
function itis_credit($id_taxon, $informations = array()) {

	// On recherche le TSN du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon=' . sql_quote($id_taxon));

	// On crée l'url du taxon sur le site ITIS
	$url_taxon = _TAXONOMIE_ITIS_TAXON_BASE_URL . $taxon['tsn'];
	$link_taxon = '<a class="nom_scientifique_inline" href="' . $url_taxon . '" rel="noreferrer">' . ucfirst($taxon['nom_scientifique']) . '</a>';
	$link_site = '<a href="' . _TAXONOMIE_ITIS_SITE_URL . '" rel="noreferrer">' . _TAXONOMIE_ITIS_SITE_URL . '</a>';

	// On établit la citation
	$credit = _T('taxonomie:credit_itis', array_merge(array('url_site' => $link_site, 'url_taxon' => $link_taxon), $informations));

	return $credit;
}


/**
 * Calcule le sha de chaque fichier ITIS fournissant des données, à savoir, ceux des règnes et ceux des noms
 * communs par langue.
 *
 * @api
 *
 * @return array
 *    Tableau à deux index principaux:
 *        - `taxons`        : tableau associatif indexé par règne
 *        - `traductions`    : tableau associatif par code de langue SPIP
 */
function itis_review_sha() {

	$shas = array();

	include_spip('inc/taxonomie');
	$kingdoms = regne_lister();

	foreach ($kingdoms as $_kingdom) {
		$file = find_in_path('services/itis/' . ucfirst($_kingdom) . '_Genus.txt');
		if (file_exists($file) and ($sha_file = sha1_file($file))) {
			$shas['taxons'][$_kingdom] = $sha_file;
		}
	}

	foreach (array_keys($GLOBALS['itis_language']) as $_language) {
		$file = find_in_path("services/itis/vernaculars_${_language}.csv");
		if (file_exists($file) and ($sha_file = sha1_file($file))) {
			$shas['traductions'][$GLOBALS['itis_language'][$_language]] = $sha_file;
		}
	}

	return $shas;
}


// ----------------------------------------------------------------
// ---------- Fonctions internes de formatage des sorties ---------
// ----------------------------------------------------------------

/**
 * Formatage d'un item de la liste des ascendants d'un taxon.
 *
 * @internal
 *
 * @param array $item
 *        Tableau extrait du service get et représentant un taxon ascendant. Les valeurs sont insérées btutes.
 *
 * @return array
 *        Tableau dont les valeurs ont été formatées (rang taxonomique en minuscules).
 */
function itis_format_hierarchyfull($item) {

	// Le formatage d'un item de la hiérarchie consiste uniquement à passer en minuscule l'identifiant du rang.
	if (isset($item['rang_taxon'])) {
		$item['rang_taxon'] = strtolower($item['rang_taxon']);
	}

	return $item;
}


// ----------------------------------------------------------------
// ------------ Fonctions internes utilisées par l'API ------------
// ----------------------------------------------------------------

/**
 * Construit l'URL de la requête ITIS correspondant à la demande utilisateur.
 *
 * @internal
 *
 * @param string $format
 *        Format du résultat de la requête. Prend les valeurs `json` ou `xml`. Le `json` est recommandé.
 * @param string $group
 *        Groupe d'actions du même type. Prend les valeurs:
 *        - `search`        : groupe des actions de recherche du TSN à partir du nom commun ou scientifique
 *        - `vernacular`    : groupe de l'action fournissant les noms communs d'une langue donnée
 *        - `getfull`        : groupe de l'action fournissant l'ensemble des informations d'un taxon
 *        - `get`            : groupe des actions fournissant une information précise sur un taxon
 * @param string $action
 *        Nom de l'action du service ITIS. Les valeurs dépendent du groupe. Par exemple, pour le groupe
 *        `search` les actions sont `commonname` et `scientificname`.
 * @param string $key
 *        Clé de recherche qui dépend de l'action demandée. Ce peut être le nom scientifique, le TSN, etc.
 *        Cette clé doit être encodée si besoin par l'appelant.
 *
 * @return string
 *        L'URL de la requête au service
 */
function itis_build_url($format, $group, $action, $key) {

	// Construire l'URL de l'api sollicitée
	$url = _TAXONOMIE_ITIS_ENDPOINT_BASE_URL
		   . ($format == 'json' ? 'jsonservice/' : 'services/ITISService/')
		   . $GLOBALS['itis_webservice'][$group][$action]['function'] . '?'
		   . $GLOBALS['itis_webservice'][$group][$action]['argument'] . '=' . $key;

	return $url;
}
