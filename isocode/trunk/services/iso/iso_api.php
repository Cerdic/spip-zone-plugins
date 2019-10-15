<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service ISO.
 * Etant donné que la lecture des sources est réalisée par une fonction générique `lire_source` ce
 * fichier ne contient que les fonctions spécifiques permettant de compléter les champs de base fournis
 * par la source.
 *
 * @package SPIP\ISOCODE\SERVICES\ISO
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_ISOCODE_SIL_ISO639_3_ENDPOINT')) {
	/**
	 * URL de base pour charger la page de documentation d'un code de langue alpha-3 sur le site
	 * sil.org. Complément à la table iso639families
	 */
	define('_ISOCODE_SIL_ISO639_3_ENDPOINT', 'http://www-01.sil.org/iso639-3/documentation.asp?id=');
}
if (!defined('_ISOCODE_LOC_ISO639_5_HIERARCHY')) {
	/**
	 * URL de base pour charger la page du tableau de la hiérarchie ISO-639-5 sur le site
	 * de la Library of Congress. Complément à la table iso639families
	 */
	define('_ISOCODE_LOC_ISO639_5_HIERARCHY', 'https://www.loc.gov/standards/iso639-5/hier.php');
}
if (!defined('_ISOCODE_GEONAMES_INFORMATIONS_PAYS')) {
	/**
	 * Chemin du fichier Geonames contenant des informations géographiques sur les pays.
	 * Complément à la table iso639countries.
	 */
	define('_ISOCODE_GEONAMES_INFORMATIONS_PAYS', 'services/iso/iso3166countries-geonames-info.txt');
}
if (!defined('_ISOCODE_M49_REGION_PAYS')) {
	/**
	 * Chemin du fichier des régions-pays M49 contenant la région de rattachement des pays.
	 * Complément à la table iso639countries.
	 */
	define('_ISOCODE_M49_REGION_PAYS', 'services/m49/m49regions_countries.txt');
}
if (!defined('_ISOCODE_IOTA_ISO4217_SYMBOL')) {
	/**
	 * URL de base pour charger la page du tableau des devises ISO-4217 sur le site
	 * de IOTA Finance qui permet de compléter les informations de base de l'ISO-4217.
	 * Complément à la table iso639currencies.
	 */
	define('_ISOCODE_IOTA_ISO4217_SYMBOL', 'http://www.iotafinance.com/Codes-ISO-Devises.html');
}


