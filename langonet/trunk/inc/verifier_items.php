<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Items de langue dans les fichiers PHP
// déclaration d'items dans base/module.php
if (!defined('_LANGONET_ITEM_PHP_OBJET'))
	define("_LANGONET_ITEM_PHP_OBJET", '%=>\s*[\'"](?:([a-z0-9_]+):)([^\/ \']*)[\'"]%Sm');
// Fontions PHP _T ou _U avec apostrophe
if (!defined('_LANGONET_ITEM_PHP_TRADA'))
	define("_LANGONET_ITEM_PHP_TRADA", '%_[TU]\s*[(]\s*\'(?:([a-z0-9_]+):)?([^\']*)\'\s*([^.,)]*[^)]*)%Sm');
// Fontions PHP _T ou _U avec guillemet
if (!defined('_LANGONET_ITEM_PHP_TRADG'))
	define("_LANGONET_ITEM_PHP_TRADG", '%_[TU]\s*[(]\s*"(?:([a-z0-9_]+):)?([^"]*)"\s*([^.,)]*[^)]*)%Sm');

// Items de langue dans les fichiers HTML

// balise <:module:raccourci:> et toutes les formes admises avec paramètres et filtres
if (!defined('_LANGONET_ITEM_HTML_BALISE'))
	define("_LANGONET_ITEM_HTML_BALISE", "%<:(?:([a-z0-9_-]+):)?((?:[^:<>|{]+(?:<[^>]*>)?)*)([^:>]*):>%sm");
// Fonction |singulier_ou_pluriel{arg1, arg2, nb} pour chaque argument. Le nb est indispensable pour la détection de
// l'arg2
if (!defined('_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1'))
	define("_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1", "%\|singulier_ou_pluriel{(?:[\s]*(?:(?:#[A-Z_0-9]+{)*)(?:([a-z0-9_-]+):)?([a-z0-9_]+))([^,]*),%sm");
if (!defined('_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2'))
	define("_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2", "%\|singulier_ou_pluriel{[^,]*,(?:[\s]*(?:(?:#[A-Z_0-9]+{)*)(?:([a-z0-9_-]+):)?([a-z0-9_]+))([^,]*),%sm");
// Fonction _T
if (!defined('_LANGONET_ITEM_HTML_FILTRE_T'))
	define("_LANGONET_ITEM_HTML_FILTRE_T", "%#[A-Z_0-9]+{(?:([a-z0-9_-]+):)?([a-z0-9_]+)}((?:\|\w+(?:{[^.]*})?)*)\|_T%Usm");

// Items de langue dans les fichiers YAML
if (!defined('_LANGONET_ITEM_YAML'))
	define("_LANGONET_ITEM_YAML", ",<:(?:([a-z0-9_-]+):)?([a-z0-9_]+):>,sm");

// Items de langue dans les fichiers XML
// -- pour plugin.xml
if (!defined('_LANGONET_ITEM_PLUGINXML'))
	define("_LANGONET_ITEM_PLUGINXML", ",<titre>\s*(?:([a-z0-9_-]+):)?([a-z0-9_]+)\s*</titre>,ism");
// -- pour paquet.xml
if (!defined('_LANGONET_ITEM_PAQUETXML'))
	define("_LANGONET_ITEM_PAQUETXML", ",titre=['\"](?:([a-z0-9_-]+):)?([a-z0-9_]+)['\"],ism");
// -- pour les autres fichiers XML
// TODO : comment faire marcher le fait que le tag est le même (contenu) et que les quotes aussi (attribut)
// TODO : comment faire aussi pour ne pas capturer ces portions
if (!defined('_LANGONET_ITEM_XML_CONTENU'))
	define("_LANGONET_ITEM_XML_CONTENU", ",<\w+>\s*(?:<:)*(?:([a-z0-9_-]+):)([a-z0-9_]+)(?::>)*\s*</\w+>,ism");
