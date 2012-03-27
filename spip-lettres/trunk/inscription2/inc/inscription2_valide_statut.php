<?php
/**
 * 
 * Fonction de validation du statut
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $statut Le statut testé
 * @param int $id_auteur[optional]
 */
function inc_inscription2_valide_statut_dist($statut,$id_auteur=NULL) {
	global $liste_des_statuts;
	
	if(!$statut){
		return false;
	}
	else{
		if(in_array($statut,$liste_des_statuts)){
			return false;
		}
		else{
			return _T('inscription2:statut_valide');
		}
	}
}

?>