<?php
/**
 * Vérification de l'utilisation des items de langue definis
 * 
 * @param string $rep
 * @param string $module
 * @param string $langue
 * @param string $ou_langue
 * @param string $ou_fichier
 * @return array
 */

// $rep        => nom du repertoire parent de lang/
// $module     => prefixe du fichier de langue
// $langue     => index du nom de langue
// $ou_lang    => chemin vers le fichier de langue a verifier
// $ou_fichier => racine de l'arborescence a verifier
function inc_langonet_verifier_utilisation($rep, $module, $langue, $ou_langue, $ou_fichier) {

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

	// On construit la liste des items definis mais plus utilises
	$non_utilises = array();
	$a_priori_utilises = array();
	foreach ($GLOBALS[$var_source] as $_item => $_traduction) {
		$utilise = true;
		$avec_certitude = true;
		if (!in_array ($_item, $utilises['items'])) {
			// L'item est soit non utilise, soit utilise dans un contexte variable (ie _T('meteo_'.$statut))
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
				// L'item est vraiment non utilise et c'est une erreur
				$utilise = false;
			}
			else {
				$avec_certitude = false;
			}
		}
		if (!$utilise) {
			$non_utilises[] = $_item;
		}
		if (!$avec_certitude) {
			$a_priori_utilises[] = $_item;
			if (is_array($item_tous[$_valeur])) {
				$item_peut_etre[$_item] = $item_tous[$_valeur];
			}
		}
	}

	// On prepare le tableau des resultats
	$resultats['module'] = $module;
	$resultats['ou_fichier'] = $ou_fichier;
	$resultats['langue'] = $ou_langue.$module.'_'.$langue.'.php';
	$resultats['non_utilises'] = $non_utilises;
	$resultats['a_priori_utilises'] = $a_priori_utilises;
	$resultats['fichier_peut_etre'] = $item_peut_etre;
	$resultats['statut'] = true;

	return $resultats;
}
?>