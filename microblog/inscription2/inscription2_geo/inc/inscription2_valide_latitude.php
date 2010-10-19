<?php
/**
 * 
 * Fonction de validation d'une latitude
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $latitude La latitude testée
 * @param int $id_auteur[optional]
 */
 
function inc_inscription2_valide_latitude_dist($latitude,$id_auteur=NULL) {
	if(!$latitude){
		return;
	}
	else if((!is_numeric($latitude))||($latitude < -90) || ($latitude > 90)){
		// verifier que la latitude soit valide	
		return _T('i2_geo:saisir_latitude_valide');
	}
	return;
}

?>