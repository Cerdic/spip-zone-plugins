<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service de taxonomie ITIS.
 *
 * @package SPIP\TAXONOMIE\ITIS
 * @todo phpdoc : décider sur les globales
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_ITIS_ENDPOINT_BASE_URL'))
	/**
	 * Préfixe des URL du service web de ITIS.
	 */
	define('_TAXONOMIE_ITIS_ENDPOINT_BASE_URL', 'http://www.itis.gov/ITISWebService/');

if (!defined('_TAXONOMIE_ITIS_TAXON_BASE_URL'))
	/**
	 * URL de base d'une page d'un taxon sur le site d'ITIS.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_ITIS_TAXON_BASE_URL', 'http://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=');

if (!defined('_TAXONOMIE_ITIS_SITE_URL'))
	/**
	 * URL de la page d'accueil du site ITIS.
	 * Cette URL est fournie dans les credits.
	 */
	define('_TAXONOMIE_ITIS_SITE_URL', 'http://www.itis.gov');

if (!defined('_TAXONOMIE_ITIS_REGEXP_RANKNAME'))
	/**
	 * Ligne d'un fichier ITIS hiérachie généré.
	 * Il est indispensable de respecter les majuscules des noms de groupe pour éviter de matcher
	 * les suborder, infrakingdom...
	 */
	define('_TAXONOMIE_ITIS_REGEXP_RANKNAME', '#(%groups_list%):\s*(\w+)\s*\[([^\]]*)\]\s*\[(\d+)\]#');


$GLOBALS['itis_language'] = array(
	/**
	 * Variable globale de configuration de la correspondance entre langue Wikipedia
	 * et code de langue SPIP. La langue du service est l'index, le code SPIP est la valeur.
	 */
	'french' => 'fr',
	'english' => 'en',
	'spanish' => 'es'
);
$GLOBALS['itis_webservice'] = array(
	/**
	 * Variable globale de configuration de l'api des actions du service web ITIS
	 */
	'search' => array(
		'commonname' => array(
			'function' => 'searchByCommonName',
			'argument' => 'srchKey',
			'list' => array('commonNames', 'commonName'),
			'index' => 'tsn'
		),
		'scientificname'=> array(
			'function'  => 'searchByScientificName',
			'argument'  => 'srchKey',
			'list'      => array('scientificNames', 'combinedName'),
			'index'     => 'tsn'
		)
	),
	'vernacular' => array(
		'vernacularlanguage' => array(
			'function'  => 'getTsnByVernacularLanguage',
			'argument'  => 'language',
			'list'      => 'vernacularTsns',
			'index'     => array('tsn' => 'commonName')
		)
	),
	'getfull' => array(
		'record' => array(
			'function'  => 'getFullRecordFromTSN',
			'argument'  => 'tsn',
			'list'      => '',
			'index'     => array(
							'nom_scientifique'  => array('scientificName', 'combinedName'),
							'rang'              => array('taxRank', 'rankName'),
							'regne'             => array('kingdom', 'kingdomName'),
							'tsn_parent'        => array('parentTSN', 'parentTsn'),
							'auteur'            => array('taxonAuthor', 'authorship'),
							'nom_commun'        => array('commonNameList', 'commonNames'),
			)
		)
	),
	'get' => array(
		'scientificname' => array(
			'function'  => 'getScientificNameFromTSN',
			'argument'  => 'tsn',
			'list'      => '',
			'index'     => array('string', 'combinedName'),
		),
		'kingdomname' => array(
			'function' => 'getKingdomNameFromTSN',
			'argument' => 'tsn',
			'list'      => '',
			'index'     => array('string', 'kingdomName'),
		),
		'parent' => array(
			'function' => 'getHierarchyUpFromTSN',
			'argument' => 'tsn',
			'list'      => '',
			'index'     => array('string', 'parentTsn'),
		),
		'rankname' => array(
			'function' => 'getTaxonomicRankNameFromTSN',
			'argument' => 'tsn',
			'list'      => '',
			'index'     => array('string', 'rankName'),
		),
		'author' => array(
			'function' => 'getTaxonAuthorshipFromTSN',
			'argument' => 'tsn',
			'list'      => '',
			'index'     => array('string', 'authorship'),
		),
		'coremetadata' => array(
			'function' => 'getCoreMetadataFromTSN',
			'argument' => 'tsn',
			'list'      => '',
			'index'     => array('array', ''),
		),
		'experts' => array(
			'function' => 'getExpertsFromTSN',
			'argument' => 'tsn',
			'list' => 'experts',
			'index'     => array('array', ''),
		),
		'commonnames' => array(
			'function' => 'getCommonNamesFromTSN',
			'argument' => 'tsn',
			'list' => 'commonNames',
			'index'     => array('array', array('language' => 'commonName')),
		),
		'othersources' => array(
			'function' => 'getOtherSourcesFromTSN',
			'argument' => 'tsn',
			'list' => 'otherSources',
			'index'     => array('array', ''),
		),
		'hierarchyfull' => array(
			'function' => 'getFullHierarchyFromTSN',
			'argument' => 'tsn',
			'list' => 'hierarchyList',
			'index'     => array('array', ''),
		),
		'hierarchydown' => array(
			'function' => 'getHierarchyDownFromTSN',
			'argument' => 'tsn',
			'list' => 'hierarchyList',
			'index'     => array('array', ''),
		),
	),
);


