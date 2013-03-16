<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Creation du tableau des items de langue d'un fichier donne trie par ordre alphabetique
 *
 * @param string $module prefixe du fichier de langue
 * @param string $langue index du nom de langue
 * @param string $ou_langue chemin vers le fichier de langue a verifier
 * @return array
 */
function inc_langonet_lister_items($module, $langue, $ou_langue) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On charge le fichier de langue a lister
	// si il existe dans l'arborescence $ou_langue
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source])) {
		$GLOBALS['idx_lang'] = $var_source;
		include(_DIR_RACINE.$ou_langue.$module.'_'.$langue.'.php');
	}

	// On range la table des items en n tables, une par initiale
	$table_brute = $GLOBALS[$var_source];
	ksort($table_brute);
	$initiale = '';
	foreach ($table_brute as $_item => $_traduction) {
		if ($initiale != $_item[0]) {
			// Nouvelle initiale
			$initiale = $_item[0];
		}
		$table[$initiale][$_item] = $_traduction;
	}
	
	// On prepare le tableau des resultats
	$resultats['table'] = $table;
	$resultats['total'] = count($table_brute);
	$resultats['langue'] = $ou_langue . $module . '_' . $langue . '.php';

	return $resultats;
}

?>