if (!defined('_LANGONET_ITEM_XML_ATTRIBUT'))
	define("_LANGONET_ITEM_XML_ATTRIBUT", ",\w+=['\"](?:([a-z0-9_-]+):)([a-z0-9_]+)['\"],ism");

$GLOBALS['langonet_regexp'] = array(
	'paquet.xml' => array(_LANGONET_ITEM_PAQUETXML),
	'plugin.xml' => array(_LANGONET_ITEM_PLUGINXML),
	'xml' => array(
				_LANGONET_ITEM_XML_CONTENU,
				_LANGONET_ITEM_XML_ATTRIBUT
	),
	'yaml' => array(_LANGONET_ITEM_YAML),
	'html' => array(
				_LANGONET_ITEM_HTML_BALISE,
				_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1,
				_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2,
				_LANGONET_ITEM_HTML_FILTRE_T
	),
	'php' => array(
				_LANGONET_ITEM_PHP_OBJET,
				_LANGONET_ITEM_PHP_TRADA,
				_LANGONET_ITEM_PHP_TRADG
	)
);

/**
 * Verification des items de langue non définis ou obsolètes
 *
 * @param string 	$module		prefixe du fichier de langue
 * @param string 	$langue		index du nom de langue
 * @param string 	$ou_langue		chemin vers le fichier de langue à vérifier
 * @param array		$ou_fichiers	tableau des racines d'arborescence à vérifier
 * @param string 	$verification	type de verification à effectuer
 * @return array
 */
function inc_verifier_items($module, $langue, $ou_langue, $ou_fichiers, $verification) {

	// On constitue la liste des fichiers pouvant être susceptibles de contenir des items de langue.
	// Pour cela on boucle sur chacune des arborescences choisies.
	// - les ultimes sous-repertoires charsets/ , lang/ , req/ sont ignorés.
	// - seuls les fichiers php, html, xml ou yaml sont considérés.
	$fichiers = array();
	foreach($ou_fichiers as $_arborescence) {
		$fichiers = array_merge(
						$fichiers,
						preg_files(_DIR_RACINE.$_arborescence, '(?<!/charsets|/lang|/req)(/[^/]*\.(xml|yaml|html|php))$'));
	}

	// On collecte l'ensemble des occurrences d'utilisation d'items de langue dans la liste des fichiers
	// précédemment constituée.
	$utilises = collecter_occurrences($fichiers);

	// On sauvegarde l'index de langue global si il existe car on va le modifier pendant le traitement.
	$idx_lang_backup = '';
	if (isset($GLOBALS['idx_lang'])) {
		$idx_lang_backup = $GLOBALS['idx_lang'];
	}

	// On charge le fichier de langue à vérifier qui doit exister dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	$idx_lang = "i18n_" . $module . "_" . $langue;
	$fichier_langue = _DIR_RACINE . $ou_langue . $module . '_' . $langue . '.php';

	$backup_trad = array();
	// Si les traductions correspondant à l'index de langue sont déjà chargées on les sauvegarde pour
	// les restaurer en fin de traitement. En effet, si l'index en cours de traitement est
	// déjà chargé, on ne peut pas présumer du fichier de langue source car il est possible d'avoir un même
	// module dans plusieurs plugins.
	if (!empty($GLOBALS[$idx_lang])) {
		$backup_trad = $GLOBALS[$idx_lang];
		unset($GLOBALS[$idx_lang]);
	}

	// On charge le fichier de langue si il existe dans l'arborescence $ou_langue
	// (le fichier source existe toujours, le cible peut être absent)
	// Ensuite on le stocke dans un tableau qui sera passé à la fonction de création du fichier de langue
	if (file_exists($fichier_langue)) {
		$GLOBALS['idx_lang'] = $idx_lang;
		include($fichier_langue);
	}
	$traductions = isset($GLOBALS[$idx_lang]) ? $GLOBALS[$idx_lang] : array();

	// On rétablit le module backupé si besoin
	if (isset($GLOBALS[$idx_lang]))
		unset($GLOBALS[$idx_lang]);
	if ($backup_trad) {
		$GLOBALS[$idx_lang] = $backup_trad;
	}

	// On restaure l'index de langue global si besoin
	if ($idx_lang_backup) {
		$GLOBALS['idx_lang'] = $idx_lang_backup;
	}
	else {
		unset($GLOBALS['idx_lang']);
	}

	// Traitement des occurrences d'erreurs et d'avertissements et constitution de la structure de résultats
	if ($verification == 'definition') {
		// Les chaines definies sont dans les fichiers definis par la RegExp ci-dessous
		// Autrement dit les fichiers francais du repertoire lang/ sont la reference
		$fichiers_langue = preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$');
		$resultats = reperer_items_non_definis($utilises, $module, $traductions, $fichiers_langue);
	}
	elseif ($GLOBALS[$idx_lang]) {
		$resultats = reperer_items_non_utilises($utilises, $module, $traductions);
	}

	// Compléments de la structure de résultats
	$resultats['module'] = $module;
	$resultats['langue'] = $fichier_langue;
	$resultats['ou_fichier'] = $ou_fichiers;

	return $resultats;
}