// -----------------------------------------------------------------------
// ------------ API du web service ITIS - Actions principales ------------
// -----------------------------------------------------------------------

/**
 * Recherche un taxon dans la base ITIS par son nom commun ou scientifique
 * et retourne son identifiant unique nommé TSN ou 0 si le taxon n'existe pas.
 *
 * @api
 * @uses api2url_itis()
 * @uses url2json_data()
 *
 * @param string	$action
 * 		Recherche par nom commun ou par nom scientifique. Prend les valeurs `commonname` ou `scientificname`
 * @param string	$search
 * 		Nom à rechercher précisément. Seul le taxon dont le nom coincidera exactement sera retourné.
 *
 * @return int
 * 		Identifiant unique TSN dans la base ITIS ou 0 si la recherche échoue
 */
function itis_search_tsn($action, $search) {
	global $itis_webservice;
	$tsn = 0;

	// Normaliser la recherche: trim et mise en lettres minuscules
	$search = strtolower(trim($search));

	// Construire l'URL de la fonction de recherche
	$url = api2url_itis('json', 'search', $action, rawurlencode($search));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération du TSN du taxon recherché
	$api = $itis_webservice['search'][$action];
	list($index_list, $index_name) = $api['list'];
	if (!empty($data[$index_list])) {
		// La recherche peut renvoyer plusieurs taxons. On considère que le "bon" taxon
		// correspond à celui dont le nom est exactement celui recherché.
		foreach ($data[$index_list] as $_data) {
			if ($_data
			AND (strcasecmp($_data[$index_name], $search) == 0)) {
				// On est sur le bon taxon, on renvoie le TSN
				$tsn = intval($_data[$api['index']]);
				break;
			}
		}
	}

	return $tsn;
}


/**
 * Renvoie l'ensemble des informations sur un taxon désigné par son identifiant unique TSN.
 *
 * @api
 * @uses api2url_itis()
 * @uses url2json_data()
 * @uses extraire_element()
 *
 * @param int	$tsn
 * 		Identifiant unique du taxon dans la base ITIS, le TSN
 *
 * @return array
 * 		Si le taxon est trouvé, le tableau renvoyé possède les index associatifs suivants:
 * 		- `nom_scientique`	: le nom scientifique du taxon en minuscules
 * 		- `rang`			: le nom anglais du rang taxonomique du taxon
 * 		- `regne`			: le nom scientifque du règne du taxon en minuscules
 * 		- `tsn_parent`		: le TSN du parent du taxon ou 0 si le taxon est un règne
 * 		- `auteur`			: la citation d’auteurs et la date de publication
 * 		- `nom_commun`		: un tableau indexé par langue (au sens d'ITIS en minuscules, `english`, `french`, `spanish`)
 * 							  fournissant le nom commun dans chacune des langues
 */
function itis_get_record($tsn) {
	global $itis_webservice;
	$record = array();

	// Construire l'URL de l'api sollicitée
	$url = api2url_itis('json', 'getfull', 'record', strval($tsn));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération des informations choisies parmi l'enregistrement reçu à partir de la configuration
	// de l'action.
	$api = $itis_webservice['getfull']['record'];
	$data = extraire_element($data, $api['list']);
	if (!empty($data)) {
		foreach ($api['index'] as $_destination => $_keys) {
			$element = extraire_element($data, $_keys);
			$record[$_destination] = is_string($element) ? trim($element) : $element;
		}
	}

	// On réorganise le sous-tableau des noms communs
	$noms = array();
	if (is_array($record['nom_commun'])
	AND $record['nom_commun']) {
		foreach ($record['nom_commun'] as $_nom) {
			$noms[strtolower($_nom['language'])] = trim($_nom['commonName']);
		}
	}
	// Et on modifie l'index des noms communs avec le tableau venant d'être construit.
	$record['nom_commun'] = $noms;

	return $record;
}


