<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de validation d'un login
 *
 * @param string $valeur 
 * 		Le login testé
 * @param array $options [optional]
 * 		Le tableau des options
 * @return false|string 
 * 		Retourne false si pas de valeurs ou si la valeur est correcte, un message d'erreur dans le cas contraire
 */
function inc_inscription3_valide_login_dist($valeur,$options=array()) {
	if(!$valeur)
		return false;
	else{
		// Vérifier si le login est déjà utilisé
		if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($options['id_auteur'])."' AND login = '$valeur'")) 
			return _T('inscription3:erreur_login_deja_utilise');
		// Vérifier si le login est trop court
		if (strlen($valeur) < _LOGIN_TROP_COURT)
			return _T('info_login_trop_court');
	}
	return;
}

?>