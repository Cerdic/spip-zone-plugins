<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Les REGEXP de recherche de l'item de langue 
// (voir le fichier regexp.txt pour des exemples)

// déclaration d'items dans base/module.php
if (!defined('_LANGONET_ITEM_B'))
	define("_LANGONET_ITEM_B", '%=>\s*[\'"](?:([a-z0-9_]+):)([^\/ \']*)[\'"]%S');
// Fontions PHP _T ou _U avec apostrophe
if (!defined('_LANGONET_ITEM_A'))
	define("_LANGONET_ITEM_A", '%_[TU]\s*[(]\s*\'(?:([a-z0-9_]+):)?([^\']*)\'\s*([^.,)]*[^)]*)%S');
// Fontions PHP _T ou _U avec guillemet
if (!defined('_LANGONET_ITEM_G'))
	define("_LANGONET_ITEM_G", '%_[TU]\s*[(]\s*"(?:([a-z0-9_]+):)?([^"]*)"\s*([^.,)]*[^)]*)%S');
// squelette avec <:  :>
if (!defined('_LANGONET_ITEM_H'))
	define("_LANGONET_ITEM_H", "%<[:](?:([a-z0-9_]+):)?((?:[^:<>|{]+(?:<[^>]*>)?)*)([^>]*)%S");
// pour plugin.xml (obsolete a terme)
if (!defined('_LANGONET_ITEM_X'))
	define("_LANGONET_ITEM_X", ",<[a-z0-9_]+>[\n|\t|\s]*([a-z0-9_]+):([a-z0-9_]+)[\n|\t|\s]*</[a-z0-9_]+()>,iS");


/**
 * Verification de l'utilisation des items de langue
 *
 * @param string $rep
 * @param string $module
 * @param string $langue
 * @param string $ou_langue
 * @param string $ou_fichier
 * @param string $verification
 * @return array
 */

// $rep          => nom du repertoire parent de lang/
// $module       => prefixe du fichier de langue
// $langue       => index du nom de langue
// $ou_lang      => chemin vers le fichier de langue a verifier
// $ou_fichier   => racine de l'arborescence a verifier
// $verification => type de verification a effectuer
function inc_langonet_verifier_items($rep, $module, $langue, $ou_langue, $ou_fichier, $verification) {

	// On ignore les ultimes sous-repertoires charsets/ , lang/ , req/ .
	// On n'analyse que les fichiers php, html, xml ou yaml
	// (voir le fichier regexp.txt).
	$files = array();
	foreach($ou_fichier as $rep){
		$files = array_merge($files,preg_files(_DIR_RACINE.$rep, '(?<!/charsets|/lang|/req)(/[^/]*\.(html|php|xml|yaml))$'));
	}
	$resultats =  langonet_collecter_items($files);

	// On charge le fichier de langue a verifier
	// si il existe dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$fichier_langue = $ou_langue.$module.'_'.$langue.'.php';
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source])) {
		$GLOBALS['idx_lang'] = $var_source;
		include(_DIR_RACINE.$fichier_langue);
	}
	if ($verification == 'definition') {
		// Les chaines definies sont dans les fichiers definis par la RegExp ci-dessous
		// Autrement dit les fichiers francais du repertoire lang/ sont la reference
		$files = preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$');
		$resultats = langonet_classer_items($module, $resultats, $GLOBALS[$var_source], $files);
	} elseif ($GLOBALS[$var_source])
		$resultats = langonet_reperer_items($resultats, $GLOBALS[$var_source]);
	$resultats['module'] = $module;
	$resultats['langue'] = $fichier_langue;
	$resultats['ou_fichier'] = $ou_fichier;
	return $resultats;
}

// On cherche l'ensemble des items utilises dans l'arborescence
// ligne par ligne (tant pis pour les items sur plusieurs lignes),
// a l'aide des RegExp definies ci-dessus

