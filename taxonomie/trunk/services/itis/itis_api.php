<?php
/**
 * Ce fichier contient l'ensemble des constantes et functions implémentant le service de taxonomie ITIS.
 *
 * @package SPIP\TAXONOMIE\ITIS
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_ITIS_URL_BASE_REQUETE'))
	/**
	 * Préfixe des URL du service web de ITIS.
	 * Le service fournit des données au format XML ou JSON
	 */
	define('_TAXONOMIE_ITIS_URL_BASE_REQUETE', 'http://www.itis.gov/ITISWebService/');
if (!defined('_TAXONOMIE_ITIS_URL_CITATION'))
	/**
	 * URL à fournir dans la citation du service ITIS.
	 */
	define('_TAXONOMIE_ITIS_URL_CITATION', 'http://www.itis.gov');
if (!defined('_TAXONOMIE_ITIS_LANGUE_DEFAUT'))
	/**
	 * Langue par défaut pour les api utilisant des noms communs
	 */
	define('_TAXONOMIE_ITIS_LANGUE_DEFAUT', 'english');

if (!defined('_TAXONOMIE_ITIS_REGEXP_RANKNAME'))
	/**
	 * Ligne d'un fichier ITIS hiérachie généré.
	 * Il est indispensable de respecter les majuscules des noms de groupe pour éviter de matcher
	 * les suborder, infrakingdom...
	 */
	define('_TAXONOMIE_ITIS_REGEXP_RANKNAME', '#(%groups_list%):\s*(\w+)\s*\[([^\]]*)\]\s*\[(\d+)\]#');


/**
 * Configuration de l'api du service web ITIS
 */
$itis_webservice = array(
	'search' => array(
		'commonname' => array(
			'function' => 'searchByCommonName',
			'argument' => 'srchKey',
			'list' => 'commonNames',
			'index' => 'commonName'
		),
		'scientificname' => array(
			'function' => 'searchByScientificName',
			'argument' => 'srchKey',
			'list' => 'scientificNames',
			'index' => 'combinedName'
		)
	),
	'vernacular' => array(
		'vernacularlanguage' => array(
			'function' => 'getTsnByVernacularLanguage',
			'argument' => 'language',
			'list' => 'ax23:vernacularTsns',
			'index' => 'commonName'
		)
	),
	'getfull' => array(
		'record' => array(
			'function' => 'getFullRecordFromTSN',
			'argument' => 'tsn',
			'list' => 'ns:return',
			'index' => 'commonNameList,expertList,taxRank,parentTSN,scientificName,taxonAuthor'
		)
	),
	'get' => array(
		'scientificname' => array(
			'multiple' => false,
			'function' => 'getScientificNameFromTSN',
			'argument' => 'tsn',
			'index' => 'combinedName',
		),
		'kingdomname' => array(
			'multiple' => false,
			'function' => 'getKingdomNameFromTSN',
			'argument' => 'tsn',
			'index' => 'kingdomName',
		),
		'parent' => array(
			'multiple' => false,
			'function' => 'getHierarchyUpFromTSN',
			'argument' => 'tsn',
			'index' => 'parentTsn',
		),
		'rankname' => array(
			'multiple' => false,
			'function' => 'getTaxonomicRankNameFromTSN',
			'argument' => 'tsn',
			'index' => 'rankName',
		),
		'author' => array(
			'multiple' => false,
			'function' => 'getTaxonAuthorshipFromTSN',
			'argument' => 'tsn',
			'index' => 'authorship',
		),
		'coremetadata' => array(
			'multiple' => false,
			'function' => 'getCoreMetadataFromTSN',
			'argument' => 'tsn',
			'index' => 'rankId',
		),
		'experts' => array(
			'multiple' => true,
			'function' => 'getExpertsFromTSN',
			'argument' => 'tsn',
			'list' => 'experts',
		),
		'commonnames' => array(
			'multiple' => true,
			'function' => 'getCommonNamesFromTSN',
			'argument' => 'tsn',
			'list' => 'commonNames',
		),
		'othersources' => array(
			'multiple' => true,
			'function' => 'getOtherSourcesFromTSN',
			'argument' => 'tsn',
			'list' => 'otherSources',
		),
		'hierarchyfull' => array(
			'multiple' => true,
			'function' => 'getFullHierarchyFromTSN',
			'argument' => 'tsn',
			'list' => 'hierarchyList',
		),
		'hierarchydown' => array(
			'multiple' => true,
			'function' => 'getHierarchyDownFromTSN',
			'argument' => 'tsn',
			'list' => 'hierarchyList',
		),
	),
);