/**
 * Cherche l'ensemble des occurrences d'utilisation d'items de langue dans la liste des fichiers fournie.
 * Cette recherche se fait ligne par ligne, ce qui ne permet pas de trouver les items sur plusieurs lignes.
 *
 * @param $fichiers
 * @return array
 */
function collecter_occurrences($fichiers) {

	$utilises = array(
					'raccourcis' => array(),
					'modules' => array(),
					'items' => array(),
					'occurrences' => array(),
					'suffixes' => array(),
					'variables' => array(),
					'debug' => array()
	);

	foreach ($fichiers as $_fichier) {
		if ($contenu = file_get_contents($_fichier)) {
			$type_fichier = identifier_type_fichier($_fichier);
			if (isset($GLOBALS['langonet_regexp'][$type_fichier])) {
				$regexps = $GLOBALS['langonet_regexp'][$type_fichier];
				// On stocke aussi le fichier à scanner sous forme d'un tableau de lignes afin de rechercher
				// les numéros de ligne et de colonne des occurrences
				$lignes = file($_fichier);
				foreach ($regexps as $_regexp) {
					if (preg_match_all($_regexp, $contenu, $matches, PREG_OFFSET_CAPTURE)) {
						foreach ($matches[0] as $_cle => $_expression) {
							$occurrence[0] = $_expression[0];
							$occurrence[1] = $matches[1][$_cle][0];
							$occurrence[2] = $matches[2][$_cle][0];
							$occurrence[3] = isset($matches[3]) ? $matches[3][$_cle][0] : '';
							// Recherche de la ligne et de la colonne à partir de l'offset global de début
							// de l'expression
							list($ligne, $no_ligne, $no_colonne) = rechercher_ligne($_expression[1], $lignes);
							$occurrence[4] = $no_colonne;
							$utilises = memoriser_occurrence($utilises, $occurrence, $_fichier, $no_ligne, $ligne, $_regexp);
						}
					}
				}
			}
			else {
				spip_log("Ce type de fichier n'est pas scanné : $type_fichier ($_fichier)", "langonet");
			}
		}
	}

	return $utilises;
}


/**
 * Identifie le type de fichier dans lequel chercher les occurrences d'utilisation d'items
 * de langue.
 *
 * @param string	$fichier
 * 		Chemin complet du fichier à scanner
 *
 * @return string
 * 		Extension du fichier parmi 'xml', 'yaml', 'html' et 'php' ou le nom du fichier de description
 * 		du plugin 'paquet.xml' ou 'plugin.xml'.
 */
function identifier_type_fichier($fichier) {
	// On initialise le type avec l'extension du fichier
	$informations = pathinfo($fichier);
	$type = strtolower($informations['extension']);

	// Pour les fichiers XML on précise si le fichier est un paquet.xml ou un plugin.xml
	if ($type == 'xml')
		if (($informations['basename'] == 'paquet.xml')
		OR ($informations['basename'] == 'plugin.xml'))
			$type = strtolower($informations['basename']);

	return $type;
}