$GLOBALS['isocode']['iso']['tables'] = array(
	'iso639codes'       => array(
		'basic_fields' => array(
			'Id'            => 'code_639_3',
			'Part2B'        => 'code_639_2b',
			'Part2T'        => 'code_639_2t',
			'Part1'         => 'code_639_1',
			'Scope'         => 'scope',
			'Language_Type' => 'type',
			'Ref_Name'      => 'ref_name',
			'Comment'       => 'comment'
		),
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.tab'
	),
	'iso639names'       => array(
		'basic_fields' => array(
			'Id'            => 'code_639_3',
			'Print_Name'    => 'print_name',
			'Inverted_Name' => 'inverted_name'
		),
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.tab'
	),
	'iso639macros'      => array(
		'basic_fields' => array(
			'M_Id'     => 'macro_639_3',
			'I_Id'     => 'code_639_3',
			'I_Status' => 'status'
		),
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.tab'
	),
	'iso639retirements' => array(
		'basic_fields' => array(
			'Id'         => 'code_639_3',
			'Ref_Name'   => 'ref_name',
			'Ret_Reason' => 'ret_reason',
			'Change_To'  => 'change_to',
			'Ret_Remedy' => 'ret_remedy',
			'Effective'  => 'effective_date'
		),
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.tab'
	),
	'iso639families'    => array(
		'basic_fields' => array(
			'URI'             => 'uri',
			'code'            => 'code_639_5',
			'Label (English)' => 'label_en',
			'Label (French)'  => 'label_fr'
		),
		'addon_fields' => array(
			'sil' => array(
				'Equivalent' => 'code_639_1',
				'Code set'   => 'code_set',
				'Code sets'  => 'code_set',
				'Scope'      => 'scope'
			),
			'loc' => array(
				'Hierarchy' => 'hierarchy',
				'parent'    => 'parent'			// Colonne est calculée à partir de la hiérarchie
			)
		),
		'label_field'  => true,
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.tab'
	),
	'iso15924scripts'   => array(
		'basic_fields' => array(
			'Code'         => 'code_15924',
			'English Name' => 'label_en',
			'Nom français' => 'label_fr',
			'N°'           => 'code_num',
			'PVA'          => 'alias_en',
			'Date'         => 'date_ref',
		),
		'label_field'  => true,
		'populating'   => 'file_csv',
		'delimiter'    => ';',
		'extension'    => '.txt'
	),
	'iso3166countries'  => array(
		'basic_fields' => array(
			'English name' => 'label_en',
			'French name'  => 'label_fr',
			'Alpha-2'      => 'code_alpha2',
			'Alpha-3'      => 'code_alpha3',
			'Numeric'      => 'code_num',
		),
		'addon_fields' => array(
			'geonames' => array(
				'Capital'        => 'capital',
				'Area(in sq km)' => 'area',
				'Population'     => 'population',
				'Continent'      => 'code_continent',
				'tld'            => 'tld',
				'CurrencyCode'   => 'code_4217_3',
				'CurrencyName'   => 'currency_en',
				'Phone'          => 'phone_id',
			),
			'm49'      => array(
				'parent'         => 'code_num_region',
			)
		),
		'label_field'  => true,
		'populating'   => 'file_csv',
		'delimiter'    => ';',
		'extension'    => '.txt'
	),
	'iso3166subdivisions' => array(
		'basic_fields' => array(
			'3166-2 code'          => 'code_3166_2',
			'Country'              => 'country',
			'Subdivision category' => 'type',
			'Parent subdivision'   => 'parent',
			'Language code'        => 'language',
			'Subdivision name'     => 'label',
		),
		'unused_fields' => array(
			'language'        => '',
		),
		'addon_fields' => array(
			'insee'    => array(
				'ccnr'         => 'code_specific',
			)
		),
		'populating'   => 'file_csv',
		'delimiter'    => "\t",
		'extension'    => '.txt',
	),
	'iso4217currencies' => array(
		'basic_fields' => array(
			'Ccy'        => 'code_4217_3',
			'CcyNbr'     => 'code_num',
			'CcyNm'      => 'label_en',
			'CcyMnrUnts' => 'minor_units',
		),
		'addon_fields' => array(
			'iota' => array(
				'Symbol devise' => 'symbol',
				'Devise'        => 'label_fr',
			)
		),
		'label_field'  => true,
		'populating'   => 'file_xml',
		'extension'    => '.xml',
		'base'         => 'CcyTbl/CcyNtry'      // clé à laquelle débute la liste des éléments
	),
);


// ----------------------------------------------------------------------------
// ----------------- API du service ISO - Actions spécifiques -----------------
// ----------------------------------------------------------------------------

function iso639families_completer_enregistrement($enregistrement, $config) {

	// Initialisation
	static $enregistrement_sil_defaut = array();

	// Initialisation des champs additionnels dans l'enregistrement passé en argument
	// (qui contient déjà les champs de base).
	$config_champs_sil = $config['addon_fields']['sil'];
	if (!$enregistrement_sil_defaut) {
		include_spip('inc/isocode_sourcer');
		$enregistrement_sil_defaut = initialiser_enregistrement('iso639families', $config_champs_sil);
	}
	$enregistrement = array_merge($enregistrement, $enregistrement_sil_defaut);

	// On récupère la page de description de la famille sur le site SIL.
	include_spip('inc/distant');
	$url = _ISOCODE_SIL_ISO639_3_ENDPOINT . $enregistrement['code_639_5'];
	$flux = recuperer_url($url, array('transcoder' => true));

	// On décrypte la page et principalement le premier tableau pour en extraire les données
	// additionnelles suivantes :
	// - scope : C(ollective)
	// - equivalent : éventuellement le code ISO-639-1
	// - code set(s) : ISO-639-5 et/ou ISO-639-2
	include_spip('inc/filtres');
	$table = extraire_balise($flux['page'], 'table');
	if ($table) {
		// On extrait la première table de la page qui contient les données voulues
		$lignes = extraire_balises($table, 'tr');
		if ($lignes) {
			foreach ($lignes as $_ligne) {
				// Chaque ligne de la table est composée de deux colonnes, la première le libellé
				// et la deuxième la valeur.
				$colonnes = extraire_balises($_ligne, 'td');
				$colonnes = array_map('supprimer_tags', $colonnes);
				$colonnes = array_map('trim', $colonnes);
				if (count($colonnes) == 2) {
					$titre = trim(substr($colonnes[0], 0, strpos($colonnes[0], ':')));
					if (isset($config_champs_sil[$titre])) {
						$valeur = str_replace(' ', '', $colonnes[1]);
						switch ($titre) {
							case 'Equivalent':
								$equivalent = explode(':', $valeur);
								$enregistrement[$config_champs_sil[$titre]] = isset($equivalent[1]) ? trim($equivalent[1]) : '';
								break;
							case 'Code sets':
							case 'Code set':
								$enregistrement[$config_champs_sil[$titre]] = str_replace('and', ',', $valeur);
								break;
							case 'Scope':
								$enregistrement[$config_champs_sil[$titre]] = substr($valeur, 0, 1);
								break;
							default:
								break;
						}
					}
				}
			}
		}
	}

	return $enregistrement;
}


