<?php

/**
 * 
 * Fonction de validation d'un numéro de téléphone
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 * @param string $numero Numéro à tester
 * @param int $id_auteur[optional]
 */
function inc_inscription2_valide_numero_dist($numero,$id_auteur=NULL){
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