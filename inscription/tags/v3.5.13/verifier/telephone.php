<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation d'un numéro de téléphone
 *
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $valeur Numéro à tester
 * @param array $options [optional]
 */
function verifier_telephone_dist($valeur,$options=array()){
	if(!$valeur)
		return false;
	else{
		if((!lire_config('inscription3/validation_numero_international') == 'on') && preg_match('/^[0-9\+\. \-]+$/',$valeur) && (strlen(str_replace(array(' ','.','+'),'',$valeur)) > 6))
			return false;
		else if(lire_config('inscription3/validation_numero_international') == 'on'){
			if(preg_match('/^\+[0-9]{2,3}[0-9\s\.]{6}[0-9\s\.]+$/',$valeur))
				return false;
			else
				return _T('inscription3:erreur_numero_valide_international');
		}
		return _T('inscription3:erreur_numero_valide');
	}
}

?>