function rechercher_ligne($offset, $lignes) {

	$no_ligne = $no_colonne = 0;
	$ligne = $lignes[0];

	$somme_ligne = 0;
	foreach ($lignes as $_no_ligne => $_ligne) {
		$longueur_ligne = strlen($_ligne);
		$somme_ligne += $longueur_ligne;
		if ($somme_ligne > $offset) {
			// on a trouvé la ligne
			$ligne = $_ligne;
			$no_ligne = $_no_ligne;
			// il faut déterminer la colonne
			$no_colonne = $longueur_ligne - ($somme_ligne-$offset);
			break;
		}
	}

	return array($ligne, $no_ligne, $no_colonne);
}

/**
 * Memorise selon une structure prédéfinie chaque occurrence d'utilisation d'un item.
 * Cette fonction analyse au passage si l'item est dynamique ou pas (_T avec $ ou concatenation).
 *
 * @param array		$utilisations
 * 		Tableau des occurrences d'utilisation des items de langues construit à chaque appel
 * 		de la fonction.
 * @param array		$occurrence
 * 		Tableau définissant l'occurrence d'utilisation en cours de mémorisation. Une occurrence
 * 		est composée des index :
 *
 * 		- 0 : le texte de l'expression matchant le pattern
 * 		- 1 : le module de langue (peut être vide)
 * 		- 2 : le raccourci de l'item de langue tel que détecté
 * 		- 3 : suite du texte du raccourci (dénote une occurrence partiellement ou totalement variable)
 * 		- 4 : numéro de colonne de l'occurrence
 *
 * @param string 	$fichier
 * 		Fichier dont est issu l'occurrence en cours de mémorisation.
 * @param string 	$no_ligne
 * 		Numéro de ligne à laquelle l'occurrence en cours de mémorisation a été trouvée.
 * @param string	$ligne
 * 		Ligne complète dans laquelle l'occurence en cours de mémorisation a été trouvée.
 * @param string	$regexp
 *      Expression régulière utilisée pour trouver l'occurrence d'utilisation en cours de
 * 		mémorisation.
 *
 * @return array
 * 		Le tableau des occurrences mis à jour avec l'occurrence passée en argument
 */