/**
 * Recherche un taxon dans la base ITIS par son nom commun ou scientifique
 * et retourne son identifiant unique nommé tsn.
 *
 * @param string	$api
 * 		Recherche par nom commun ou par nom scientifique. Prend les valeurs 'commonname' ou 'scientificname'
 * @param string	$recherche
 * 		Nom à rechercher précisément. Seul le taxon dont le nom coincidera exactement sera retourné.
 *
 * @return int
 * 		Identifiant unique (tsn) dans la base ITIS ou 0 si la recherche échoue
 */
function itis_search_tsn($api, $recherche) {
	global $itis_webservice;
	$tsn = 0;

	// Normaliser la recherche: trim et mise en lettres minuscules
	$recherche = strtolower(trim($recherche));

	// Construire l'URL de la fonction de recherche
	$url = itis_api2url('json', 'search', $api, rawurlencode($recherche));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// Récupération du TSN du taxon recherché
	$api = $itis_webservice['search'][$api];
	if (isset($data[$api['list']])
	AND $data[$api['list']]) {
		// La recherche peut renvoyer plusieurs taxons. On considère que le "bon" taxon
		// correspond à celui ont le nom est exactement celui recherché.
		foreach ($data[$api['list']] as $_data) {
			if ($_data
			AND (strcasecmp($_data[$api['index']], $recherche) == 0)) {
				// On est sur le bon taxon, on renvoie le TSN
				$tsn = intval($_data['tsn']);
				break;
			}
		}
	}

	return $tsn;
}


/**
 * Renvoie les informations demandées sur un taxon désigné par son identifiant unique (tsn).
 *
 * @param string	$api
 * 		Type d'information demandé. Prend les valeurs
 *      - 'scientificname' : le nom scientifique du taxon
 *      - 'kingdomname' : le règne du taxon
 *      - 'parent' : le taxon parent dont son tsn
 *      - 'rankname' : le rang taxonomique du taxon
 *      - 'author' : le ou les auteurs du taxon
 * 		- 'coremetadata' : les métadonnées (???)
 * 		- 'experts' : les experts du taxon
 * 		- 'commonnames' : le ou les noms communs
 * 		- 'othersources' : les sources d'information sur le taxon
 * 		- 'hierarchyfull' : la hiérarchie complète jusqu'au taxon
 * 		- 'hierarchydown' : la hiérarchie ???
 * @param int		$tsn
 * 		Identifiant unique du taxon dans la base ITIS (tsn)
 *
 * @return array
 * 		Le tableau renvoyé est caractéristique du type d'information demandé.
 */
function itis_get_information($api, $tsn) {
	global $itis_webservice;
	$output =array();

	// Construire l'URL de l'api sollicitée
	$url = itis_api2url('json', 'get', $api, strval($tsn));

	// Acquisition des données spécifiées par l'url
	include_spip('inc/taxonomer');
	$data = url2json_data($url);

	// On vérifie que le tableau est complet sinon on retourne un tableau vide
	$api = $itis_webservice['get'][$api];
	if ($api['multiple']) {
		if (isset($data[$api['list']][0])
		AND $data[$api['list']][0]) {
			$output = $data[$api['list']];
		}
	}
	else {
		if (isset($data[$api['index']])
		AND $data[$api['index']]) {
			$output = $data;
		}
	}

	return $output;
}


/**
 * Renvoie l'ensemble des informations sur un taxon désigné par son identifiant unique (tsn).
 *
 * @param int	$tsn
 * 		Identifiant unique du taxon dans la base ITIS (tsn)
 *
 * @return array
 */
function itis_get_record($tsn) {
	global $itis_webservice;
	$output =array();

	// Construire l'URL de l'api sollicitée
	$url = itis_api2url('xml', 'getfull', 'record', strval($tsn));

	// Acquisition des données spécifiées par l'url
	$api = $itis_webservice['getfull']['record'];
	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// Suppression du préfixe ax21: des balises afin de récupérer des index associatifs non préfixés
	$flux = str_replace('ax21:parentTsn', 'ax21:TsnParent', $flux );
	$flux = str_replace('ax21:', '', $flux);

	// Phrasage de la chaine XML obtenue
	include_spip('inc/xml');
	$arbre = spip_xml_parse($flux);
	if (spip_xml_match_nodes(",^{$api['list']},", $arbre, $matches) > 0) {
		$infos = reset($matches);
		foreach ($infos[0] as $_info) {
			$info = array();
			$output[] = $info;
		}
	}

	return $output;
}


