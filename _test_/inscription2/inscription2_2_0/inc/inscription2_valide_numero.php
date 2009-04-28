<?php

/**
 * 
 * Fonction de validation d'un numéro de téléphone
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $numero Numéro à tester
 */
function inc_inscription2_valide_numero_dist($numero){
	if(!$numero){
		return;
	}
	else{
		if(preg_match('/^[0-9\+\. \-]+$/',$numero)){
			return;
		}
		else{
			return _T('inscription2:numero_valide');
		}
	}
}

?>