function iso639families_completer_table($enregistrements, $config) {

	// Initialisation des champs additionnels
	$hierarchies = array();
	$config_champs_loc = $config['addon_fields']['loc'];
	include_spip('inc/isocode_sourcer');
	$enregistrement_loc_defaut = initialiser_enregistrement('iso639families', $config_champs_loc);

	// On récupère la page de description de la famille sur le site SIL.
	include_spip('inc/distant');
	$url = _ISOCODE_LOC_ISO639_5_HIERARCHY;
	$flux = recuperer_url($url, array('transcoder' => true));

	// On décrypte la page et principalement le tableau pour en extraire la colonne hiérarchie
	// de chaque famille et créer la colonne parent dans la table iso639families.
	include_spip('inc/filtres');
	$table = extraire_balise($flux['page'], 'table');
	if ($table) {
		// On extrait la première table de la page qui contient les données voulues
		$lignes = extraire_balises($table, 'tr');
		if ($lignes) {
			// La première ligne du tableau est celle des titres de colonnes : on la supprime.
			array_shift($lignes);
			foreach ($lignes as $_ligne) {
				// Chaque ligne de la table est composée de deux colonnes, le première le libellé
				// et la deuxième la valeur.
				$colonnes = extraire_balises($_ligne, 'td');
				$colonnes = array_map('supprimer_tags', $colonnes);
				if (count($colonnes) >= 2) {
					// La première colonne contient la hiérarchie et la seconde le code alpha-3 de la famille.
					$code = trim($colonnes[1]);
					$hierarchies[$code] = str_replace(array(' ', ':'), array('', ','), trim($colonnes[0]));
				}
			}
		}
	}

	// On complète maintenant le tableau des enregistrements avec la colonne additionnelle hierarchy et la colonne
	// dérivée parent qui ne contient que le code alpha-3 de la famille parente si elle existe.
	foreach ($enregistrements as $_cle => $_enregistrement) {
		$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrement_loc_defaut);
		$code = $_enregistrement['code_639_5'];
		if (isset($hierarchies[$code])) {
			$enregistrements[$_cle][$config_champs_loc['Hierarchy']] = $hierarchies[$code];
			// Calcul du parent : si la hiérarchie ne contient qu'un code c'est qu'il n'y a pas de parent.
			// Sinon, le parent est le premier code qui précède le code du record.
			$parents = explode(',', $hierarchies[$code]);
			if (count($parents) > 1) {
				array_pop($parents);
				$enregistrements[$_cle][$config_champs_loc['parent']] = array_pop($parents);
			}
		}
	}

	return $enregistrements;
}