/**
 * Renvoie les informations demandées sur un taxon désigné par son identifiant unique TSN.
 *
 * @api
 * @uses api2url_itis()
 * @uses url2json_data()
 * @uses extraire_element()
 *
 * @param string	$action
 * 		Type d'information demandé. Prend les valeurs
 * 		- `scientificname`	: le nom scientifique du taxon
 * 		- `kingdomname`		: le règne du taxon
 * 		- `parent`			: le taxon parent dont son TSN
 * 		- `rankname`		: le rang taxonomique du taxon
 * 		- `author`			: le ou les auteurs du taxon
 * 		- `coremetadata`	: les métadonnées (à vérifier)
 * 		- `experts`			: les experts du taxon
 * 		- `commonnames`		: le ou les noms communs
 * 		- `othersources`	: les sources d'information sur le taxon
 * 		- `hierarchyfull`	: la hiérarchie complète jusqu'au taxon
 * 		- `hierarchydown`	: la hiérarchie (à vérifier)
 * @param int		$tsn
 * 		Identifiant unique du taxon dans la base ITIS (TSN)
 *
 * @return string|array
 * 		Chaine ou tableau caractéristique du type d'information demandé.
 */
function itis_get_information($action, $tsn) {
	global $itis_webservice;

	// Construire l'URL de l'api sollicitée
	$url = api2url_itis('json', 'get', $action, strval($tsn));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// On vérifie que le tableau est complet sinon on retourne un tableau vide
	$api = $itis_webservice['get'][$action];
	$data = extraire_element($data, $api['list']);
	list($type, $index) = $api['index'];

	if ($type == 'string') {
		$information = '';
		if (!empty($data[$index])) {
			$information = $data[$index];
		}
	} else {
		$information = array();
		if ($data) {
			$first_value = reset($data);
			if ($first_value) {
				if (!$index) {
					$information = $data;
					$format = "format_$action";
					if (function_exists($format)) {
						$information = $format($information);
					}
				} else {
					list($kkey, $kvalue) = each($index);
					foreach ($data as $_data) {
						$information[strtolower($_data[$kkey])][] = $_data[$kvalue];
					}
				}
			}
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
 * @uses api2url_itis()
 * @uses url2json_data()
 *
 * @param $language
 * 		Langue au sens d'ITIS écrite en minuscules. Vaut `french`, `english`, `spanish`...
 *
 * @return array
 * 		Tableau des noms communs associés à leur TSN. Le format du tableau est le suivant:
 * 		- `index`	: le TSN du taxon
 * 		- `valeur`	: le tableau des noms communs, chaque nom étant préfixé du code de langue
 * 					  de SPIP (ex: `[fr]bactéries`)
 */
function itis_list_vernaculars($language) {
	global $itis_webservice, $itis_language;
	$vernaculars =array();

	// Construire l'URL de l'api sollicitée
	$url = api2url_itis('json', 'vernacular', 'vernacularlanguage', $language);

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	include_spip('inc/distant');
	$data = url2json_data($url, _INC_DISTANT_MAX_SIZE*7);

	$api = $itis_webservice['vernacular']['vernacularlanguage'];
	if (!empty($data[$api['list']])) {
		$tag_language = '[' . $itis_language[$language] . ']';
		list($index, $index_name) = each($api['index']);
		foreach ($data[$api['list']] as $_data) {
			if (!empty($_data[$index])
			AND !empty($_data[$index_name])) {
				$vernaculars[$_data[$index]][] = $tag_language . $_data[$index_name];
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
 * @param string	$kingdom
 * 		Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param string	$upto
 * 		Rang taxonomique minimal jusqu'où charger le règne. Ce rang est fourni en anglais et en minuscules.
 * 		Il prend les valeurs :
 * 		- `phylum` (pour le règne Animalia) ou `division` (pour les règnes Fungi et Plantae),
 * 		- `class`,
 * 		- `order`,
 * 		- `family`,
 * 		- `genus`.
 * @param int		$sha_file
 *		Sha calculé à partir du fichier de taxons correspondant au règne choisi. Le sha est retourné
 * 		par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 * 		Chaque élément du tableau est un taxon. Un taxon est un tableau associatif dont chaque
 * 		index correspond à un champ de la table `spip_taxons`. Le tableau est ainsi prêt pour une
 * 		insertion en base de données.
 */
function itis_read_hierarchy($kingdom, $upto, &$sha_file) {
	$hierarchy = array();
	$sha_file = false;

	include_spip('inc/taxonomer');
	static $group_ids = array(
		'kingdom' => 1,
		'class' => 3,
		'order' => 4,
		'family' => 5,
		'genus' => 6,
		'specie' => 7);
	$rang_phylum = $kingdom==_TAXONOMIE_REGNE_ANIMAL ? 'phylum': 'division';
	$group_ids[$rang_phylum] = 2;
	asort($group_ids);

	if (array_key_exists($upto, $group_ids)) {
		include_spip('inc/charsets');
		// Construire la regexp qui permet de limiter la hiérarchie comme demandée
		$groups_list = implode('|', array_map('ucfirst', array_slice(array_flip($group_ids), 0, $group_ids[$upto])));
		$regexp = str_replace('%groups_list%', $groups_list, _TAXONOMIE_ITIS_REGEXP_RANKNAME);

		$file = find_in_path('services/itis/' . ucfirst($kingdom) . '_Genus.txt');
		if (file_exists($file)
		AND ($sha_file = sha1_file($file))) {
			$lines = file($file);
			if ($lines) {
				$groups = array();
				for ($i=1;$i<=array_search($upto, $group_ids);$i++) {
					$groups[$i] = 0;
				}
				// Scan du fichier ligne par ligne
				foreach ($lines as $_line) {
					$taxon = array(
						'regne' => $kingdom,
						'nom_commun' => '',
						'descriptif' => '',
						'edite' => 'non');
					if (preg_match($regexp, $_line, $match)) {
						// Initialisation du taxon
						$taxon['rang'] = strtolower($match[1]);
						$taxon['nom_scientifique'] = strtolower($match[2]);
						$taxon['auteur'] = importer_charset(trim($match[3]), 'iso-8859-1');
						$tsn = intval($match[4]);
						$taxon['tsn'] = $tsn;

						// Recherche du parent
						$taxon_group_id = $group_ids[$taxon['rang']];
						if ($taxon_group_id == 1) {
							// On traite à part le cas du règne qui ne se rencontre qu'une fois en début de fichier
							$taxon['tsn_parent'] = 0;
						}
						else {
							for($i=$taxon_group_id-1;$i>=1;$i--) {
								if ($groups[$i]) {
									$taxon['tsn_parent'] = $groups[$i];
									break;
								}
							}
						}

						// Insertion du taxon dans la hiérarchie
						$hierarchy[$tsn] = $taxon;

						// Stockage du groupe venant d'être inséré
						$groups[$taxon_group_id] = $tsn;
						// On vide les groupes d'après
						for($i=$taxon_group_id+1;$i<=5;$i++) {
							$groups[$i] = 0;
						}
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
 *
 * @api
 *
 * @param string	$language
 * 		Langue au sens d'ITIS écrite en minuscules. Vaut `french`, `english`, `spanish` etc.
 * @param int		$sha_file
 *		Sha calculé à partir du fichier des noms communs choisi. Le sha est retourné
 * 		par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 * 		Tableau des noms communs d'une langue donnée indexé par TSN. Le nom commun est préfixé
 * 		par le tag de langue SPIP pour être utilisé simplement dans une balise `<multi>`.
 */
function itis_read_vernaculars($language, &$sha_file) {
	global $itis_language;
	$vernaculars =array();
	$sha_file = false;

	// Ouvrir le fichier de nom communs correspondant au code de langue spécifié
	$file = find_in_path("services/itis/vernaculars_${language}.csv");
	if (file_exists($file)
	AND ($sha_file = sha1_file($file))) {
		// Lecture du fichier csv comme un fichier texte sachant que :
		// - le délimiteur de colonne est une virgule
		// - le caractère d'encadrement d'un texte est le double-quotes
		$lines = file($file);
		if ($lines) {
			// Créer le tableau de sortie à partir du tableau issu du csv (TSN, nom commun)
			$tag_language = '[' . $itis_language[$language] . ']';
			foreach ($lines as $_line) {
				list($tsn, $name) = explode(',', trim($_line));
				$vernaculars[intval($tsn)] = $tag_language . trim($name, '"');
			}
		}
	}

	return $vernaculars;
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
 * @param string    $spip_language
 *      Code de langue de SPIP. Prend les valeurs `fr`, `en`, `es`, etc.
 * 		La variable globale `$itis_language` définit le transcodage langue ITIS vers code SPIP.
 *
 * @return string
 *      Langue au sens d'ITIS en minuscules - `french`, `english`, `spanish` - ou chaine vide sinon.
 */
function itis_spipcode2language($spip_language) {
	global $itis_language;

	if (!$language = array_search($spip_language,  $itis_language)) {
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
 * @param int	$id_taxon
 * 		Id du taxon nécessaire pour construire l'url de la page ITIS fournissant une information complète sur
 * 		le taxon.
 * @param array	$informations
 * 		Tableau des informations complémentaires sur la source. Pour ITIS ce tableau est vide.
 *
 * @return string
 * 		Phrase de crédit.
 */
function itis_credit($id_taxon, $informations) {
	// On recherche le TSN du taxon afin de construire l'url vers sa page sur ITIS
	$taxon = sql_fetsel('tsn, nom_scientifique', 'spip_taxons', 'id_taxon='. sql_quote($id_taxon));

	// On crée l'url du taxon sur le site ITIS
	$url_taxon = _TAXONOMIE_ITIS_TAXON_BASE_URL . $taxon['tsn'];
	$link_taxon = '<a href="' . $url_taxon . '" rel="noreferrer"><em>' . ucfirst($taxon['nom_scientifique']) . '</em></a>';
	$link_site = '<a href="' . _TAXONOMIE_ITIS_SITE_URL . '" rel="noreferrer">' . _TAXONOMIE_ITIS_SITE_URL . '</a>';

	// On établit la citation
	$credit = _T('taxonomie:credit_itis', array('url_site' => $link_site, 'url_taxon' => $link_taxon));

	return $credit;
}


/**
 * Calcule le sha de chaque fichier ITIS fournissant des données, à savoir, ceux des règnes et ceux des noms
 * communs par langue.
 *
 * @api
 * @uses lister_regnes()
 *
 * @return array
 * 	Tableau à deux index principaux:
 * 		- `taxons`		: tableau associatif indexé par règne
 * 		- `traductions`	: tableau associatif par code de langue SPIP
 */
function itis_review_sha() {
	global $itis_language;
	$shas = array();

	include_spip('inc/taxonomer');
	$kingdoms = lister_regnes();

	foreach ($kingdoms as $_kingdom) {
		$file = find_in_path('services/itis/' . ucfirst($_kingdom) . '_Genus.txt');
		if (file_exists($file)
		AND ($sha_file = sha1_file($file))) {
			$shas['taxons'][$_kingdom] = $sha_file;
		}
	}

	foreach (array_keys($itis_language) as $_language) {
		$file = find_in_path("services/itis/vernaculars_${_language}.csv");
		if (file_exists($file)
		AND ($sha_file = sha1_file($file))) {
			$shas['traductions'][$itis_language[$_language]] = $sha_file;
		}
	}

	return $shas;
}


// ----------------------------------------------------------------
// ------------ Fonctions internes utilisées par l'API ------------
// ----------------------------------------------------------------

/**
 * Construit l'URL de la requête ITIS correspondant à la demande utilisateur.
 *
 * @param string	$format
 * 		Format du résultat de la requête. Prend les valeurs `json` ou `xml`. Le `json` est recommandé.
 * @param string	$group
 * 		Groupe d'actions du même type. Prend les valeurs:
 * 		- `search`		: groupe des actions de recherche du TSN à partir du nom commun ou scientifique
 * 		- `vernacular`	: groupe de l'action fournissant les noms communs d'une langue donnée
 * 		- `getfull`		: groupe de l'action fournissant l'ensemble des informations d'un taxon
 * 		- `get`			: groupe des actions fournissant une information précise sur un taxon
 * @param string	$action
 * 		Nom de l'action du service ITIS. Les valeurs dépendent du groupe. Par exemple, pour le groupe
 * 		`search` les actions sont `commonname` et `scientificname`.
 * @param string	$key
 * 		Clé de recherche qui dépend de l'action demandée. Ce peut être le nom scientifique, le TSN, etc.
 * 		Cette clé doit être encodée si besoin par l'appelant.
 *
 * @return string
 * 		L'URL de la requête au service
 */
function api2url_itis($format, $group, $action, $key) {
	global $itis_webservice;

	// Construire l'URL de l'api sollicitée
	$url = _TAXONOMIE_ITIS_ENDPOINT_BASE_URL
		 . ($format=='json' ? 'jsonservice/' : 'services/ITISService/')
		 . $itis_webservice[$group][$action]['function'] . '?'
		 . $itis_webservice[$group][$action]['argument'] . '=' . $key;

	return $url;
}
?>