<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Les REGEXP de recherche de l'item de langue 
// (voir le fichier regexp.txt pour des exemples)

// -- pour les fichiers .html et .php
// sans detection de _L mais avec variables PHP eventuelles dans l'argument de _T
define("_LANGONET_TROUVER_ITEM_HP", 
	"#" .
	"(?:<:|_[TU]\(['\"])" . // designation (<: pour squelette, T|U pour PHP)
	"(?:([a-z0-9_]+):)?" .  // nom du module eventuel
       "(" . "(?:\\$|[\"\']\s*\.\s*\\$*)?" . // delimiteur ' ou " pour T|U
		"[A-Za-z0-9@_&;,.?!\s()-]+" . // item nu, pas forcement normalise
	       ")" .
	"(" . "(?:{(?:[^\|=>]*=[^\|>]*)})?" . // argument entre accolades
		"(?:(?:\|[^>]*)?)" . // filtre
		"(?:['\"]\s*\.\s*[^\s]+)?" . // delimiteur ' ou " pour T|U
	")" .
	"#iS"
       );

// -- pour les fichiers .xml
define("_LANGONET_TROUVER_ITEM_X", ",<[a-z0-9_]+>[\n|\t|\s]*([a-z0-9_]+):([a-z0-9_]+)[\n|\t|\s]*</[a-z0-9_]+()>,iS");

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

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$item_non = $definition_non_mais_nok = $item_md5 = $fichier_non = $resultats = array();

	// On charge le fichier de langue a verifier
	// si il existe dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	include_spip('inc/langonet_verifier_l');
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source])) {
		$GLOBALS['idx_lang'] = $var_source;
		include(_DIR_RACINE.$ou_langue.$module.'_'.$langue.'.php');
	}

	// On cherche l'ensemble des items utilises dans l'arborescence $ou_fichier
	$utilises = array('items' => array(), 'suffixes' => array(), 'modules' => array());
	// On ignore les ultimes sous-repertoires charsets/ ,
	// lang/ , req/ . On ne scanne que les fichiers php, html ou xml
	// (voir le fichier regexp.txt).
	$files = preg_files(_DIR_RACINE.$ou_fichier, '(?<!/charsets|/lang|/req)(/[^/]*\.(html|php|xml|yaml))$');
	foreach ($files as $_fichier) {
		$re = strpos($_fichier, '.xml') ? _LANGONET_TROUVER_ITEM_X : _LANGONET_TROUVER_ITEM_HP;
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (preg_match_all($re, $texte,  $m, PREG_SET_ORDER)) {
				foreach ($m as $occ) {
					$suffixe = preg_replace(',\s*,', '', $occ[2]);
				// On traite les cas particuliers ou l'item est entierement une expression ou une variable:
					if ((substr($suffixe, 0, 1) == "$") OR (substr($suffixe, 0, 2) == "'.") OR (substr($suffixe, 0, 2) == '".')) {
						$suffixe = str_replace('$', '\$', $suffixe);
					} else $suffixe = $occ[3];
					$index = langonet_index_l($occ[2], $utilises['items']);
					$utilises['items'][$index] = $occ[2];
					$utilises['modules'][$index] = $occ[1];
					$item_tous[$index][$_fichier][$ligne][] = trim($occ[0]);
					// l'item est-il dynamique, hormis tableau d'arguments ou filtre ?
					// (si oui c'est sale et on pourra pas faire grand chose)
					$utilises['suffixes'][$index] = ($suffixe AND (strpos('|{', $suffixe[0] !== false)));
				}
			}
		}
	}

	if ($verification == 'definition') {
		// On construit la liste de tous les items definis
		// dans tous les fichiers de langue presents sur le site.
		// Par economie, on se limite au scan des '/lang/xxxx_fr.php'
		foreach (preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$') as $_fichier) {
			foreach ($contenu = file($_fichier) as $ligne => $texte) {
				if (preg_match_all("#^[\s\t]*['\"]([a-z0-9_]+)['\"][\s\t]*=>#i", $texte, $matches)) {
					foreach ($matches[1] as $cet_item) {
						$tous_lang[$cet_item][] = $_fichier;
					}
				}
			}
		}
		// On construit la liste des items utilises mais non definis
		foreach ($utilises['items'] as $_cle => $_valeur) {

			if (!isset($GLOBALS[$var_source][$_valeur])) {
				if (!$utilises['suffixes'][$_cle]) {
					if ($utilises['modules'][$_cle] == $module) {
						// L'item est vraiment non defini et c'est une erreur
						$item_non[] = $_valeur;
						if (is_array($item_tous[$_cle])) {
							$fichier_non[$_cle] = $item_tous[$_cle];
						}
					}
					else {
						// L'item est a priori defini dans un autre module. Le fait qu'il ne soit pas
						// defini dans le fichier en cours de verification n'est pas forcement une erreur.
						// On l'identifie donc a part
						$definition_ok = false;
						$definitions = array();
						if (array_key_exists($_valeur, $tous_lang)) {
							$definitions = $tous_lang[$_valeur];
							while ((list($_index, $_fichier) = each($definitions)) AND !$definition_ok)  {
								preg_match(',/lang/([^/]+)_fr\.php$,i', $_fichier, $module_trouve);
								if ($module_trouve[1]) {
									if ($module_trouve[1] == $utilises['modules'][$_cle]) {
										$definition_ok = true;
									}
									else {
										$definition_ok = ((($module_trouve[1]=='spip') OR ($module_trouve[1]=='ecrire') OR ($module_trouve[1]=='public')) AND (!$utilises['modules'][$_cle]));
									}
								}
							}
						}
						if ($definition_ok) {
							$item_non_mais[] = $_valeur;
							if (is_array($item_tous[$_cle])) {
								$fichier_non_mais[$_cle] = $item_tous[$_cle];
							}
							if ($definitions)
								$definition_non_mais[$_valeur] = $definitions;
						}
						else {
							$item_non_mais_nok[] = $_cle;
							if (is_array($item_tous[$_cle])) {
								$fichier_non_mais_nok[$_cle] = $item_tous[$_cle];
						// Si pas normalise, c'est une auto-definition 
								if (!preg_match(',^\w+$,', $_valeur)) {
									$item_md5[$_cle] = $_valeur;
								}
							}
							if ($definitions)
								$definition_non_mais_nok[$_cle] = $definitions;
						}
					}
				}
				else {
					// L'item est defini dynamiquement (i.e. a l'execution)
					// Il ne peut etre trouve directement dans le fichier de
					// langue, donc on verifie que des items ressemblant
					// existent dans le fichier de langue
					$item_trouve = false;
					foreach($GLOBALS[$var_source] as $_item => $_traduction) {
						if (substr($_item, 0, strlen($_valeur)) == $_valeur) {
							$item_peut_etre[] = $_valeur;
							if (is_array($item_tous[$_cle])) {
								$fichier_peut_etre[$_item] = $item_tous[$_cle];
							}
							$item_trouve = true;
						}
					}
					// Si on a pas trouve d'item pouvant correspondre c'est peut-etre que
					// cet item est en fait une variable ou une expression.
					// On ajoute ces cas aussi aux incertains (pas essentiel)
					if (!$item_trouve) {
						$_item = ltrim($_valeur, '\'".\\');
						$item_peut_etre[] = $_item;
						if (is_array($item_tous[$_cle])) {
							$fichier_peut_etre[$_item] = $item_tous[$_cle];
						}
					}
				}
			}
		}
	}
	else {
		// On construit la liste des items definis mais plus utilises
		if ($GLOBALS[$var_source]) {
			foreach ($GLOBALS[$var_source] as $_item => $_traduction) {
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
					}
					else {
						// L'item est utilise dans un contexte variable
						$item_peut_etre[] = $_item;
						if (is_array($item_tous[$_valeur])) {
							$fichier_peut_etre[$_item] = $item_tous[$_valeur];
						}
					}
				}
			}
		}
	}

	// On prepare le tableau des resultats
	$resultats['module'] = $module;
	$resultats['ou_fichier'] = $ou_fichier;
	$resultats['langue'] = $ou_langue.$module.'_'.$langue.'.php';
	$resultats['item_non'] = $item_non;
	$resultats['item_non_mais'] = $item_non_mais;
	$resultats['item_non_mais_nok'] = $item_non_mais_nok;
	$resultats['fichier_non'] = $fichier_non;
	$resultats['fichier_non_mais'] = $fichier_non_mais;
	$resultats['fichier_non_mais_nok'] = $fichier_non_mais_nok;
	$resultats['definition_non_mais'] = $definition_non_mais;
	$resultats['definition_non_mais_nok'] = $definition_non_mais_nok;
	$resultats['item_peut_etre'] = $item_peut_etre;
	$resultats['fichier_peut_etre'] = $fichier_peut_etre;
	$resultats['item_md5'] = $item_md5;

	return $resultats;
}

?>