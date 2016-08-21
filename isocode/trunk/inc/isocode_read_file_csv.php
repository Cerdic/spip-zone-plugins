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
 * @param string $service
 * @param string $table
 *
 * @api
 *
 * @return array
 */
function inc_isocode_read_file_csv($service, $table) {

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
