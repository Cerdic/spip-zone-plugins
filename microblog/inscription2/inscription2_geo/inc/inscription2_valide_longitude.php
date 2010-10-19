<?php
/**
 * 
 * Fonction de validation d'une longitude
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $longitude La longitude testée
 * @param int $id_auteur[optional]
 */
 
function inc_inscription2_valide_longitude_dist($longitude,$id_auteur=NULL) {
	if(!$longitude){
		return;
	}
	else if((!is_numeric($longitude))||($longitude < -180) || ($longitude > 180)){
		// verifier que la longitude soit valide	
		return _T('i2_geo:saisir_longitude_valide');
	}
	return;
}

?>