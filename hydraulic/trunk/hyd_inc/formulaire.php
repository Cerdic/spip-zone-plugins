<?php

/**
 * Vérification de la validité des champs des formulaires du plugin
 * @param $data tableau des données du formulaire
 * @param $ctrl codes des contrôles à appliquer sur les données
 * @author David Dorchies
 * @date 22/11/2015
 */
function hyd_formulaires_verifier($tData,$tCtrl) {

	$erreurs = array();

	foreach($tCtrl as $Cle=>$Ctrl) {
		$tData[$Cle] = trim(str_replace(',','.',$tData[$Cle]));
		if(strpos($Ctrl,'o')!==false & (!isset($tData[$Cle]) | $tData[$Cle]=="")) {
			// Champ obligatoire
			$erreurs[$Cle] = _T('hydraulic:erreur_obligatoire');
		} elseif(!preg_match('#^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$#', $tData[$Cle]) & $tData[$Cle]!="") {
			// Valeurs numériques obligatoires dans tous les cas
			$erreurs[$Cle] = _T('hydraulic:erreur_non_numerique');
		} else {
			// Conversion des champs en valeur réelle
			$tData[$Cle] =  floatval($tData[$Cle]);
			if(strpos($Ctrl,'p')!==false & strpos($Ctrl,'n')!==false & $tData[$Cle] < 0) {
				// Contrôles des valeurs qui doivent être positives ou nulles
				$erreurs[$Cle] = _T('hydraulic:erreur_val_positive_nulle');
			} elseif(strpos($Ctrl,'p')!==false & strpos($Ctrl,'n')===false & $tData[$Cle] <= 0) {
				// Contrôles des valeurs qui doivent être strictement positives
				$erreurs[$Cle] = _T('hydraulic:erreur_val_positive');
			}
		}
	}

	// On compte s'il y a des erreurs. Si oui, alors on affiche un message
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
	}

	return $erreurs;
}

?>