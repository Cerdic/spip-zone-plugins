<?php
/**
 * 
 * Fonction de validation du reglement
 * Utilisé que lors de la création de compte
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $statut Le statut testé
 * @param int $id_auteur[optional]
 */
function inc_inscription2_valide_reglement_dist($reglement,$id_auteur=nulle) {
	spip_log('verification pour le reglement');
	if(intval($id_auteur)){
		return false;
	}
	else{
		if($reglement){
			return false;
		}
		else{
			return _T('inscription2:erreur_reglement_obligatoire');
		}
	}
}

?>