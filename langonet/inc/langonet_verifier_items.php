<?php
/**
 * Vérification de l'utilisation des items de langue
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

	// On charge le fichier de langue a verifier
	// si il existe dans l'arborescence $ou_langue 
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source])) {
		$GLOBALS['idx_lang'] = $var_source;
		include(_DIR_RACINE.$ou_langue.$module.'_'.$langue.'.php');
	}

	// On cherche l'ensemble des items utilises dans l'arborescence $ou_fichier
	$utilises_brut = array('items' => array(), 'suffixes' => array());
	// On ne scanne pas dans les ultimes sous-repertoires charsets/ ,
	// lang/ , req/ . On ne scanne que les fichiers php, html ou xml
	// (voir le fichier regexp.txt).
	foreach (preg_files(_DIR_RACINE.$ou_fichier, '(?<!/charsets|/lang|/req)(/[^/]*\.(html|php|xml))$') as $_fichier) {
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (strpos($_fichier, '.xml')) {
				$trouver_item = _TROUVER_ITEM_X;
			}
			else {
				$trouver_item = _TROUVER_ITEM_HP;
			}
			if (preg_match_all($trouver_item, $texte, $matches)) {
				$utilises_brut['items'] = array_merge($utilises_brut['items'], $matches[2]);
				$utilises_brut['suffixes'] = array_merge($utilises_brut['suffixes'], $matches[3]);
				foreach ($matches[2] as $item_val) {
					$item_tous[$item_val][$_fichier][$ligne][] = trim($texte);
				}
			}
		}
	}

	// On affine le tableau resultant en supprimant les doublons
	$utilises = array('items' => array(), 'suffixes' => array());
	foreach ($utilises_brut['items'] as $_cle => $_valeur) {
		if (!in_array($_valeur, $utilises['items'])) {
			$utilises['items'][] = $_valeur;
			$utilises['suffixes'][] = (!$utilises_brut['suffixes'][$_cle]) ? false : true;
		}
	}

	if ($verification == 'definition') {
		// On construit la liste des items utilises mais non definis
		foreach ($utilises['items'] as $_cle => $_valeur) {
			if (!$GLOBALS[$var_source][$_valeur]) {
				if (!$utilises['suffixes'][$_cle]) {
					// L'item est vraiment non defini et c'est une erreur
					$item_non[] = $_valeur;
					if (is_array($item_tous[$_valeur])) {
						$fichier_non[$_valeur] = $item_tous[$_valeur];
					}
				}
				else {
					// L'item trouve est utilise dans un contexte variable
					// (par exemple : _T('langonet_'.$variable))
					// Il ne peut etre trouve directement dans le fichier de
					// langue, donc on verifie que des items de ce "type"
					// existent dans le fichier de langue
					foreach($GLOBALS[$var_source] as $_item => $_traduction) {
						if (substr($_item, 0, strlen($_valeur)) == $_valeur) {
							$item_peut_etre[] = $_valeur;
							if (is_array($item_tous[$_valeur])) {
								$fichier_peut_etre[$_item] = $item_tous[$_valeur];
							}
						}
					}
				}
			}
		}
	}
	else {
		// On construit la liste des items definis mais plus utilises
		foreach ($GLOBALS[$var_source] as $_item => $_traduction) {
			if (!in_array ($_item, $utilises['items'])) {
				// L'item est soit non utilise, soit utilise dans un contexte
				// variable (par exemple : _T('langonet_'.$variable))
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

	// On prepare le tableau des resultats
	$resultats['module'] = $module;
	$resultats['ou_fichier'] = $ou_fichier;
	$resultats['langue'] = $ou_langue.$module.'_'.$langue.'.php';
	$resultats['item_non'] = $item_non;
	$resultats['fichier_non'] = $fichier_non;
	$resultats['item_peut_etre'] = $item_peut_etre;
	$resultats['fichier_peut_etre'] = $fichier_peut_etre;
	$resultats['statut'] = true;

	return $resultats;
}

/*
commentaire pour test (ne pas supprimer) :
	_T('langonet:test_item_non_defini') TEST : Cet item de langue est bien utilise dans un fichier du repertoire scanne mais n'est pas defini dans le fichier de langue verifie.
*/
?>