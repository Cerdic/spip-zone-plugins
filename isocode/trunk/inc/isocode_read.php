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
 * Constitue, à partir, d'un fichier CSV ou XML ou d'une page HTML au format texte, un tableau des éléments
 * prêt à être inséré dans une table de la base de données.
 * La fonction utilise le service et le nom de table pour récupérer la configuration permettant l'analyse
 * du fichier et sa traduction en élements de la table (délimiteur ou regexp, nom des colonnes...).
 * Il est possible, pour chaque élément ou pour l'ensemble d'appliquer une fonction spécifique à la table
 * qui complète l'élément.
 *
 * @param string $service
 *        Nom du service associé à la lecture de la table.
 * @param string $table
 *        Nom de la table concernée par la lecture de la source.
 *
 * @return array
 *        Tableau à deux éléments:
 *        - index 0 : la liste des éléments à enregistrer dans la table concernée
 *        - index 1 : le sha256 du fichier CSV source des éléments de la table
 */
function inc_isocode_read($service, $table) {

	// Initialisations
	$records = array();
	$sha_file = false;
	$f_complete_record = "${table}_complete_by_record";
	$f_complete_table = "${table}_complete_by_table";

	// Inclusion des configurations et des fonctions spécifiques au service qui fournit les données
	// de la table à remplir.
	include_spip("services/${service}/${service}_api");

	// Acquisition de la configuration de lecture pour la table concernée.
	$config = $GLOBALS['isocode'][$service]['tables'][$table];

	// Détermination de la clé primaire de la table
	$primary_key_table = get_primary_key($table);

	// Initialisation d'un élément de la table par défaut (uniquement les champs de base)
	// Cela permet de s'assurer que chaque élément du tableau de sortie aura la même structure
	// quelque soit les données lues dans la source.
	$default_fields = init_element_fields($table, $config['basic_fields']);

	// Récupération du contenu du fichier ou de la page HTML source et du sha associé. Pour les fichiers CSV
	// on renvoie aussi la liste des titres des colonnes.
	list($content, $header, $sha) = get_source_content($service, $table, $config);
	if ($content and $sha and $default_fields) {
		// On n'analyse le contenu que si celui-ci a changé (sha différent de celui stocké).
		include_spip('isocode_fonctions');
		if (!isocode_comparer_sha($sha, $table)) {
			$primary_key_values = array();
			foreach ($content as $_element) {
				// Pour chaque élément on récupère un tableau associatif [titre colonne] = valeur colonne.
				$values = get_element_values($_element, $header, $config);
				// Création de chaque enregistrement de la table
				$fields = $default_fields;
				$pkey_element_exists = false;
				$pkey_element = array();
				foreach ($values as $_key => $_value) {
					$key = trim($_key);
					// Seuls les champs identifiés dans la configuration sont récupérés dans le fichier
					if (isset($fields_config[$key])) {
						$fields[$fields_config[$key]] = $_value ? trim($_value) : '';
						// Vérifier si le champ en cours fait partie de la clé primaire et élaborer la clé
						// primaire de l'élément en cours
						if (in_array($fields_config[$key], $primary_key_table['list'])) {
							$pkey_element[$fields_config[$key]] = $_value;
							if (count($pkey_element) == $primary_key_table['count']) {
								$pkey_element_exists = true;
							}
						}
					} else {
						spip_log("Le champ <${_key}> n'existe pas dans la configuration de la table ${table}", 'isocode' . _LOG_INFO);
					}
				}
				// On ajoute l'élément que si la clé primaire a bien été trouvée et si la valeur de cette clé
				// n'est pas en doublon avec un élément déjà enregistré.
				if ($pkey_element_exists) {
					ksort($pkey_element);
					$pkey_element_value = implode(',', $pkey_element);
					if (!in_array($pkey_element_value, $primary_key_values)) {
						// On rajoute cette clé dans la liste
						$primary_key_values[] = $pkey_element_value;
						// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter
						if (function_exists($f_complete_record)) {
							$fields = $f_complete_record($fields);
						}
						$records[] = $fields;
					} else {
						spip_log("L'entrée de clé primaire <${pkey_element_value}> de la table <${table}> est en doublon", 'isocode' . _LOG_ERREUR);
					}
				} else {
					spip_log("L'entrée <" . var_export($_element, true) . "> de la table <${table}> n'a pas de clé primaire", 'isocode' . _LOG_ERREUR);
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
 * @param $table
 *
 * @return array
 */
function get_primary_key($table) {

	include_spip('base/objets');
	$primary_key = array();

	if ($id_key = id_table_objet($table)) {
		// On stocke la clé sous forme de liste pour les tests d'appartenance.
		$primary_key['list'] = explode(',', $id_key);
		// On trie la liste et on recompose la clé sous forme de chaine pour la gestion des doublons.
		sort($primary_key['list']);
		$primary_key['name'] = implode(',', $primary_key['list']);
		// On stocke le nombre de champs de la clé.
		$primary_key['count'] = count($primary_key['list']);
	}

	return $primary_key;
}


/**
 * Initialise un élément d'une table donnée avec les valeurs par défaut configurées dans
 * la déclaration de la base ou avec une valeur prédéfinie par type.
 *
 * @param string $table
 *        Nom de la table concernée par la lecture sans le préfixe `spip_`.
 * @param array  $fields_config
 *        Configuration de la correspondance entre le nom de la donnée dans la source
 *        et celui du champ dans la table.
 *
 * @return array
 */
function init_element_fields($table, $fields_config) {

	$fields = array();

	// Acquisition de la description de la table (champs, clés, jointures) et définition
	// de la regexp permettant d'isoler la valeur par défaut si elle existe.
	$description = sql_showtable("spip_${table}");
	$regexp_default = '/DEFAULT\s+\'(.*)\'/i';

	if (!empty($description['field'])) {
		foreach ($fields_config as $_field) {
			if (isset($description['field'][$_field])) {
				// On normalise la description du champ en supprimant les espaces inutiles
				$description['field'][$_field] = preg_replace('/\s2,/', ' ', $description['field'][$_field]);
				$field_description = explode(' ', $description['field'][$_field]);

				// On compare maintenant avec le format du champ
				if (isset($field_description[0])) {
					$type = strtoupper($field_description[0]);

					// On cherche une instruction DEFAULT
					$default = null;
					if (preg_match($regexp_default, $description['field'][$_field], $matches)) {
						$default = $matches[1];
					}

					// On finalise l'initialisation du champ en fonction de son type
					if ((strpos($type, 'CHAR') !== false) or (strpos($type, 'TEXT') !== false)
						or (strpos($type, 'BLOB') !== false) or (strpos($type, 'BINARY') !== false)
					) {
						$fields[$_field] = ($default != null) ? $default : '';
					} elseif (strpos($type, 'DATE') !== false) {
						$fields[$_field] = ($default != null) ? $default : '0000-00-00 00:00:00';
					} else {
						$fields[$_field] = ($default != null) ? intval($default) : 0;
					}
				} else {
					// On a un problème de configuration: on le trace et on arrête la boucle
					// La table ne sera pas mise à jour.
					$fields = array();
					spip_log("La description du champ <${_field}> de la table <${table}> est mal formée", 'isocode' . _LOG_ERREUR);
					break;
				}
			} else {
				// On a un problème de configuration: on le trace et on arrête la boucle.
				// La table ne sera pas mise à jour.
				$fields = array();
				spip_log("Le champ <${_field}> n'est pas un champ de la table <${table}>", 'isocode' . _LOG_ERREUR);
				break;
			}
		}
	}

	return $fields;
}

/**
 * @param $service
 * @param $table
 * @param $config
 *        Configuration de la méthode de lecture de la source pour la table concernée.
 *
 * @return array
 */
function get_source_content($service, $table, $config) {

	// Initialisation des données de sortie
	$content = array();
	$header = array();
	$sha = false;

	if ($config['populating'] == 'page_text') {
		// Acquisition de la page ciblée par l'url
		include_spip('inc/distant');
		$options = array();
		$flux = recuperer_url($config['url'], $options);
		if (!empty($flux['page']) and ($sha = sha1($flux['page']))) {
			// Chaque élément est identifié soit par un délimiteur, soit par une regexp suivant la méthode configurée.
			if ($config['parsing']['element']['method'] == 'explode') {
				// On récupére donc un tableau des éléments à lire en utilisant la fonction explode
				$content = explode($config['parsing']['element']['delimiter'], $flux['page']);
			} else {
				// TODO : C'est une regexp... à compléter
			}
		}
	} else {
		// La source est un fichier.
		// On construit son nom et on lit son contenu en fonction du type du fichier.
		$file = find_in_path("services/${service}/${table}{$config['extension']}");
		if (file_exists($file) and ($sha = sha1_file($file))) {
			if ($config['populating'] == 'file_csv') {
				$lines = file($file);
				if ($lines) {
					// La première ligne d'un CSV contient toujours les titres des colonnes.
					// On sauvegarde ces titres dans une variable et on élimine la ligne du contenu retourné.
					$header = explode($config['delimiter'], trim(array_shift($lines), "\r\n"));
					$header = array_map('trim', $header);
					// On renvoie le contenu sans titre
					$content = $lines;
				}
			} elseif ($config['populating'] == 'file_xml') {
				include_spip('inc/flock');
				lire_fichier($file, $xml);
				$xml_tree = json_decode(json_encode(simplexml_load_string($xml)), true);

				include_spip('inc/filtres');
				if (table_valeur($xml_tree, $config['base'], '')) {
					$content = table_valeur($xml_tree, $config['base'], '');
				}
			}
		}
	}

	return array($content, $header, $sha);
}


/**
 * @param $element
 * @param $header
 * @param $config
 *
 * @return array
 */
function get_element_values($element, $header, $config) {

	$values = array();

	if ($config['populating'] == 'file_csv') {
		// Chaque valeur de colonne est séparée par le délimiteur configuré.
		$columns = explode($config['delimiter'], trim($element, "\r\n"));
		// On construit un tableau associatif [nom colonne] => valeur colonne
		foreach ($header as $_key => $_header) {
			$values[$_header] = trim($columns[$_key]);
		}
	} elseif ($config['populating'] == 'page_text') {
		// Chaque couple (nom donnée, valeur donnée) est identifiée par une REGEXP configurée
		if (preg_match_all($config['parsing']['field']['regexp'], $element, $matches)) {
			// L'index 1 correspond à la liste des nom de données et l'index 2 à la liste des
			// valeur de données correspondantes.
			// Il faut donc reconstruire un tableau associatif [nom donnée] => valeur donnée
			foreach ($matches[1] as $_key => $_header) {
				$values[trim($_header)] = $matches[2][trim($_key)];
			}
		}
	} else {
		// Fichier XML.
		// Le tableau associatif (nom donnée, valeur donnée) est déjà correctement formé.
		$values = $element;
	}

	return $values;
}
