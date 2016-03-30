<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation du statut
 *
 * @param string $valeur 
 * 		Le statut testé
 * @param array $options [optional]
 * 		Le tableau des options
 * @return false|string 
 * 		retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 */
function verifier_statut_dist($valeur,$options=array()) {
	global $liste_des_statuts;

	if(!$valeur)
		return false;
	else{
		if(in_array($valeur,$liste_des_statuts))
			return false;
		else
			return _T('inscription3:erreur_statut_valide');
	}
}

?>