<?php
/**
 * Ce fichier contient la fonction générique de lecture d'un fichier ou d'une page HTML source
 * en un tableau d'éléments prêt à être inséré dans une table de la base de données.
 *
 * Dans les fonctions du package, les conventions suivantes sont utilisées:
 * - source          : désigne le fichier ou la page HTML à partir duquel la table des codes ISO est remplie.
 * - contenu         : le contenu de la source sous quelque forme que ce soit.
 * - élément         : un élément du contenu destiné à devenir un enregistrement de la table concernée. Un élément
 *                     est un tableau de couples (nom, valeur).
 * - éléments        : liste des éléments constitutifs du contenu de la source.
 * - enregistrement  : un tableau de couples (champ, valeur) pour un sous-ensemble des champs d'une table.
 * - enregistrements : la liste des enregistrements à insérer dans la table concernée.
 * - titre           : le libellé de la donnée dans la source (par ex, le titre d'une colonne dans un fichier CSV).
 *
 * @package SPIP\ISOCODE\SOURCE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Constitue, à partir, d'un fichier CSV ou XML ou d'une page HTML au format texte, un tableau des éléments
 * prêt à être inséré dans une table de la base de données.
 * La fonction utilise le service et le nom de table pour récupérer la configuration permettant l'analyse
 * du fichier et sa traduction en éléments de la table (délimiteur ou regexp, nom des colonnes...).
 * Il est possible, pour chaque élément ou pour l'ensemble d'appliquer une fonction spécifique à la table
 * qui complète l'élément.
 *
 * @param string $service
 *      Nom du service associé à la lecture de la table.
 * @param string $table
 *      Nom de la table concernée par la lecture de la source.
 *
 * @return array
 *      Tableau à deux éléments:
 *      - index 0 : la liste des éléments à enregistrer dans la table concernée
 *      - index 1 : le sha256 de la source des éléments de la table
 *      - index 2 : indicateur de sha identique
 */
function lire_source($service, $table) {

	// Initialisations
	$enregistrements = array();
	$completer_enregistrement = "${table}_completer_enregistrement";
	$completer_table = "${table}_completer_table";
	$source_identique = true;

	// Inclusion des configurations et des fonctions spécifiques au service qui fournit les données
	// de la table à remplir.
	include_spip("services/${service}/${service}_api");

	// Acquisition de la configuration de lecture pour la table concernée.
	$config = $GLOBALS['isocode'][$service]['tables'][$table];

	// Détermination de la clé primaire de la table.
	$cle_primaire_table = obtenir_cle_primaire($table);

	// Initialisation d'un élément de la table par défaut (uniquement les champs de base).
	// Cela permet de s'assurer que chaque élément du tableau de sortie aura la même structure
	// quelque soit les données lues dans la source.
	$enregistrement_defaut = initialiser_enregistrement($table, $config['basic_fields']);

	// Récupération du contenu du fichier ou de la page HTML source et du sha associé. Pour les fichiers CSV
	// on renvoie aussi la liste des titres des colonnes qui existe toujours.
	list($contenu, $titres, $sha) = extraire_contenu_source($service, $table, $config);
	if ($contenu and $sha and $enregistrement_defaut) {
		// On n'analyse le contenu que si celui-ci a changé (sha différent de celui stocké).
		if (!sha_identique($sha, $table)) {
			$source_identique = false;
			$liste_cles_primaires = array();
			foreach ($contenu as $_contenu) {
				// Pour chaque élément on récupère un tableau associatif [titre colonne] = valeur colonne.
				$element = extraire_element($_contenu, $titres, $config);

				// Création de chaque enregistrement de la table
				$enregistrement = $enregistrement_defaut;
				$cle_primaire_existe = false;
				$cle_primaire = array();
				foreach ($element as $_titre => $_valeur) {
					$titre = trim($_titre);
					// Seuls les champs identifiés dans la configuration sont récupérés dans le fichier
					if (isset($config['basic_fields'][$titre])) {
						// Si la valeur n'est pas vide on l'affecte au champ sinon on laisse la valeur par défaut
						// déjà initialisée.
						if ($_valeur) {
							$enregistrement[$config['basic_fields'][$titre]] = trim($_valeur);
						}
						// Vérifier si le champ en cours fait partie de la clé primaire et élaborer la clé
						// primaire de l'élément en cours
						if (in_array($config['basic_fields'][$titre], $cle_primaire_table)) {
							$cle_primaire[$config['basic_fields'][$titre]] = $_valeur;
							if (count($cle_primaire) == count($cle_primaire_table)) {
								$cle_primaire_existe = true;
							}
						}
					} else {
						spip_log("Le champ <${_titre}> n'existe pas dans la configuration de la table ${table}", 'isocode' . _LOG_INFO);
					}
				}

				// On ajoute l'élément que si la clé primaire a bien été trouvée et si la valeur de cette clé
				// n'est pas en doublon avec un élément déjà enregistré.
				if ($cle_primaire_existe) {
					// On tri la clé primaire et on la transforme en chaine pour la tester et la stocker
					ksort($cle_primaire);
					$cle_primaire = implode(',', $cle_primaire);
					if (!in_array($cle_primaire, $liste_cles_primaires)) {
						// On rajoute cette clé dans la liste
						$liste_cles_primaires[] = $cle_primaire;

						// Si la table possède un nom multilangue, on le calcule à partir des champs du type label_xx
						// où xx est le code de langue utilisé par SPIP (a priori toujours un alpha2).
						if (!empty($config['label_field'])) {
							$label = '';
							foreach ($enregistrement as $_champ => $_valeur) {
								if ($_valeur
								and (substr($_champ, 0, 6) == 'label_')
								and ($langue = str_replace('label_', '', $_champ))) {
									$label .= "[${langue}]${_valeur}";
								}
							}
							if ($label) {
								$enregistrement['label'] = "<multi>${label}</multi>";
							}
						}

						// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter.
						if (function_exists($completer_enregistrement)) {
							$enregistrement = $completer_enregistrement($enregistrement, $config);
						}

						// Ajout de l'enregistrement finalisé dans la liste.
						$enregistrements[] = $enregistrement;
					} else {
						spip_log("L'élément de clé primaire <${cle_primaire}> de la table <${table}> est en doublon", 'isocode' . _LOG_ERREUR);
					}
				} else {
					spip_log("L'élément <" . var_export($_contenu, true) . "> de la table <${table}> n'a pas de clé primaire", 'isocode' . _LOG_ERREUR);
				}
			}

			// Si besoin on appelle une fonction de complétude pour toute la table.
			if (function_exists($completer_table)) {
				$enregistrements = $completer_table($enregistrements, $config);
			}
		}
	}

	return array($enregistrements, $sha, $source_identique);
}


