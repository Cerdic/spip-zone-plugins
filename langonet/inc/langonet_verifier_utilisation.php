<?php
/**
 * Vérification de l'utilisation dans le module des items de langue definis
 * 
 * @param object $module
 * @param object $langue
 * @param object $ou_langue
 * @param object $ou_fichiers
 * @return 
 */
function inc_langonet_verifier_utilisation($module, $langue, $ou_langue, $ou_fichiers) {

	// On charge le fichier de langue a verifier si il existe dans l'arborescence $ou_langue 
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source]))
		if (find_in_path($module.'_'.$langue.'.php', $ou_langue)) {
			charger_langue($langue, $module);
			if (!$GLOBALS[$var_source]) {
				$resultats['statut'] = false;
				$resultats['erreur'] = _T('langonet:message_nok_plugin_inactif', 
										array('module' => $module));
				return $resultats;
			}
		}
		else{
			$resultats['statut'] = false;
			$resultats['erreur'] = _T('langonet:message_nok_fichier_langue', 
									array('langue' => $langue, 'module' => $module, 'dossier' => $ou_langue));
			return $resultats;
		}
		
	// On cherche l'ensemble des items utilises dans l'arborescence $ou_fichiers
	$utilises_brut = array('items' => array(), 'suffixes' => array());
//	$regexp = ",(=\"$module:|='$module:|<\w+>$module:|<:$module:|_T\('$module:|_U\('$module:)(\w*)('\s*\.\s*\\$*\w*)*,im";
	foreach (preg_files(_DIR_RACINE.$ou_fichiers,'\.(html|php|xml)$') as $_fichier) {
		lire_fichier($_fichier, $contenu);
		if (preg_match_all(_TROUVER_ITEM, $contenu, $matches)) {
			$utilises_brut['items'] = array_merge($utilises_brut['items'], $matches[2]);
			$utilises_brut['suffixes'] = array_merge($utilises_brut['suffixes'], $matches[3]);
		}
	}
	// On rafine le tableau resultant en virant les doublons
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
		}
	}

	// On prepare le tableau des resultats
	$resultats['non_utilises'] = $non_utilises;
	$resultats['a_priori_utilises'] = $a_priori_utilises;
	$resultats['statut'] = true;
	
	return $resultats;
}
?>