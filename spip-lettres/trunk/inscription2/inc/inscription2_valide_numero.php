<?php

/**
 * 
 * Fonction de validation d'un numéro de téléphone
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $numero Numéro à tester
 * @param int $id_auteur[optional]
 */
function inc_inscription2_valide_numero_dist($numero,$id_auteur=NULL,$options=''){
	if(!$numero){
		return;
	}
	else{
		if((!lire_config('inscription2/validation_numero_international') == 'on') && preg_match('/^[0-9\+\. \-]+$/',$numero) && (strlen(str_replace(array(' ','.','+'),'',$numero)) > 6)){
			return;
		}
		else if(lire_config('inscription2/validation_numero_international') == 'on'){
			if(preg_match('/^\+[0-9]{2,3}[0-9\s\.]{6}[0-9\s\.]+$/',$numero)){
				return;
			}else{
				return _T('inscription2:numero_valide_international');
			}
		}
		return _T('inscription2:numero_valide');
	}
}

?>