function memoriser_occurrence($utilisations, $occurrence, $fichier, $no_ligne, $ligne, $regexp) {
	include_spip('inc/outiller');

	list(, $module, $raccourci_regexp, $suite,) = $occurrence;
	$suite = trim($suite);

	$raccourci_partiellement_variable = false;
	$raccourci_totalement_variable = false;

	// Dans le cas du PHP, les expressions peuvent donner un raccourci variable dans $raccouci_regexp
	// ou une suite qui ne correspond qu'au paramètres supplémentaires de _T ou _U.
	// Dans ce cas, il faut nettoyer ces variables
	if (in_array($regexp, $GLOBALS['langonet_regexp']['php'])) {
		// Nettoyage de la variable $suite (à faire en premier)
		$offset_virgule = strpos(trim($suite), ',');
		if ($offset_virgule !== false) {
			$suite = trim(substr($suite, 0, $offset_virgule));
		}
		// Nettoyage de la variable $raccourci_regexp
		if ($raccourci_regexp
		AND preg_match('#^([a-z0-9_]*)(.*)$#im', $raccourci_regexp, $matches)) {
			if (!$matches[1]) {
				$raccourci_totalement_variable = true;
				$raccourci_regexp = $matches[2];
			}
			elseif ($matches[2]) {
				$raccourci_partiellement_variable = true;
				$raccourci_regexp = $matches[1];
				$suite = $matches[2];
			}
		}
	}

	// Rechercher si l'occurrence trouvée est dynamique (existence d'un suffixe ou pas)
	// -- on commence par traiter le cas ou le raccourci est vide car détecté comme une suite
	if ($suite AND !$raccourci_regexp) {
		// Cas de la nouvelle écriture variable du raccourci <:xxx:{=#ENV{yyy}}:> ou d'une variable PHP
		// -- on rétablit le raccourci à partir de la suite qui n'en est pas une.
		$raccourci_regexp = $suite;
		$suite = '';
		$raccourci_totalement_variable = true;
	}
	// -- on continue en détectant les suites qui sont de vrais suffixes d'un raccourci incomplet
	if ($suite
	AND !$raccourci_partiellement_variable
	AND (($regexp == _LANGONET_ITEM_HTML_FILTRE_T)
		OR ($regexp == _LANGONET_ITEM_HTML_FILTRE_PLURIEL_1)
		OR ($regexp == _LANGONET_ITEM_HTML_FILTRE_PLURIEL_2)
		OR in_array($regexp, $GLOBALS['langonet_regexp']['php']))	) {
			// Cas HTML ou PHP dynamique
			$raccourci_partiellement_variable = true;
	}

	if ($raccourci_totalement_variable) {
		// Si le raccourci est totalement variable il l'est a fortiori partiellement
		$raccourci_partiellement_variable = true;
	}

	// TODO : vérifier si avec les traitements précédents extraire_argument est encore nécessaire
	list($raccourci, ) = extraire_arguments($raccourci_regexp);
	list($raccourci_unique, ) = calculer_raccourci_unique($raccourci_regexp, $utilisations['raccourcis']);
	// TODO : si un raccourci est identique dans deux modules différents on va écraser l'index existant

	$occurrence[] = $ligne;

	$item = ($module ? "$module:$raccourci_unique" : $raccourci_unique);
	$utilisations['raccourcis'][$item] = $raccourci;
	$utilisations['modules'][$item] = $module;
	$utilisations['items'][$item] = ($module ? "$module:$raccourci" : $raccourci);
	$utilisations['occurrences'][$item][$fichier][$no_ligne][] = $occurrence;
	$utilisations['suffixes'][$item] = $raccourci_partiellement_variable;
	$utilisations['variables'][$item] = $raccourci_totalement_variable;

	// Construction d'une liste plate pour debug
	$occurrence[] = $no_ligne;
	$utilisations['debug'][] = $occurrence;

	return $utilisations;
}


///  gerer les args
/// La RegExp utilisee ci-dessous est defini dans phraser_html ainsi:
/// define('NOM_DE_BOUCLE', "[0-9]+|[-_][-_.a-zA-Z0-9]*");
/// define('NOM_DE_CHAMP', "#((" . NOM_DE_BOUCLE . "):)?(([A-F]*[G-Z_][A-Z_0-9]*)|[A-Z_]+)(\*{0,2})");

/**
 * @param string	$raccourci_regexp
 *
 * @return array
 */
function extraire_arguments($raccourci_regexp) {
	include_spip('public/phraser_html');
	$arguments = '';
	if (preg_match_all('/' . NOM_DE_CHAMP . '/S', $raccourci_regexp, $matches, PREG_SET_ORDER)) {
		foreach($matches as $_match) {
		  $nom = strtolower($_match[4]);
		  $raccourci_regexp = str_replace($_match[0], "@$nom@", $raccourci_regexp);
		  $arguments[]= "$nom=" . $_match[0];
		}
		$arguments = '{' . join(',',$arguments) . '}';
	}

	return array($raccourci_regexp, $arguments);
}


