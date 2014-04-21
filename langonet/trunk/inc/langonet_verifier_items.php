<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Items de langue dans les fichiers PHP
// déclaration d'items dans base/module.php
if (!defined('_LANGONET_ITEM_PHP_OBJET'))
	define("_LANGONET_ITEM_PHP_OBJET", '%=>\s*[\'"](?:([a-z0-9_]+):)([^\/ \']*)[\'"]%S');
// Fontions PHP _T ou _U avec apostrophe
if (!defined('_LANGONET_ITEM_PHP_TRADA'))
	define("_LANGONET_ITEM_PHP_TRADA", '%_[TU]\s*[(]\s*\'(?:([a-z0-9_]+):)?([^\']*)\'\s*([^.,)]*[^)]*)%S');
// Fontions PHP _T ou _U avec guillemet
if (!defined('_LANGONET_ITEM_PHP_TRADG'))
	define("_LANGONET_ITEM_PHP_TRADG", '%_[TU]\s*[(]\s*"(?:([a-z0-9_]+):)?([^"]*)"\s*([^.,)]*[^)]*)%S');

// Items de langue dans les fichiers HTML
// balise <:module:raccourci:> et toutes les formes admises avec paramètres et filtres
if (!defined('_LANGONET_ITEM_HTML_BALISE'))
	define("_LANGONET_ITEM_HTML_BALISE", "%<:(?:([a-z0-9_-]+):)?((?:[^:<>|{]+(?:<[^>]*>)?)*)([^:>]*):>%s");
// fonction |singulier_ou_pluriel{arg1, arg2, nb} pour chaque argument
if (!defined('_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1'))
	define("_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1", "%\|singulier_ou_pluriel{(?:[\s]*(?:([a-z0-9_-]+):)?([a-z0-9_]+))[^}]*}%s");
if (!defined('_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2'))
	define("_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2", "%\|singulier_ou_pluriel{[^,]*,(?:[\s]*(?:([a-z0-9_-]+):)?([a-z0-9_]+))[^}]*}%s");
// fonction _T
if (!defined('_LANGONET_ITEM_HTML_FILTRE_T'))
	define("_LANGONET_ITEM_HTML_FILTRE_T", "%#[A-Z_0-9]+{(?:([a-z0-9_-]+):)?([a-z0-9_]+)}((?:\|\w+(?:{[^.]*})?)*)\|_T%Us");

// Items de langue dans les fichiers YAML
if (!defined('_LANGONET_ITEM_YAML'))
	define("_LANGONET_ITEM_YAML", ",<:(?:([a-z0-9_-]+):)?([a-z0-9_]+):>,s");

// Items de langue dans les fichiers XML
// -- pour plugin.xml
if (!defined('_LANGONET_ITEM_PLUGINXML'))
	define("_LANGONET_ITEM_PLUGINXML", ",<titre>\s*(?:([a-z0-9_-]+):)?([a-z0-9_]+)\s*</titre>,is");
// -- pour paquet.xml
if (!defined('_LANGONET_ITEM_PAQUETXML'))
	define("_LANGONET_ITEM_PAQUETXML", ",titre=['\"](?:([a-z0-9_-]+):)?([a-z0-9_]+)['\"],is");
// -- pour les autres fichiers XML
// TODO : comment faire marcher le fait que le tag est le même (contenu) et que les quotes aussi (attribut) ?
// TODO : comment faire aussi pour ne pas capturer ces portions ?
if (!defined('_LANGONET_ITEM_XML_CONTENU'))
	define("_LANGONET_ITEM_XML_CONTENU", ",<\w+>\s*(?:([a-z0-9_-]+):)([a-z0-9_]+)\s*</\w+>,is");