/**
 * @param $table
 *
 * @return array
 */
function obtenir_cle_primaire($table) {

	include_spip('base/objets');
	$cle_primaire = array();

	if ($id_table = id_table_objet($table)) {
		// On stocke la clé sous forme de liste pour les tests d'appartenance.
		$cle_primaire = explode(',', $id_table);
	}

	return $cle_primaire;
}


/**
 * Initialise un élément d'une table donnée avec les valeurs par défaut configurées dans
 * la déclaration de la base ou avec une valeur prédéfinie par type.
 *
 * @param string $table
 *      Nom de la table concernée par la lecture sans le préfixe `spip_`.
 * @param array  $config_champs
 *      Configuration de la correspondance entre le nom de la donnée dans la source
 *      et celui du champ dans la table.
 *
 * @return array
 */
function initialiser_enregistrement($table, $config_champs) {

	$enregistrement = array();

	// Acquisition de la description de la table (champs, clés, jointures) et définition
	// de la regexp permettant d'isoler la valeur par défaut si elle existe.
	$description = sql_showtable("spip_${table}");
	$regexp_default = '/DEFAULT\s+\'(.*)\'/i';

	if (!empty($description['field'])) {
		foreach ($config_champs as $_champ) {
			if (isset($description['field'][$_champ])) {
				// On normalise la description du champ en supprimant les espaces inutiles
				$description['field'][$_champ] = preg_replace('/\s2,/', ' ', $description['field'][$_champ]);
				$description_champ = explode(' ', $description['field'][$_champ]);

				// On compare maintenant avec le format du champ
				if (isset($description_champ[0])) {
					$type = strtoupper($description_champ[0]);

					// On cherche une instruction DEFAULT
					$defaut = null;
					if (preg_match($regexp_default, $description['field'][$_champ], $matches)) {
						$defaut = $matches[1];
					}

					// On finalise l'initialisation du champ en fonction de son type
					if ((strpos($type, 'CHAR') !== false) or (strpos($type, 'TEXT') !== false)
						or (strpos($type, 'BLOB') !== false) or (strpos($type, 'BINARY') !== false)
					) {
						$enregistrement[$_champ] = ($defaut != null) ? $defaut : '';
					} elseif (strpos($type, 'DATE') !== false) {
						$enregistrement[$_champ] = ($defaut != null) ? $defaut : '0000-00-00 00:00:00';
					} else {
						$enregistrement[$_champ] = ($defaut != null) ? intval($defaut) : 0;
					}
				} else {
					// On a un problème de configuration: on le trace et on arrête la boucle
					// La table ne sera pas mise à jour.
					$enregistrement = array();
					spip_log("La description du champ <${_champ}> de la table <${table}> est mal formée", 'isocode' . _LOG_ERREUR);
					break;
				}
			} else {
				// On a un problème de configuration: on le trace et on arrête la boucle.
				// La table ne sera pas mise à jour.
				$enregistrement = array();
				spip_log("Le champ <${_champ}> n'est pas un champ de la table <${table}>", 'isocode' . _LOG_ERREUR);
				break;
			}
		}
	}

	return $enregistrement;
}