function iso3166countries_completer_table($enregistrements, $config) {

	// Inclusion de l'API.
	include_spip('inc/isocode_sourcer');

	// Lecture du fichier CSV geonames-countryInfo.txt pour récupérer les informations additionnelles.
	// Le délimiteur est une tabulation.
	// -- Initialisation des champs additionnels
	$enregistrements_geo = array();
	$config_champs_geo = $config['addon_fields']['geonames'];
	$enregistrement_geo_defaut = initialiser_enregistrement('iso3166countries', $config_champs_geo);

	// -- Lecture du fichier
	$fichier = find_in_path(_ISOCODE_GEONAMES_INFORMATIONS_PAYS);
	$separateur = "\t";
	$lignes = file($fichier);
	if ($lignes) {
		$titres = array();
		$index_code_pays = null;
		foreach ($lignes as $_numero => $_ligne) {
			$valeurs = explode($separateur, trim($_ligne, "\r\n"));
			if ($_numero == 0) {
				// Stockage des noms de colonnes car la première ligne contient toujours le header et de
				// l'index correspondant au code ISO-3166 alpha2 du pays qui se nomme ISO dans le fichier CSV.
				$titres = $valeurs;
				$index_code_pays = array_search('ISO', $titres);
			} else {
				// On extrait de chaque ligne les informations additionnelles du pays ainsi que le code alpha2 du pays
				// qui servira d'index du tableau constitué.
				// On ne sélectionne que les colonnes correspondant à des champs additionnels.
				$enregistrement_geo = $enregistrement_geo_defaut;
				foreach ($titres as $_cle => $_titre) {
					$titre = trim($_titre);
					if (isset($config_champs_geo[$titre]) and !empty($valeurs[$_cle])) {
						$enregistrement_geo[$config_champs_geo[$titre]] = trim($valeurs[$_cle]);
					}
				}
				if (isset($valeurs[$index_code_pays])) {
					$enregistrements_geo[$valeurs[$index_code_pays]] = $enregistrement_geo;
				}
			}
		}
	}

	// Lecture du fichier CSV m49regions_countries.txt pour récupérer le lien entre les pays et leur région de
	// rattachement (indicatif M49).
	// Le délimiteur est un point-virgule.
	// -- Initialisation des champs additionnels
	$enregistrements_m49 = array();
	$config_champs_m49 = $config['addon_fields']['m49'];
	$enregistrement_m49_defaut = initialiser_enregistrement('iso3166countries', $config_champs_m49);

	// -- Lecture du fichier
	$fichier = find_in_path(_ISOCODE_M49_REGION_PAYS);
	$separateur = ';';
	$lignes = file($fichier);
	if ($lignes) {
		$titres = array();
		$index_code_pays = null;
		foreach ($lignes as $_numero => $_ligne) {
			$valeurs = explode($separateur, trim($_ligne, "\r\n"));
			if ($_numero == 0) {
				// Stockage des noms de colonnes car la première ligne contient toujours le header et de
				// l'index correspondant au code ISO-3166 numérique du pays qui se nomme 'code_num' dans le fichier CSV.
				$titres = $valeurs;
				$index_code_pays = array_search('code_num', $titres);
			} else {
				// On extrait de chaque ligne la région de rattachement du pays ainsi que le code numérique du pays
				// qui servira d'index du tableau constitué.
				// On ne sélectionne que les colonnes correspondant à des champs additionnels.
				$enregistrement_m49 = $enregistrement_m49_defaut;
				foreach ($titres as $_cle => $_titre) {
					$titre = trim($_titre);
					if (isset($config_champs_m49[$titre]) and !empty($valeurs[$_cle])) {
						$enregistrement_m49[$config_champs_m49[$titre]] = trim($valeurs[$_cle]);
					}
				}
				if (isset($valeurs[$index_code_pays])) {
					$enregistrements_m49[$valeurs[$index_code_pays]] = $enregistrement_m49;
				}
			}
		}
	}

	// On complète maintenant le tableau des enregistrements avec les informations glanées dans le fichier geonames
	// et dans celui du M49.
	foreach ($enregistrements as $_cle => $_enregistrement) {
		// Les informations Geonames
		$code = $_enregistrement['code_alpha2'];
		if (isset($enregistrements_geo[$code])) {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrements_geo[$code]);
		} else {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrement_geo_defaut);
		}

		// La région M49
		$code = $_enregistrement['code_num'];
		if (isset($enregistrements_m49[$code])) {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrements_m49[$code]);
		} else {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrement_m49_defaut);
		}
	}

	return $enregistrements;
}