if (!defined('_LANGONET_ITEM_XML_ATTRIBUT'))
	define("_LANGONET_ITEM_XML_ATTRIBUT", ",\w+=['\"](?:([a-z0-9_-]+):)([a-z0-9_]+)['\"],is");

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
function inc_langonet_verifier_items($module, $langue, $ou_langue, $ou_fichiers, $verification) {

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
	exporter_tableau($utilises['debug'], array('expression', 'module', 'raccourci', 'suffixe', 'ligne'));

	// On charge le fichier de langue à vérifier qui doit exister dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_langue = "i18n_" . $module . "_" . $langue;
	$fichier_langue = _DIR_RACINE . $ou_langue . $module . '_' . $langue . '.php';
	if (empty($GLOBALS[$var_langue])) {
		if (file_exists($fichier_langue)) {
			$GLOBALS['idx_lang'] = $var_langue;
			include($fichier_langue);
		}
	}

	// Traitement des occurrences d'erreurs et d'avertissements et constitution de la structure de résultats
	if ($verification == 'definition') {
		// Les chaines definies sont dans les fichiers definis par la RegExp ci-dessous
		// Autrement dit les fichiers francais du repertoire lang/ sont la reference
		$fichiers_langue = preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$');
		$resultats = reperer_items_manquants($module, $utilises, $GLOBALS[$var_langue], $fichiers_langue);
	}
	elseif ($GLOBALS[$var_langue])
		$resultats = reperer_items_inutiles($utilises, $GLOBALS[$var_langue]);

	// Completude de la structure de résultats
	$resultats['module'] = $module;
	$resultats['langue'] = $fichier_langue;
	$resultats['ou_fichier'] = $ou_fichiers;
	// Todo : c'est quoi cette variable ?

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

	$utilises = array('items' => array(), 'suffixes' => array(), 'modules' => array(), 'item_tous' => array());

	foreach ($fichiers as $_fichier) {
		if ($contenu = file($_fichier)) {
			$type_fichier = identifier_type_fichier($_fichier);
			if (isset($GLOBALS['langonet_regexp'][$type_fichier])) {
				$regexps = $GLOBALS['langonet_regexp'][$type_fichier];
				foreach ($contenu as $_no_ligne => $_ligne) {
					foreach ($regexps as $_regexp) {
						if (preg_match_all($_regexp, $_ligne, $occurrences, PREG_SET_ORDER))
							foreach ($occurrences as $_occurrence) {
								memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $_regexp);
							}
					}
				}
			}
			else {
				spip_log("Ce type de fichier n'est pas scanné : $type_fichier ($fichier)", "langonet");
			}
		}
	}

	return $utilises;
}

