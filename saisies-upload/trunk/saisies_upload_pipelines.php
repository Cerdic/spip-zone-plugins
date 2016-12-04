<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Modifie la vérification des saisies pour tenir compte des particularisme des saisies de type upload
 * 
 * @pipeline saisies_verifier()
 * @param array $flux Tableau du flux du pipeline
 * @return array Retourne le flux possiblement modifié
 */
function saisies_upload_saisies_verifier($flux){
	foreach ($flux['args']['saisies'] as $saisie){ // chercher le type de saisie du présent plugins, pour faire nos propres vérifications
		if (in_array($saisie['saisie'], array('upload'))){//si saisie de type upload, ou apparenté (pour le moment que upload)
			var_dump($saisie);
			$nom = $saisie['options']['nom'];
			
			// supprimer l'éventuelle erreur envoyée par la fonction saisies_veirifer
			if (isset($flux['data'][$nom])){
					unset ($flux['data'][$nom]);
			}
			// et faire notre propre verification
			$fonction_verifier = charger_fonction('verifier_'.$saisie['saisie'],'inc');
			$erreur_saisie = $fonction_verifier($saisie);
			
			// intégrer le résultat dans la liste des erreurs
			if ($erreur_saisie){
				$flux['data'][$nom] = $erreur_saisie;
			}
		}
	}
	return $flux;
}
