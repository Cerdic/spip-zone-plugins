<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_PATTERN_ETAT_ITEM'))
	define('_LANGONET_PATTERN_ETAT_ITEM', '%\s[\'"]([^\'"]*)[\'"].+[\'"](?:[^\'"]*)[\'"]\s*,?(?:\s*#\s*(NEW|MODIF))?$%Uims');
if (!defined('_LANGONET_PATTERN_REFERENCE'))
	define('_LANGONET_PATTERN_REFERENCE', '#<traduction[^>]*reference="(.*)">#Uims');

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

	$liste_brute = $GLOBALS[$var_source];
	if ($liste_brute) {
		// Recherche du gestionnaire de traduction TradLang par l'existence du rapport XML.
		// Si le module est traduit avec ce gestionnaire, on peut identifier les états de traduction
		// de chaque item. On peut aussi identifier la langue de référence
		$rapport_xml = _DIR_RACINE . $ou_langue . $module . '.xml';
		$tradlang = false;
		$langue_reference = false;
		if (file_exists($rapport_xml)) {
			$tradlang = true;
			if ($contenu = spip_file_get_contents($rapport_xml))
				if (preg_match(_LANGONET_PATTERN_REFERENCE, $contenu, $matches))
					$langue_reference = ($matches[1] == $langue);
		}

		// Créer le tableau des items NEW et MODIF si le module est sous TradLang
		$matches = array();
		if ($tradlang AND !$langue_reference) {
			if ($contenu = spip_file_get_contents($fichier_lang)) {
				// la langue de référence ne possède pas les tags NEW ou MODIF
				preg_match_all(_LANGONET_PATTERN_ETAT_ITEM, $contenu, $matches);
			}
		}

		// On range la table des items en y ajoutant l'état
		ksort($liste_brute);
		$liste = array();
		foreach ($liste_brute as $_item => $_traduction) {
			$liste[$_item]['traduction'] = $_traduction;
			if ($tradlang) {
				if ($langue_reference)
					$liste[$_item]['etat'] = 'ok';
				else {
					$cle = array_search($_item, $matches[1]);
					if ($cle !== false)
						$liste[$_item]['etat'] = $matches[2][$cle] ? strtolower($matches[2][$cle]) : 'ok';
					else
						$liste[$_item]['etat'] = 'nok';
				}
			}
			else
				$liste[$_item]['etat'] = 'nok';
		}

		// On prepare le tableau des resultats
		$resultats['items'] = $liste;
		$resultats['total'] = count($liste_brute);
		$resultats['tradlang'] = $tradlang;
		$resultats['reference'] = $langue_reference;
		$resultats['langue'] = $ou_langue . $module . '_' . $langue . '.php';
	}
	else {
		$resultats['erreur'] = _T('langonet:message_nok_lecture_fichier', array('langue' => $langue, 'module' => $module));
	}

	return $resultats;
}

?>