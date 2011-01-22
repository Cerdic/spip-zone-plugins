<?php
/**
 * 
 * Fonction de validation d'un login
 * 
 * @return false|string retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire 
 * @param string $login Le login testé
 * @param int $id_auteur[optional]
 */

function inc_inscription2_valide_login_dist($login,$id_auteur=NULL) {
	if(!$login){
		return false;
	}
	else{
		// Vérifier si login correct
		if (! preg_match("/[A-Za-z0-9_-]*/",$login)){
			return _T('inscription2:caracteres_interdit');	
		}


		// Vérifier si le login est déjà utilisé
		if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($id_auteur)."' AND login = '$login'")) {
			return _T('inscription2:formulaire_login_deja_utilise');
		}
		// Vérifier si le login est trop court
		if (strlen($login) < _LOGIN_TROP_COURT){
			return _T('info_login_trop_court');	
		}
	}
	return;
}

?>