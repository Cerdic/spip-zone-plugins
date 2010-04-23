<?php

/**
 * Creation du tableau des items de langue d'un fichier donne trie par ordre alphabetique
 *
 * @param string $module
 * @param string $langue
 * @param string $ou_langue
 * @return array
 */

// $module       => prefixe du fichier de langue
// $langue       => index du nom de langue
// $ou_lang      => chemin vers le fichier de langue a verifier
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

	// On prepare le tableau des resultats
	$table = $GLOBALS[$var_source];
	ksort($table);
	$resultats['table'] = $table;

	return $resultats;
}

?>