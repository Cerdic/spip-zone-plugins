<?php
/**
 * Ce fichier contient la fonction générique de lecture d'un fichier XML en un tableau d'éléments d'une
 * table de la base de données.
 *
 * @package SPIP\ISOCODE\OUTILS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Constitue, à partir d'un fichier XML donné, un tableau des éléments prêt à être inséré dans une table
 * de la base de données.
 * La fonction utilise le service et le nom de table pour récupérer la configuration permettant l'analyse
 * du fichier et sa traduction en élements de la table (nom des colonnes...).
 * Il est possible, pour chaque élément ou pour l'ensemble d'appliquer une fonction spécifique à la table
 * qui complète l'élément.
 *
 * @api
 *
 * @param string $service
 * 		Nom du service associé à la lecture de la table.
 * @param string $table
 * 		Nom de la table concernée par la lecture du fichier XML.
 * @param string $sha_stocke
 * 		SHA du fichier XML stocké dans la meta associée à la table ou `false` si la table n'est pas chargée.
 *
 * @return array
 * 		Tableau à deux éléments:
 * 		- index 0 : la liste des éléments à enregistrer dans la table concernée
 * 		- index 1 : le sha256 du fichier XML source des éléments de la table
 */
function inc_isocode_read_file_xml($service, $table) {

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
	$fields_config = $GLOBALS['isocode'][$service]['tables'][$table]['basic_fields'];
	$base = $GLOBALS['isocode'][$service]['tables'][$table]['base'];

	// Détermination de la clé primaire de la table
	include_spip('base/objets');
	$primary_key = id_table_objet($table);

	// Ouvrir le fichier des enregistrements de la table spécifiée.
	$file = find_in_path("services/${service}/${table}${file_extension}");
	if (file_exists($file) and ($sha_file = sha1_file($file))) {
		// On ne lit le fichier que si celui-ci a changé.
		include_spip('isocode_fonctions');
		if (!isocode_comparer_sha($sha_file, $table)) {
			// Lecture du fichier au format XML en utilisant le décodage JSON afin d'améliorer la performance
			// ainsi que la lisibilité du tableau obtenu.
			include_spip('inc/flock');
			lire_fichier($file, $xml);
			$entries = json_decode(json_encode(simplexml_load_string($xml)), true);

			include_spip('inc/filtres');
			if (table_valeur($entries, $base, '')) {
				// Enregistrer les clés priamires afin d'éviter de lister des doublons et donc
				// d'avoir une erreur SQL à l'INSERT.
				$primary_key_values = array();
				foreach (table_valeur($entries, $base, '') as $_entry) {
					// Création de chaque enregistrement de la table
					$fields = array();
					$primary_key_exists = false;
					$primary_key_value = '';
					foreach ($_entry as $_tag => $_value) {
						$tag = trim($_tag);
						// Seuls les champs identifiés dans la configuration sont récupérés dans le fichier
						if (isset($fields_config[$tag])) {
							$fields[$fields_config[$tag]] = $_value ? trim($_value) : '';
							if ($fields_config[$tag] == $primary_key) {
								$primary_key_exists = true;
								$primary_key_value = $fields[$fields_config[$tag]];
							}
						}
					}
					// On ajoute l'élément que si la clé primaire a bien été trouvée
					if ($primary_key_exists) {
						if (!in_array($primary_key_value, $primary_key_values)) {
							// On rajoute cette clé dans la liste
							$primary_key_values[] = $primary_key_value;
							// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter
							if (function_exists($f_complete_record)) {
								$fields = $f_complete_record($fields);
							}
							$records[] = $fields;
						} else {
							spip_log("L'entrée <" . implode(',', $_entry) . "> de la table <${table}> est en doublon", "isocode" . _LOG_ERREUR);
						}
					} else {
						spip_log("L'entrée <" . implode(',', $_entry) . "> de la table <${table}> n'a pas de clé primaire", "isocode" . _LOG_ERREUR);
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