/**
 * Détection des items de langue obsolètes d'un module.
 * Cette fonction renvoie un tableau composé des items obsolètes et des items potentiellement obsolètes.
 *
 * @param array		$utilisations
 * 		Tableau des occurrences d'utilisation d'items de langue dans le code de l'arborescence choisie.
 * @param string	$module
 * 		Nom du module de langue en cours de vérification.
 * @param array		$items_module
 * 		Liste des items de langues contenus dans le module de langue en cours de vérification. L'index est
 * 		le raccourci, la valeur la traduction brute.
 *
 * @return array
 * 		Tableau des items obsolètes ou potentiellement obsolètes. Ce tableau associatif possède une structure
 * 		à deux index :
 *
 * 		- 'occurrences_non' : liste des items obsolètes;
 * 		- 'occurrences_non_mais' : liste des items a priori obsolètes pour le module vérifié mais utilisés avec un autre module;
 * 		- 'occurrences_peut-etre' : liste des items potentiellement obsolètes (contexte d'utilisation dynamique).
 */
function reperer_items_non_utilises($utilisations, $module, $items_module) {
	$item_non = $item_non_mais = $item_peut_etre = array();

	// On boucle sur la liste des items de langue ($items_module) du module en cours de vérification ($module).
	// On teste chaque item pour trouver une utilisation
	foreach ($items_module as $_raccourci => $_traduction) {
		// Il faut absolument tester l'item complet soit module:raccourci car sinon
		// on pourrait accepter comme ok un raccourci identique utilisé avec un autre module.
		// Pour cela la valeur de chaque index des sous-tableaux $utilisations est l'item complet
		// (module:raccourci).
		$item = "$module:$_raccourci";
		$index_variable = '';
		if (!in_array($item, $utilisations['items'])) {
			// L'item est soit
			// 1- non utilisé avec le module en cours de vérification
			// 2- non utilisé avec le module en cours de vérification mais utilisé avec un autre module
			// 3- utilise dans un contexte variable

			// On cherche si l'item est détectable dans un contexte variable
			foreach($utilisations['raccourcis'] as $_cle => $_valeur) {
				if ($utilisations['suffixes'][$_cle]) {
					if (substr($_raccourci, 0, strlen($_valeur)) == $_valeur) {
						$index_variable = $_cle;
						break;
					}
				}
			}

			if (!$index_variable) {
				if ($items_suspects = array_keys($utilisations['raccourcis'], $_raccourci)) {
					// Cas 2- : l'item est utilise avec un module différent que celui en cours
					// de vérification ce qui peut révéler une erreur.
					// On renvoie les occurrences en cause pour affichage complet en reconstruisant le
					// tableau afin qu'il soit de même format que celui des items peut_etre.
					$occurrences_suspectes = array_intersect_key($utilisations['occurrences'], array_flip($items_suspects));
					foreach ($occurrences_suspectes as $_occurrences) {
						foreach ($_occurrences as $_fichier => $_ligne) {
							foreach ($_ligne as $_no_ligne => $_occurrence) {
								if (!isset($item_non_mais[$_raccourci][$_fichier])) {
									// Première occurrence dans ce fichier
									$item_non_mais[$_raccourci][$_fichier] = $_ligne;
								} elseif (!isset($item_non_mais[$_raccourci][$_fichier][$_no_ligne])) {
									// Cette ligne n'a pas encore d'occurrence
									$item_non_mais[$_raccourci][$_fichier][$_no_ligne] = $_occurrence;
								} else {
									// Cette ligne avait déjà une occurrence
									$item_non_mais[$_raccourci][$_fichier][$_no_ligne] = array_merge($item_non_mais[$_raccourci][$_fichier][$_no_ligne], $_occurrence);
								}
							}
						}
					}
				} else {
					// Cas 1- : on renvoie uniquement la traduction afin de l'afficher dans les résultats.
					$item_non[$_raccourci][] = $_traduction;
				}
			} else {
				// Cas 3- : l'item est utilise dans un contexte variable, on renvoie l'occurrence complète
				$item_peut_etre[$_raccourci] = $utilisations['occurrences'][$index_variable];
			}
		}
	}

	return array(
			'occurrences_non' => $item_non,
			'occurrences_non_mais' => $item_non_mais,
			'occurrences_peut_etre' => $item_peut_etre,
	       );
}