/**
 * Lecture du fichier hiérarchique ITIS des taxons d'un règne.
 *
 * @param string	$kingdom
 * 		Nom scientifique du règne en lettres minuscules (animalia, plantae, fungi)
 * @param string	$upto
 * 		Rang taxonomique minimal jusqu'où charger le règne. Ce rang est fourni en anglais et
 * 		correspond à : phylum (pour le règne animalia) ou division (pour les règnes fungi et plantae),
 * 		class, order, family, genus.
 * @param int		$sha_file
 *		Sha calculé à partir du fichier de taxons correspondant au règne choisi. Le sha est retourné
 * 		par la fonction afin d'être stocké par le plugin.
 *
 * @return array
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
		// Construire la regexp qui permet de limiter la hiérarchie comme demandée
		$groups_list = implode('|', array_map('ucfirst', array_slice(array_flip($group_ids), 0, $group_ids[$upto])));
		$regexp = str_replace('%groups_list%', $groups_list, _TAXONOMIE_ITIS_REGEXP_RANKNAME);

		$file = find_in_path('services/itis/' . ucfirst($kingdom) . '_Genus.txt');
		if (file_exists($file)
		AND ($sha_file = sha1_file($file))) {
			$lines = file($file);
			if ($lines) {
				$tsn = 0;
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
						$taxon['auteur'] = mb_convert_encoding(trim($match[3]), 'ISO-8859-1');
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
 * Lit le fichier des noms communs (tout règne confondu) d'une langue donnée et renvoie un tableau
 * de tous ces noms indexés par leur TSN.
 *
 * @param string	$language_code
 * @param int		$sha_file
 *
 * @return array
 */
function itis_read_vernaculars($language_code, &$sha_file) {
	$vernaculars =array();
	$sha_file = false;

	// Ouvrir le fichier de nom communs correspondant au code de langue spécifié
	$file = find_in_path("services/itis/vernaculars_${language_code}.csv");
	if (file_exists($file)
	AND ($sha_file = sha1_file($file))) {
		// Lecture du fichier csv comme un fichier texte sachant que :
		// - le délimiteur de colonne est une virgule
		// - le caractère d'encadrement d'un texte est le double-quotes
		$lines = file($file);
		if ($lines) {
			// Créer le tableau de sortie à partir du tableau issu du csv (tsn, nom commun)
			$tag_language = '[' . $language_code . ']';
			foreach ($lines as $_line) {
				$name = explode(',', trim($_line));
				$vernaculars[intval($name[0])] = $tag_language . trim($name[1], '"');
			}
		}
	}

	return $vernaculars;
}


/**
 * @param $language_code
 *
 * @return array
 */
function itis_list_vernaculars($language_code) {
	global $itis_webservice;
	$vernaculars =array();

	// Construire l'URL de l'api sollicitée
	$url = itis_api2url('xml', 'vernacular', 'vernacularlanguage', itis_code2language($language_code));

	// Acquisition des données spécifiées par l'url
	$api = $itis_webservice['vernacular']['vernacularlanguage'];
	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// Suppression du préfixe ax21: des balises afin de récupérer des index associatif non préfixés
	$flux = str_replace('ax21:', '', $flux);

	// Phrasage de la chaine XML obtenue
	include_spip('inc/xml');
	$arbre = spip_xml_parse($flux);
	if (spip_xml_match_nodes(",^{$api['list']},", $arbre, $matches) > 0) {
		$names = reset($matches);
		$tag_language = '[' . $language_code . ']';
		foreach ($names as $_name) {
			$vernaculars[$_name['tsn'][0]] .= $tag_language . $_name[$api['index']][0];
		}
	}

	return $vernaculars;
}

/**
 * @return string
 */
function itis_citation() {
	$link = '<a href="' . _TAXONOMIE_ITIS_URL_CITATION . '">' . _TAXONOMIE_ITIS_URL_CITATION . '</a>';
	return _T('taxonomie:citation_itis', array('url' => $link));
}

/**
 * @param $format
 * @param $area
 * @param $api
 * @param $key
 *
 * @return string
 */
function itis_api2url($format, $area, $api, $key) {
	global $itis_webservice;

	// Construire l'URL de l'api sollicitée
	$url = _TAXONOMIE_ITIS_URL_BASE_REQUETE
		 . ($format=='json' ? 'jsonservice/' : 'services/ITISService/')
		 . $itis_webservice[$area][$api]['function'] . '?'
		 . $itis_webservice[$area][$api]['argument'] . '=' . $key;

	return $url;
}

/**
 * @param $language_code
 *
 * @return string
 */
function itis_code2language($language_code) {
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