function langonet_collecter_items($files) {

	$utilises = array('items' => array(), 'suffixes' => array(), 'modules' => array(), 'tous' => array());
	foreach ($files as $_fichier) {
		$xml = strpos($_fichier, '.xml');
		foreach ($contenu = file($_fichier) as $ligne => $t) {
			if ($xml) {
				if (preg_match_all(_LANGONET_ITEM_X, $t, $m, PREG_SET_ORDER))
					foreach ($m as $occ) langonet_match($utilises, $occ, $_fichier, $ligne);
			} else {
				if (preg_match_all(_LANGONET_ITEM_B, $t, $m, PREG_SET_ORDER))
            		foreach ($m as $occ) langonet_match($utilises, $occ, $_fichier, $ligne);
				if (preg_match_all(_LANGONET_ITEM_A, $t, $m, PREG_SET_ORDER))
					foreach ($m as $occ) langonet_match($utilises, $occ, $_fichier, $ligne);
				if (preg_match_all(_LANGONET_ITEM_G, $t, $m, PREG_SET_ORDER))
					foreach ($m as $occ) langonet_match($utilises, $occ, $_fichier, $ligne, true);
				if (preg_match_all(_LANGONET_ITEM_H, $t, $m, PREG_SET_ORDER))
					foreach ($m as $occ) langonet_match($utilises, $occ, $_fichier, $ligne);
			}
		}
	}
	return $utilises;
}

/// Memorise le resultat d'un preg_match ci-dessus
// et analyse au passage si l'item est dynamique (_T avec $ ou concatenation)

function langonet_match(&$utilises, $occ, $_fichier, $ligne, $eval=false) {
	include_spip('inc/langonet_utils');

	list($tout, $module, $nom, $suite) = $occ;
	if (($tout[0] == '<') AND ($suite[0] == '{') AND ($suite[1] == '=')) {
		// $nom approximatif, mais pas grave: c'est pour le msg
		$nom .= ' . ' . substr($suite,3); 
		$eval = true;
	} else $eval = (($suite AND ($suite[0]==='.')) OR ($eval AND strpos($nom, '$')));
	if (!$nom) return;
	list($item, $args) = langonet_argumenter($nom);
	$index = langonet_index($nom, $utilises['items']) . $args;
	$utilises['items'][$index] = $item;
	$utilises['modules'][$index] = $module;
	$utilises['item_tous'][$index][$_fichier][$ligne][] = $occ;
	$utilises['suffixes'][$index] = $eval;
}

include_spip('public/phraser_html');

///  gerer les args
/// La RegExp utilisee ci-dessous est defini dans phraser_html ainsi:
/// define('NOM_DE_BOUCLE', "[0-9]+|[-_][-_.a-zA-Z0-9]*");
/// define('NOM_DE_CHAMP', "#((" . NOM_DE_BOUCLE . "):)?(([A-F]*[G-Z_][A-Z_0-9]*)|[A-Z_]+)(\*{0,2})");

function langonet_argumenter($occ)
{
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

function langonet_reperer_items($utilises, $init)
{
	$item_non = $item_peut_etre = $fichier_peut_etre = array();
	foreach ($init as $_item => $_traduction) {
		if (!in_array ($_item, $utilises['items'])) {
			// L'item est soit non utilise, soit utilise dans un contexte variable
			$contexte_variable = false;
			foreach($utilises['items'] as $_cle => $_valeur) {
				if ($utilises['suffixes'][$_cle]) {
					if (substr($_item, 0, strlen($_valeur)) == $_valeur) {
						$contexte_variable = true;
						break;
					}
				}
			}
			if (!$contexte_variable) {
				// L'item est vraiment non utilise
				$item_non[] = $_item;
			} else {
				// L'item est utilise dans un contexte variable
				$item_peut_etre[] = $_item;
				$fichier_peut_etre[$_item] = array();
			}
		}
	}
	return array(
		   'item_non' => $item_non,
		   'item_peut_etre' => $item_peut_etre,
		   'fichier_peut_etre' => $fichier_peut_etre,
	       );
}

// On construit la liste de tous les items definis dans les fichiers de langues fournis
// Ensuite on construit la liste des items utilises mais non definis

function langonet_classer_items($module, $utilises, $init=array(), $files=array()) {

	$tous_lang = array();
	foreach ($files as $_fichier) {
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
		if (!isset($init[$_valeur])) {
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
				foreach($init as $_item => $_traduction) {
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