/**
 * Détection des items de langue utilises mais apparamment non definis.
 * Cette fonction renvoie un tableau composé des items manquants et des items potentiellement manquants.
 *
 * @param array		$utilisations
 * 		Tableau des occurrences d'utilisation d'items de langue dans le code de l'arborescence choisie.
 * @param string	$module
 * 		Nom du module de langue en cours de vérification.
 * @param array		$items_module
 * 		Liste des items de langues contenus dans le module de langue en cours de vérification. L'index est
 * 		le raccourci, la valeur la traduction brute.
 * @param array 	$fichiers_langue
 * 		Liste des fichiers de langue 'fr' présent sur site et dans lesquels il est possible de trouver
 * 		certains items de langue.
 *
 * @return array
 */
function reperer_items_non_definis($utilisations, $module, $items_module=array(), $fichiers_langue=array()) {

	// Constitution du tableau de tous les items de langue fr disponibles sur le site et stockage de la liste
	// des modules scannés
	$tous_lang = $modules_tous_lang = array();
	foreach ($fichiers_langue as $_fichier) {
		$module_tous_lang = preg_match(',/lang/([^/]+)_fr\.php$,i', $_fichier, $m) ? $m[1] : '';
		foreach ($contenu = file($_fichier) as $_texte) {
			if (preg_match_all("#^[\s\t]*['\"]([a-z0-9_]+)['\"][\s\t]*=>(.*)$#i", $_texte, $items, PREG_SET_ORDER)) {
				foreach ($items as $_item) {
					// $_item[1] représente le raccourci
					$tous_lang[$_item[1]][] = array(0 => $_fichier, 1 => $module_tous_lang);
				}
			}
		}
		$modules_tous_lang[] = $module_tous_lang;
	}

	$item_non = $item_non_mais = $item_peut_etre = $item_oui_mais = $complement = array();
	foreach ($utilisations['raccourcis'] as $_cle => $_raccourci) {
		$module_utilise = $utilisations['modules'][$_cle];
		// Il faut absolument tester l'item complet soit module:raccourci car sinon
		// on pourrait vérifier un raccourci identique d'un autre module.
		if (!isset($items_module[$_raccourci]) OR ($module_utilise != $module)) {
			$complement[$_raccourci] = array();
			if (!$utilisations['suffixes'][$_cle]) {
				// L'item est explicite, il n'est ni totalement variable ni suffixé par une partie variable
				if ($module_utilise == $module) {
					// Cas 1: item forcément indefini alors que le module est bien celui en cours de vérification
					// => c'est une erreur !
					$item_non[$_raccourci] = $utilisations['occurrences'][$_cle];
				} else {
					// On vérifie si le raccourci appartient au module en cours de vérification.
					$raccourci_dans_module = false;
					if (isset($items_module[$_raccourci])) {
						$raccourci_dans_module = true;
					}

					// On vérifie si le raccourci appartient au module utilisé par l'occurrence en cours.
					$module_utilise_verifiable = false;
					$raccourci_dans_module_utilise = false;
					if (in_array($module_utilise, $modules_tous_lang)) {
						$module_utilise_verifiable = true;
						if (array_key_exists($_raccourci, $tous_lang)) {
							foreach ($tous_lang[$_raccourci] as $_item_tous_lang) {
								// $_item_tous_lang[1] contient toujours le nom du module exact à savoir
								// pour le core spip, public ou ecrire
								if (!$_item_tous_lang[1]) continue;
								$raccourci_dans_module_utilise =
									$module_utilise ?
									($_item_tous_lang[1] == $module_utilise) :
									(($_item_tous_lang[1]=='spip') OR ($_item_tous_lang[1]=='ecrire') OR ($_item_tous_lang[1]=='public'));
								if ($raccourci_dans_module_utilise) {
									break;
								}
							}
						}
					}

					$options = array('module' => $module, 'module_utilise' => $module_utilise);
					if ($raccourci_dans_module) {
						// Cas 2 : le raccourci est dans le module en cours de vérification.
						// On donne la priorité au module en cours de vérification. Si le raccourci fait
						// partie de ce module on considère qu'il est plus probable que l'utilisation qui en
						// est faite soit erronée.
						// Néanmoins, si le raccourci est aussi présent dans le module utilisé par l'occurrence
						// en cours de vérification on le précise car cela diminue la probabilité d'une erreur.
						$item_non_mais[$_raccourci] = $utilisations['occurrences'][$_cle];
						$complement[$_raccourci][0] = _T('langonet:complement_definis_non_mais_cas2', $options);
						$complement[$_raccourci][1] =
							$raccourci_dans_module_utilise ?
							_T('langonet:complement_definis_non_mais_cas2_1', $options) :
							($module_utilise_verifiable ?
								_T('langonet:complement_definis_non_mais_cas2_2', $options) :
								_T('langonet:complement_definis_non_mais_cas2_3', $options));
					} else {
						if ($raccourci_dans_module_utilise) {
							// Cas 3 : le raccourci est bien dans le module utilisé mais pas dans le module en cours
							// de vérification. Il y a de grande chance que ce soit ok mais on le notifie
							$item_oui_mais[$_raccourci] = $utilisations['occurrences'][$_cle];
						} else {
							// Cas 4 : le raccourci n'est ni dans le module en cours de vérification, ni dans le
							// module de l'occurrence de vérification. Il est donc non défini mais on ne sait pas
							// si cela concerne le module en cours ou pas.

							// Si pas normalise, c'est une auto-definition
							// Si l'index est deja pris pour un autre texte
							// (48 caracteres initiaux communs)
							// forcer un suffixe md5
							// TODO : a priori ce code devrait être obsolete
							$md5 = $_raccourci;
							if (!preg_match(',^\w+$,', $_raccourci)) {
								if (isset($tous_lang[$_raccourci])
								AND !preg_match("%^\s*'$_raccourci',?\s*$%", $tous_lang[$_cle][0][2])) {
									$md5 .= '_' . md5($_raccourci);
								}
							}
							$item_non_mais[$_raccourci] = $utilisations['occurrences'][$_cle];
							$complement[$_raccourci][0] = _T('langonet:complement_definis_non_mais_cas4', $options);
							$complement[$_raccourci][1] = $module_utilise_verifiable ? '' : _T('langonet:complement_definis_non_mais_cas4_1', $options);
						}
					}
				}
			} else {
				if ($utilisations['variables'][$_cle]) {
					// Cas 5 : le raccourci est totalement variable, il n'est pas possible de trouver un
					// raccourci rapprochant dans le module en cours de vérification
					$raccourci_variable = ltrim($_raccourci, '\'".\\');
					$item_peut_etre[$raccourci_variable] = $utilisations['occurrences'][$_cle];
					$complement[$raccourci_variable][0] = _T('langonet:complement_definis_peut_etre_cas5');
				} else {
					// Cas 6 : le raccourci est partiellement variable
					// => on cherche un item du module en cours de vérification qui pourrait en approcher
					//    (commence par le raccourci).
					$item_approchant = '';
					foreach($items_module as $_item => $_traduction) {
						if (substr($_item, 0, strlen($_raccourci)) == $_raccourci) {
							$item_approchant = $_item;
						}
					}
					$item_peut_etre[$_raccourci] = $utilisations['occurrences'][$_cle];
					$complement[$_raccourci][0] = _T('langonet:complement_definis_peut_etre_cas6');
					$complement[$_raccourci][1] =
						($item_approchant == '') ?
						_T('langonet:complement_definis_peut_etre_cas6_1', array('module' => $module)) :
						_T('langonet:complement_definis_peut_etre_cas6_2', array('module' => $module, 'item' => $item_approchant));
				}
			}
		}
	}

	return array(
			'occurrences_non' => $item_non,
			'occurrences_non_mais' => $item_non_mais,
			'occurrences_oui_mais' => $item_oui_mais,
			'occurrences_peut_etre' => $item_peut_etre,
			'complements' => $complement,
	       );
}

?>