/**
 * @param $service
 * @param $table
 * @param $config
 *      Configuration de la méthode de lecture de la source pour la table concernée.
 *
 * @return array
 */
function extraire_contenu_source($service, $table, $config) {

	// Initialisation des données de sortie
	$contenu = array();
	$titres = array();
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
				$contenu = explode($config['parsing']['element']['delimiter'], $flux['page']);
			} else {
				// TODO : c'est une regexp... à compléter
			}
		}
	} else {
		// La source est un fichier.
		// On construit son nom et on lit son contenu en fonction du type du fichier.
		$fichier = find_in_path("services/${service}/${table}{$config['extension']}");
		if (file_exists($fichier) and ($sha = sha1_file($fichier))) {
			if ($config['populating'] == 'file_csv') {
				$lignes = file($fichier);
				if ($lignes) {
					// La première ligne d'un CSV contient toujours les titres des colonnes.
					// On sauvegarde ces titres dans une variable et on élimine la ligne du contenu retourné.
					$titres = explode($config['delimiter'], trim(array_shift($lignes), "\r\n"));
					$titres = array_map('trim', $titres);
					// On renvoie le contenu sans titre
					$contenu = $lignes;
				}
			} elseif ($config['populating'] == 'file_xml') {
				include_spip('inc/flock');
				lire_fichier($fichier, $xml);
				$arbre = json_decode(json_encode(simplexml_load_string($xml)), true);

				include_spip('inc/filtres');
				if (table_valeur($arbre, $config['base'], '')) {
					$contenu = table_valeur($arbre, $config['base'], '');
				}
			} elseif ($config['populating'] == 'file_json') {
				include_spip('inc/flock');
				lire_fichier($fichier, $json);
				$contenu = json_decode($json, true);
			}
		}
	}

	return array($contenu, $titres, $sha);
}


/**
 * @param $contenu
 * @param $titres
 * @param $config
 *
 * @return array
 */
function extraire_element($contenu, $titres, $config) {

	$element = array();

	if ($config['populating'] == 'file_csv') {
		// Chaque valeur de colonne est séparée par le délimiteur configuré.
		$valeurs = explode($config['delimiter'], trim($contenu, "\r\n"));
		// On construit un tableau associatif [nom colonne] => valeur colonne
		foreach ($titres as $_cle => $_titre) {
			$element[$_titre] = isset($valeurs[$_cle]) ? trim($valeurs[$_cle]) : '';
		}
	} elseif ($config['populating'] == 'page_text') {
		// Chaque couple (nom donnée, valeur donnée) est identifiée par une REGEXP configurée
		if (preg_match_all($config['parsing']['field']['regexp'], $contenu, $matches)) {
			// L'index 1 correspond à la liste des titres et l'index 2 à la liste des
			// valeurs correspondantes.
			// Il faut donc reconstruire un tableau associatif [nom] => valeur
			foreach ($matches[1] as $_cle => $_titre) {
				$element[trim($_titre)] = isset($matches[2][trim($_cle)]) ? $matches[2][trim($_cle)] : '';
			}
		}
	} else {
		// Fichier XML.
		// Le tableau associatif (nom donnée, valeur donnée) est déjà correctement formé.
		$element = $contenu;
	}

	return $element;
}


/**
 * Compare le sha passé en argument pour la table concernée avec le sha stocké dans la meta
 * pour cette même table.
 *
 * @api
 *
 * @param string $sha
 *      SHA à comparer à celui de la table.
 * @param string $table
 *      Nom de la table sans préfixe `spip_` dont il faut comparer le sha
 *      stocké dans sa meta de chargement.
 *
 * @return bool
 *      `true` si le sha passé en argument est identique au sha stocké pour la table choisie, `false` sinon.
 */
function sha_identique($sha, $table) {

	$sha_identique = false;

	// On récupère le sha de la table dans les metas si il existe (ie. la table a été chargée)
	include_spip('inc/config');
	$sha_stocke = lire_config("isocode/tables/${table}/sha", false);

	if ($sha_stocke and ($sha == $sha_stocke)) {
		$sha_identique = true;
	}

	return $sha_identique;
}