function collecter_occurrences1($fichiers) {

	$utilises = array('items' => array(), 'suffixes' => array(), 'modules' => array(), 'item_tous' => array());

	foreach ($fichiers as $_fichier) {
		if ($contenu = file($_fichier)) {
			foreach ($contenu as $_no_ligne => $_ligne) {
				$type_fichier = identifier_type_fichier($_fichier);
				if ($type_fichier == 'paquet.xml') {
					$regexp = _LANGONET_ITEM_PAQUETXML;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				elseif ($type_fichier == 'plugin.xml') {
					$regexp = _LANGONET_ITEM_PLUGINXML;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				elseif ($type_fichier == 'xml') {
					$regexp = _LANGONET_ITEM_XML_CONTENU;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_XML_ATTRIBUT;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				elseif ($type_fichier == 'yaml') {
					$regexp = _LANGONET_ITEM_YAML;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				elseif ($type_fichier == 'html') {
					$regexp = _LANGONET_ITEM_HTML_BALISE;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_HTML_FILTRE_PLURIEL_1;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_HTML_FILTRE_PLURIEL_2;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_HTML_FILTRE_T;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				elseif ($type_fichier == 'php') {
					$regexp = _LANGONET_ITEM_PHP_OBJET;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_PHP_TRADA;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
					$regexp = _LANGONET_ITEM_PHP_TRADG;
					if (preg_match_all($regexp, $_ligne, $occurrences, PREG_SET_ORDER))
						foreach ($occurrences as $_occurrence)
							memoriser_occurrence($utilises, $_occurrence, $_fichier, $_no_ligne, $_ligne, $regexp);
				}
				else {
					spip_log("Ce type de fichier n'est pas scanné : $type_fichier", "langonet");
				}
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

/**
 * Memorise selon une structure prédéfinie chaque occurrence d'utilisation d'un item.
 * Cette fonction analyse au passage si l'item est dynamique ou pas (_T avec $ ou concatenation).
 *
 * @param array		$utilises
 * 		Tableau des occurrences d'utilisation des items de langues construit à chaque appel
 * 		de la fonction.
 * @param array		$occurrence
 * 		Tableau définissant l'occurrence d'utilisation en cours de mémorisation. Une occurrence
 * 		est composée des index :
 *
 * 		- 0 : le texte de l'expression matchant le pattern
 * 		- 1 : le module de langue (peut être vide)
 * 		- 2 : le raccourci de l'item de langue
 * 		- 3 : suite du texte ???
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
 * @return boolean
 */
function memoriser_occurrence(&$utilises, $occurrence, $fichier, $no_ligne, $ligne, $regexp) {
	include_spip('inc/langonet_utils');

	if (!isset($occurrence[3]))
		$occurrence[3] = '';
	list($expression, $module, $raccourci_regexp, $suite) = $occurrence;
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
		OR in_array($regexp, $GLOBALS['langonet_regexp']['php']))) {
			// Cas HTML #VAL{xxx}|yyy|_T dynamique ou PHP _T("xxx:$yyy")
			$raccourci_partiellement_variable = true;
	}

	list($item, ) = extraire_arguments($raccourci_regexp);
	list($raccourci_unique, ) = calculer_raccourci_unique($raccourci_regexp, $utilises['items']);
	// TODO : si un raccourci est identique dans deux modules différents on va écraser l'index existant

	$occurrence[] = $ligne;

	$utilises['items'][$raccourci_unique] = $item;
	$utilises['modules'][$raccourci_unique] = $module;
	$utilises['item_tous'][$raccourci_unique][$fichier][$no_ligne][] = $occurrence;
	$utilises['suffixes'][$raccourci_unique] = $raccourci_partiellement_variable;
	$utilises['variables'][$raccourci_unique] = $raccourci_totalement_variable;
	$utilises['debug'][] = $occurrence;

	return true;
}

function memoriser_occurrence2(&$utilises, $occurrence, $fichier, $no_ligne, $ligne, $eval=false) {
	include_spip('inc/langonet_utils');

	if (!isset($occurrence[3]))
		$occurrence[3] = '';
	list($expression, $module, $raccourci_regexp, $suite) = $occurrence;

	if (($expression[0] == '<') AND ($suite[0] == '{') AND ($suite[1] == '=')) {
		// $raccourci_regexp approximatif, mais pas grave: c'est pour le msg
		$raccourci_regexp .= ' . ' . substr($suite,3);
		$eval = true;
	}
	else
		$eval = (($suite AND ($suite[0]==='.')) OR ($eval AND strpos($raccourci_regexp, '$')));
	if (!$raccourci_regexp) return; // TODO : c'est quoi ça ?

	list($item, $args) = extraire_arguments($raccourci_regexp);
	list($raccourci_argumente, $raccourci_brut) = calculer_raccourci_unique($raccourci_regexp, $utilises['items']);
	$raccourci_argumente .= $args;

	$occurrence[] = $ligne;

	$utilises['items'][$raccourci_argumente] = $item;
	$utilises['modules'][$raccourci_argumente] = $module;
	$utilises['item_tous'][$raccourci_argumente][$fichier][$no_ligne][] = $occurrence;
	$utilises['suffixes'][$raccourci_argumente] = $eval;
	$utilises['debug'][] = $occurrence;

}


///  gerer les args
/// La RegExp utilisee ci-dessous est defini dans phraser_html ainsi:
/// define('NOM_DE_BOUCLE', "[0-9]+|[-_][-_.a-zA-Z0-9]*");
/// define('NOM_DE_CHAMP', "#((" . NOM_DE_BOUCLE . "):)?(([A-F]*[G-Z_][A-Z_0-9]*)|[A-Z_]+)(\*{0,2})");

function extraire_arguments($occ) {
	include_spip('public/phraser_html');
	$args = '';
	if (preg_match_all('/' . NOM_DE_CHAMP . '/S', $occ, $m, PREG_SET_ORDER)) {
		foreach($m as $match) {
		  $nom = strtolower($match[4]);
		  $occ = str_replace($match[0], "@$nom@", $occ);
		  $args[]= "$nom=" . $match[0];
		}
		$args = '{' . join(',',$args) . '}';
	}

	return array($occ, $args);
}

//  Construire la liste des items definis mais apparament pas utilises

function reperer_items_inutiles($utilises, $items) {
	$item_non = $item_peut_etre = $fichier_peut_etre = array();
	$index_variable = '';
	foreach ($items as $_raccourci => $_traduction) {
		// TODO : c'est bizarre que l'on ne teste pas l'égalité des modules
		if (!in_array ($_raccourci, $utilises['items'])) {
			// L'item est soit non utilise, soit utilise dans un contexte variable
			$contexte_variable = false;
			foreach($utilises['items'] as $_cle => $_valeur) {
				if ($utilises['suffixes'][$_cle]) {
					if (substr($_raccourci, 0, strlen($_valeur)) == $_valeur) {
						$contexte_variable = true;
						$index_variable = $_cle;
						break;
					}
				}
			}
			if (!$contexte_variable) {
				// L'item est vraiment non utilise
				$item_non[$_raccourci] = $_raccourci;
			} else {
				// L'item est utilise dans un contexte variable
				$item_peut_etre[$_raccourci] = $utilises['item_tous'][$index_variable];
			}
		}
	}
	return array(
		   'occurrences_non' => $item_non,
		   'occurrences_peut_etre' => $item_peut_etre,
	       );
}

// On construit la liste de tous les items definis dans les fichiers de langues fournis
// Ensuite on construit la liste des items utilises mais non definis

function reperer_items_manquants($module, $utilises, $items=array(), $fichiers_langue=array()) {

	$tous_lang = array();
	foreach ($fichiers_langue as $_fichier) {
		$module_def = preg_match(',/lang/([^/]+)_fr\.php$,i', $_fichier, $m) ? $m[1] : '';
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (preg_match_all("#^[\s\t]*['\"]([a-z0-9_]+)['\"][\s\t]*=>(.*)$#i", $texte, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $m) {
					$index = $m[1];
					$m[0] = $_fichier;
					$m[1] = $module_def;
					$tous_lang[$index][] = $m;
				}
			}
		}
	}

	$item_non_mais = $item_non_mais_nok = $item_non = $definition_non_mais_nok = $item_md5 = $fichier_non = array();
	foreach ($utilises['items'] as $_cle => $_valeur) {
		if (!isset($items[$_valeur])) {
			if (!$utilises['suffixes'][$_cle]) {
				$mod = $utilises['modules'][$_cle];
				if ($mod == $module) {
					// Item indefini alors que le module est explicite, c'est une erreur
					$item_non[] = $_valeur;
					$fichier_non[$_cle] = $utilises['item_tous'][$_cle];
				} else {
					// L'item peut etre defini dans un autre module. Le fait qu'il ne soit pas
					// defini dans le fichier en cours de verification n'est pas forcement une erreur.
					// On l'identifie donc a part
					$ok = false;
					if (array_key_exists($_valeur, $tous_lang)) {
						foreach ($tous_lang[$_valeur] as $m) {
						  if (!$m[1]) continue;
						  if ($ok = ($mod ? ($m[1] == $mod) : (($m[1]=='spip') OR ($m[1]=='ecrire') OR ($m[1]=='public')))) {
									break;
							}
						}
					}
					if ($ok) {
						$definition_non_mais[$_valeur] = array_map('array_shift', $tous_lang[$_valeur]);
						$item_non_mais[] = $_valeur;
						$fichier_non_mais[$_cle] = $utilises['item_tous'][$_cle];
					} else {
						$tous = $utilises['item_tous'][$_cle];
						// Si pas normalise, c'est une auto-definition
						// Si l'index est deja pris pour un autre texte
						// (32 caracteres initiaux communs)
						// forcer un suffixe md5
						if (!preg_match(',^\w+$,', $_valeur)) {
							if (isset($tous_lang[$_cle]) 
							AND !preg_match("%^\s*'$_valeur',?\s*$%", $tous_lang[$_cle][0][2])) {
								$_cle .= '_' . md5($_valeur);
							}
							$item_md5[$_cle] = $_valeur;
						}
						$fichier_non_mais_nok[$_cle] = $tous;
						$item_non_mais_nok[] = $_cle;
					}
				}
			}
			else {
				// L'item est defini dynamiquement (i.e. a l'execution),
				// il ne peut etre trouve dans un fichier de langue.
				// On regarde s'il existe des items ressemblants.
				$item_trouve = false;
				foreach($items as $_item => $_traduction) {
					if (substr($_item, 0, strlen($_valeur)) == $_valeur) {
						$item_trouve = true;
						$item_peut_etre[] = $_valeur;
						$fichier_peut_etre[$_item] = is_array($utilises['item_tous'][$_cle]) ? $utilises['item_tous'][$_cle] : array();
					}
				}
				// Si on a pas trouve d'item pouvant correspondre c'est peut-etre que
				// cet item est en fait une variable ou une expression.
				// On ajoute ces cas aussi aux incertains (pas essentiel)
				if (!$item_trouve) {
					$_item = ltrim($_valeur, '\'".\\');
					$item_peut_etre[] = $_item;
					$fichier_peut_etre[$_item] = is_array($utilises['item_tous'][$_cle]) ? $utilises['item_tous'][$_cle] : array();
				}
			}
		}
	}

	return array(
		   'item_non' => $item_non,
		   'item_non_mais' => $item_non_mais,
		   'item_non_mais_nok' => $item_non_mais_nok,
		   'fichier_non' => $fichier_non,
		   'fichier_non_mais' => $fichier_non_mais,
		   'fichier_non_mais_nok' => $fichier_non_mais_nok,
		   'definition_non_mais' => $definition_non_mais,
		   'definition_non_mais_nok' => $definition_non_mais_nok,
		   'item_peut_etre' => $item_peut_etre,
		   'fichier_peut_etre' => $fichier_peut_etre,
		   'item_md5' => $item_md5,
	       );
}

?>