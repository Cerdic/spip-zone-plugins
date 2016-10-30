<?php
/**
 * Ce fichier contient la fonction générique de lecture d'un fichier CSV en un tableau d'éléments d'une
 * table de la base de données.
 *
 * @package SPIP\ISOCODE\OUTILS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Constitue, à partir d'un fichier CSV donné, un tableau des éléments prêt à être inséré dans une table
 * de la base de données.
 * La fonction utilise le service et le nom de table pour récupérer la configuration permettant l'analyse
 * du fichier et sa traduction en élements de la table (délimiteur, nom des colonnes...).
 * Il est possible, pour chaque élément ou pour l'ensemble d'appliquer une fonction spécifique à la table
 * qui complète l'élément.
 *
 * @api
 *
 * @param string $service
 * 		Nom du service associé à la lecture de la table.
 * @param string $table
 * 		Nom de la table concernée par la lecture du fichier CSV.
 * @param string $sha_stocke
 * 		SHA du fichier CSV stocké dans la meta associée à la table ou `false` si la table n'est pas chargée.
 *
 * @return array
 * 		Tableau à deux éléments:
 * 		- index 0 : la liste des éléments à enregistrer dans la table concernée
 * 		- index 1 : le sha256 du fichier CSV source des éléments de la table
 */
function inc_isocode_read_file_csv($service, $table) {

	// Initialisations
	$records = array();
	$sha_file = false;
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
		// On ne lit le fichier que si celui-ci a changé.
		include_spip('isocode_fonctions');
		if (!isocode_comparer_sha($sha_file, $table)) {
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
	}

	return array($records, $sha_file);
}
