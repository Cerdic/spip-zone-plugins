<?php
/**
 * Vérification de la bonne definition des items de langue utilises par le module
 * 
 * @param string $module
 * @param string $langue
 * @param string $ou_langue
 * @param string $ou_fichiers
 * @return 
 */
function inc_langonet_verifier_definition($module, $langue, $ou_langue, $ou_fichiers) {

	$resultats = array();

	// On charge le fichier de langue a verifier si il existe dans l'arborescence $ou_langue 
	// (evite le mecanisme standard de surcharge SPIP)
	include_spip('inc/traduire');
	$var_source = "i18n_".$module."_".$langue;
	if (empty($GLOBALS[$var_source]))
		if (find_in_path($module.'_'.$langue.'.php', $ou_langue))
			charger_langue($langue, $module);
		else {
			$resultats['statut'] = false;
			$resultats['erreur'] = _T('langonet:message_nok_fichier_langue', 
									array('langue' => $langue, 'module' => $module, 'dossier' => $ou_langue));
			return $resultats;
		}

	// On cherche l'ensemble des items utilises dans l'arborescence $ou_fichiers
	$utilises_brut = array('items' => array(), 'suffixes' => array());
// 	$regexp = ",(<:$module:|_T\('$module:)(\w*)('\s*\.\s*\\$*\w*)*,im";
//	$regexp = ",(<\w+>$module:|<:$module:|_T\('$module:)(\w*)('\s*\.\s*\\$*\w*)*,im";
	$regexp = ",(=\"$module:|='$module:|<\w+>$module:|<:$module:|_T\('$module:|_U\('$module:)(\w*)('\s*\.\s*\\$*\w*)*,im";
	foreach (preg_files(_DIR_RACINE.$ou_fichiers,'\.(html|php|xml)$') as $_fichier) {
		lire_fichier($_fichier, $contenu);
		if (preg_match_all($regexp, $contenu, $matches))
			$utilises_brut['items'] = array_merge($utilises_brut['items'], $matches[2]);
			$utilises_brut['suffixes'] = array_merge($utilises_brut['suffixes'], $matches[3]);
	}
	// On rafine le tableau resultant en virant les doublons
	$utilises = array('items' => array(), 'suffixes' => array());
	foreach ($utilises_brut['items'] as $_cle => $_valeur) {
		if (!in_array($_valeur, $utilises['items'])) {
			$utilises['items'][] = $_valeur;
			$utilises['suffixes'][] = (!$utilises_brut['suffixes'][$_cle]) ? false : true;
		}
	}
	// On construit la liste des items utilises mais non definis
	$non_definis = array();
	$a_priori_definis = array();
	foreach ($utilises['items'] as $_cle => $_valeur) {
		$defini = true;
		$avec_certitude = true;
		if (!$GLOBALS[$var_source][$_valeur]) {
			if (!$utilises['suffixes'][$_cle]) {
				// L'item est vraiment non defini et c'est une erreur
				$defini = false;
			}
			else {
				// L'item trouve est utilise dans un contexte variable (ie _T('meteo_'.$statut))
				// il ne peut etre trouve directement dans le fichier de langue
				// Donc on verifie que des items de ce "type" existe dans le fichier de langue
				$defini = false;
				foreach($GLOBALS[$var_source] as $_item => $_traduction) {
					if (substr($_item, 0, strlen($_valeur)) == $_valeur) {
						$defini = true;
						$avec_certitude = false;
					}
				}
			}
		}
		if (!$defini) {
			$non_definis[] = $_valeur;
		}
		if (!$avec_certitude) {
				$a_priori_definis[] = $_valeur;
		}
	}

	// On prepare le tableau des resultats
	$resultats['non_definis'] = $non_definis;
	$resultats['a_priori_definis'] = $a_priori_definis;
	$resultats['statut'] = true;
	
	return $resultats;
}
?>
