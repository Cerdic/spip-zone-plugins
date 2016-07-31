<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant les services ISO.
 *
 * @package SPIP\ISOCODE\SERVICES\ISO
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_ISOCODE_SIL_ISO639_3_ENDPOINT')) {
	/**
	 * URL de base pour charger la page de documentation d'un code de langue alpha-3 sur le site
	 * sil.org
	 */
	define('_ISOCODE_SIL_ISO639_3_ENDPOINT', 'http://www-01.sil.org/iso639-3/documentation.asp?id=');
}
if (!defined('_ISOCODE_LOC_ISO639_5_HIERARCHY')) {
	/**
	 * URL de base pour charger la page du tableau de la hiérarchie ISO-639-5 sur le site
	 * de la Library of Congress.
	 */
	define('_ISOCODE_LOC_ISO639_5_HIERARCHY', 'https://www.loc.gov/standards/iso639-5/hier.php');
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
		'addon_fields'   => array(
			'sil' => array(
				'Equivalent' => 'code_639_1',
				'Code set'   => 'code_set',
				'Code sets'  => 'code_set',
				'Scope'      => 'scope'
			),
			'loc' => array(
				'Hierarchy' => 'hierarchy'
			)
		),
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
		'populating'   => 'file_csv',
		'delimiter'    => ';',
		'extension'    => '.txt'
	),
);

// ----------------------------------------------------------------------------
// ----------------- API du service ISO - Actions spécifiques -----------------
// ----------------------------------------------------------------------------

function iso639families_complete_by_record($fields) {

	// Initialisation des champs additionnels
	$sil_to_spip = $GLOBALS['isocode']['iso']['tables']['iso639families']['addon_fields']['sil'];
	foreach ($sil_to_spip as $_label => $_field) {
		$fields[$_field] = '';
	}

	// On récupère la page de description de la famille sur le site SIL.
	include_spip('inc/distant');
	$url = _ISOCODE_SIL_ISO639_3_ENDPOINT . $fields['code_639_5'];
	$options = array('transcoder' => true);
	$flux = recuperer_url($url, $options);

	// On décrypte la page et principalement le premier tableau pour en extraire les données
	// additionnelles suivantes :
	// - scope : C(ollective)
	// - equivalent : éventuellement le code ISO-639-1
	// - code set : ISO-639-5 et/ou ISO-639-2
	include_spip('inc/filtres');
	$table = extraire_balise($flux['page'], 'table');
	if ($table) {
		// On extrait la première table de la page qui contient les données voulues
		$rows = extraire_balises($table, 'tr');
		if ($rows) {
			foreach ($rows as $_row) {
				// Chaque ligne de la table est composée de deux colonnes, le première le libellé
				// et la deuxième la valeur.
				$columns = extraire_balises($_row, 'td');
				$columns = array_map('supprimer_tags', $columns);
				if (count($columns) == 2) {
					$keys = explode(':', trim($columns[0]));
					$key = trim($keys[0]);
					$value = str_replace(' ', '', trim($columns[1]));
					switch ($key) {
						case 'Equivalent':
							$equivalent = explode(':', $value);
							$fields[$sil_to_spip[$key]] = isset($equivalent[1]) ? trim($equivalent[1]) : '';
							break;
						case 'Code sets':
						case 'Code set':
							$fields[$sil_to_spip[$key]] = str_replace('and', ',', $value);
							break;
						case 'Scope':
							$fields[$sil_to_spip[$key]] = substr($value, 0, 1);
							break;
						default:
							break;
					}
				}
			}
		}
	}

	return $fields;
}


function iso639families_complete_by_table($records) {

	// Initialisation des champs additionnels
	$hierarchies = array();
	$loc_to_spip = $GLOBALS['isocode']['iso']['tables']['iso639families']['addon_fields']['loc'];

	// On récupère la page de description de la famille sur le site SIL.
	include_spip('inc/distant');
	$url = _ISOCODE_LOC_ISO639_5_HIERARCHY;
	$options = array('transcoder' => true);
	$flux = recuperer_url($url, $options);

	// On décrypte la page et principalement le tableau pour en extraire la colonne hiérarchie
	// de chaque famille et créer la colonne parent dans la table iso639families.
	include_spip('inc/filtres');
	$table = extraire_balise($flux['page'], 'table');
	if ($table) {
		// On extrait la première table de la page qui contient les données voulues
		$rows = extraire_balises($table, 'tr');
		if ($rows) {
			// La première ligne du tableau est celle des titres de colonnes : on la supprime.
			array_shift($rows);
			foreach ($rows as $_row) {
				// Chaque ligne de la table est composée de deux colonnes, le première le libellé
				// et la deuxième la valeur.
				$columns = extraire_balises($_row, 'td');
				$columns = array_map('supprimer_tags', $columns);
				if (count($columns) >= 2) {
					// La première colonne contient la hiérarchie et la seconde le code alpha-3 de la famille.
					$code = trim($columns[1]);
					$hierarchies[$code] = str_replace(array(' ', ':'), array('', ','), trim($columns[0]));
				}
			}
		}
	}

	// On complète maintenant le tableau des enregistrements avec la colonne additionnelle hierarchy et la colonne
	// dérivée parent qui ne contient que le code alpha-3 de la famille parente si elle existe.
	foreach ($records as $_cle => $_record) {
		$code = $_record['code_639_5'];
		$records[$_cle]['parent'] = '';
		if (isset($hierarchies[$code])) {
			$records[$_cle][$loc_to_spip['Hierarchy']] = $hierarchies[$code];
			// Calcul du parent : si la hierarchie ne contient qu'un code c'est qu'il n'y a pas de parent.
			// Sinon, le parent est le premier code qui précède le code du record.
			$parents = explode(',', $hierarchies[$code]);
			if (count($parents) > 1) {
				array_pop($parents);
				$records[$_cle]['parent'] = array_pop($parents);
			}
		} else {
			$records[$_cle][$loc_to_spip['Hierarchy']] = '';
		}
	}

	return $records;
}
