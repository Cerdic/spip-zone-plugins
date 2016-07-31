<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin Codes de Langues.
 *
 * @package SPIP\ISOCODE\OUTILS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Compare le sha passé en argument pour la table concernée avec le sha stocké dans la meta
 * pour cette même table.
 *
 * @api
 *
 * @param string $sha
 * @param string $table
 *
 * @return bool
 */
function isocode_comparer_sha($sha, $table) {

	$sha_identique = false;

	// On récupère le sha de la table dans les metas si il existe (ie. la table a été chargée)
	include_spip('inc/config');
	$sha_stocke = lire_config("isocode/tables/${table}/sha", '');

	if ($sha_stocke and ($sha == $sha_stocke)) {
		$sha_identique = true;
	}

	return $sha_identique;
}


/**
 * @param string $service
 * @param string $table
 *
 * @api
 *
 * @return array
 */
function isocode_read_file_csv($service, $table) {

	// Initialisations
	$records = array();
	$sha_file = '';
	$f_complete_record = "${table}_complete_by_record";
	$f_complete_table = "${table}_complete_by_table";

	// Inclusion des configurations et des fonctions spécifiques au service qui fournit les données
	// de la table à remplir.
	include_spip("services/${service}/${service}_api");

	// Initialisations des données de configuration propre au service et à la table
	$file_extension = $GLOBALS['isocode'][$service]['tables'][$table]['extension'];
	$delimiter = $GLOBALS['isocode'][$service]['tables'][$table]['delimiter'];
	$fields_config = $GLOBALS['isocode'][$service]['tables'][$table]['basic_fields'];

	// Ouvrir le fichier des enregistrements de la table spécifiée.
	$file = find_in_path("services/${service}/${table}${file_extension}");
	if (file_exists($file) and ($sha_file = sha1_file($file))) {
		// Lecture du fichier au format CSV comme un fichier texte sachant :
		// - que le délimiteur de colonne est configuré pour chaque table
		// - et qu'il n'y a jamais de caractère d'enclosure dans ces fichiers csv
		$lines = file($file);
		if ($lines) {
			$headers = array();
			foreach ($lines as $_number => $_line) {
				$values = explode($delimiter, trim($_line, "\r\n"));
				if ($_number == 0) {
					// Stockage des noms de colonnes car la première ligne contient toujours le header
					$headers = $values;
				} else {
					// Création de chaque enregistrement de la table
					$fields = array();
					foreach ($headers as $_cle => $_header) {
						$fields[$fields_config[trim($_header)]] = isset($values[$_cle]) ? trim($values[$_cle]) : '';
					}
					// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter
					if (function_exists($f_complete_record)) {
						$fields = $f_complete_record($fields);
					}
					$records[] = $fields;
				}
			}
			// Si besoin on appelle une fonction pour toute la table
			if (function_exists($f_complete_table)) {
				$records = $f_complete_table($records);
			}
		}
	}

	return array($records, $sha_file);
}


/**
 * @param string $service
 * @param string $table
 * @param string $url
 * @param string $record_delimiter
 * @param string $field_regexp
 * @param array  $fields_config
 *
 * @return array
 */
function isocode_read_page_text($service, $table) {

	// Initialisations
	$records = array();
	$sha_file = false;
	$f_complete_record = "${table}_complete_by_record";
	$f_complete_table = "${table}_complete_by_table";

	// Inclusion des configurations et des fonctions spécifiques au service qui fournit les données
	// de la table à remplir.
	include_spip("services/${service}/${service}_api");

	// Initialisations des données de configuration propre au service et à la table
	$url = $GLOBALS['isocode'][$service]['tables'][$table]['url'];
	$parsing_config = $GLOBALS['isocode'][$service]['tables'][$table]['parsing'];
	$fields_config = $GLOBALS['isocode'][$service]['tables'][$table]['basic_fields'];

	// Acquisition de la page sur le site de l'IANA
	include_spip('inc/distant');
	$options = array();
	$flux = recuperer_url($url, $options);

	if (!empty($flux['page'])) {
		$elements = array();
		// Chaque élément est identifié soit par un délimiteur, soit par une regexp suivant la méthode configurée.
		if ($parsing_config['element']['method'] == 'explode') {
			// On récupére donc un tableau des éléments à lire en utilisant la fonction explode
			$elements = explode($parsing_config['element']['delimiter'], $flux['page']);
		} else {
		}

		// Chaque champ est identifié par une regexp qui permet d'extraire le nom du champ et sa valeur dans deux tableaux
		// distincts indexés de façon concomittante.
		// C'est pour l'instant la seule méthode permise sur un élément.
		foreach ($elements as $_element) {
			if (preg_match_all($_element, $parsing_config['fields']['regexp'], $matches)) {
				$fields = array();
				foreach ($matches[1] as $_cle => $_header) {
					$fields[$fields_config[trim($_header)]] = isset($matches[2][$_cle]) ? $matches[2][$_cle] : '';
				}
				// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter
				if (function_exists($f_complete_record)) {
					$fields = $f_complete_record($fields);
				}
				$records[] = $fields;
			}
		}

		// Si besoin on appelle une fonction pour toute la table
		if (function_exists($f_complete_table)) {
			$records = $f_complete_table($records);
		}
	}

	return array($records, $sha_file);
}
