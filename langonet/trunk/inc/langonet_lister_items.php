<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_ETAT_ITEM'))
	define('_LANGONET_ETAT_ITEM', '%\s[\'"]([^\'"]*)[\'"].+[\'"](?:[^\'"]*)[\'"]\s*,?(?:\s*#\s*(NEW|MODIF))?$%Uims');

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
		$fichier_lang = _DIR_RACINE . $ou_langue . $module . '_' . $langue . '.php';
		include($fichier_lang);
	}

	// Determiner le tableau des items NEW et MODIF
	if ($contenu = spip_file_get_contents($fichier_lang)) {
		preg_match_all(_LANGONET_ETAT_ITEM, $contenu, $matches);
	}

	// On range la table des items en y ajoutant l'état
	$table_brute = $GLOBALS[$var_source];
	ksort($table_brute);
	$initiale = '';
	foreach ($table_brute as $_item => $_traduction) {
//		if ($initiale != $_item[0]) {
//			// Nouvelle initiale
//			$initiale = $_item[0];
//		}
//		$table[$initiale][$_item]['traduction'] = $_traduction;
		$table[$_item]['traduction'] = $_traduction;
		$cle = array_search($_item, $matches[1]);
		if ($cle !== false)
			$table[$_item]['etat'] = $matches[2][$cle] ? strtolower($matches[2][$cle]) : 'ok';
		else
			$table[$_item]['etat'] = 'nok';
	}

	// On prepare le tableau des resultats
	$resultats['table'] = $table;
	$resultats['total'] = count($table_brute);
	$resultats['langue'] = $ou_langue . $module . '_' . $langue . '.php';

	return $resultats;
}

?>