<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_PATTERN_ETAT_ITEM'))
	define('_LANGONET_PATTERN_ETAT_ITEM', '%\s[\'"]([^\'"]*)[\'"].+[\'"](?:[^\'"]*)[\'"]\s*,?(?:\s*#\s*(NEW|MODIF))?$%Uims');
if (!defined('_LANGONET_PATTERN_TRADLANG'))
	define('_LANGONET_PATTERN_TRADLANG', '// extrait automatiquement de');

/**
 * Creation du tableau des items de langue d'un fichier donne trie par ordre alphabetique
 *
 * @param string $module
 * 		Nom du module de langue
 * @param string $langue
 * 		Code SPIP de la langue
 * @param string $ou_langue
 * 		Chemin vers le fichier de langue à vérifier
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

	$table_brute = $GLOBALS[$var_source];
	if ($table_brute) {
		// Créer le tableau des items NEW et MODIF si le module est sous TradLang
		$matches = array();
		$tradlang = false;
		if ($contenu = spip_file_get_contents($fichier_lang)) {
			// Déterminer préalablement si le module est traduit sous tradlang ou pas.
			// Si non, l'état ne peut être déduit
			if (stripos($contenu, _LANGONET_PATTERN_TRADLANG) !== false) {
				$tradlang = true;
				preg_match_all(_LANGONET_PATTERN_ETAT_ITEM, $contenu, $matches);
			}
		}

		// On range la table des items en y ajoutant l'état
		ksort($table_brute);
		foreach ($table_brute as $_item => $_traduction) {
			$table[$_item]['traduction'] = $_traduction;
			if ($tradlang) {
				$cle = array_search($_item, $matches[1]);
				if ($cle !== false)
					$table[$_item]['etat'] = $matches[2][$cle] ? strtolower($matches[2][$cle]) : 'ok';
				else
					$table[$_item]['etat'] = 'nok';
			}
			else
				$table[$_item]['etat'] = 'nok';
		}

		// On prepare le tableau des resultats
		$resultats['table'] = $table;
		$resultats['tradlang'] = $tradlang;
		$resultats['total'] = count($table_brute);
		$resultats['langue'] = $ou_langue . $module . '_' . $langue . '.php';
	}
	else {
		$resultats['erreur'] = _T('langonet:message_nok_lecture_fichier', array('langue' => $langue, 'module' => $module));
	}

	return $resultats;
}

?>