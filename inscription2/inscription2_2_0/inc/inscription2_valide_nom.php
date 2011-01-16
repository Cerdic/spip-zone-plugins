<?php
/**
 * 
 * Fonction de validation d'un nom
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $nom Le nom testé
 * @param int $id_auteur[optional]
 */

function inc_inscription2_valide_nom_dist($nom,$id_auteur=NULL) {
	if(!$nom){
		return false;
	}
	else{
		// Vérifier si nom correct
		if (preg_match("/[A-Za-z0-9_-]*/",$nom)){
			return _T('caracteres_interdit');	
		}

		// Vérifier si le nom est déjà utilisé
		if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($id_auteur)."' AND nom = '$nom'")) {
			return _T('inscription2:formulaire_nom_deja_utilise');
		}
	}
	return;
}

?>