function iso3166subdivisions_completer_enregistrement($enregistrement, $config) {

	// La finalisation de l'enregistrement consiste à élaborer la version multilangue du champ label
	// en considérant le champ language qui contient le codet alpha2 de la langue.
	// Cette fonction est appelée pour le premier enregistrement ayant la clé primaire concernée, les autres
	// enregistrements avec cette même clé sont traités dans la fonction fusionner_enregistrement().
	// De fait, le champ label ne contient que le texte sans multi.
	$enregistrement['label'] = "<multi>[{$enregistrement['language']}]"
		. $enregistrement['label']
		.'</multi>';

	// On supprime maintenant le champ language qui ne sert plus à rien
	unset($enregistrement['language']);

	return $enregistrement;
}


function iso3166subdivisions_fusionner_enregistrement($enregistrements, $index_enregistrement, $enregistrement, $config) {

	// Cette fonction est appelée alors qu'il existe au moins un premier enregistrement ayant la clé primaire concernée.
	// De fait, il suffit de compléter le champ label avec la traduction dans la langue de l'enregistrement coourant.
	$label = $enregistrements[$index_enregistrement]['label'];
	$enregistrements[$index_enregistrement]['label'] = '<multi>'
		. extraire_balise($label, 'multi')
		. "[{$enregistrement['language']}]"
		. $enregistrement['label']
		. '</multi>';

	return $enregistrements;
}

function iso4217currencies_completer_table($enregistrements, $config) {

	// Initialisation des champs additionnels
	$enregistrements_iota = array();
	$config_champs_iota = $config['addon_fields']['iota'];
	include_spip('inc/isocode_sourcer');
	$enregistrement_iota_defaut = initialiser_enregistrement('iso4217currencies', $config_champs_iota);

	// On récupère la page de description de la famille sur le site SIL.
	include_spip('inc/distant');
	$url = _ISOCODE_IOTA_ISO4217_SYMBOL;
	$flux = recuperer_url($url, array('transcoder' => true));

	include_spip('inc/filtres');
	$table = extraire_balise($flux['page'], 'table');
	if ($table) {
		// On extrait la première table de la page qui contient les données voulues
		$lignes = extraire_balises($table, 'tr');
		if ($lignes) {
			$cles_iota = array();
			$index_code_devise = null;
			foreach ($lignes as $_numero => $_ligne) {
				$balise_colonne = $_numero == 0 ? 'th' : 'td';
				$colonnes = extraire_balises($_ligne, $balise_colonne);
				$colonnes = array_map('supprimer_tags', $colonnes);
				$colonnes = array_map('trim', $colonnes);
				if ($_numero == 0) {
					// La première ligne du tableau est celle des titres de colonnes.
					// On détermine les index de colonnes correspondant aux champs additionnels configurés.
					foreach ($colonnes as $_cle => $_titre) {
						if (array_key_exists($_titre, $config_champs_iota)) {
							$cles_iota[$_titre] = $_cle;
						}
					}
					// On détermine l'index de la colonne qui porte le code alpha3 ISO-4217 nommé
					$index_code_devise = array_search('ISO devise', $colonnes);
				} else {
					// Chaque ligne de la table est composée de plusieurs colonnes, la première étant le code alpha3
					// de la devise selon l'ISO-4217
					$enregistrement_iota = $enregistrement_iota_defaut;
					foreach ($cles_iota as $_titre => $_cle) {
						if ($colonnes[$_cle]) {
							$enregistrement_iota[$config_champs_iota[$_titre]] = $colonnes[$_cle];
						}
					}
					$code = $colonnes[$index_code_devise];
					$enregistrements_iota[$code] = $enregistrement_iota;
				}
			}
		}
	}

	// On complète maintenant le tableau des enregistrements avec les colonne additionnelles symbole
	// et devise en français.
	// Etant donné qu'il faut que tous les enregistrements possèdent la même structure, si une devise ne
	// possède pas d'informations complémentaires IOTA on lui adjoint des colonnes par défaut.
	foreach ($enregistrements as $_cle => $_enregistrement) {
		$code = $_enregistrement['code_4217_3'];
		if (isset($enregistrements_iota[$code])) {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrements_iota[$code]);
		} else {
			$enregistrements[$_cle] = array_merge($enregistrements[$_cle], $enregistrement_iota_defaut);
		}
	}

	return $enregistrements;
}
