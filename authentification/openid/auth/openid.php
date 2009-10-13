<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Authentifie via OPENID et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_openid_dist ($login, $pass, $md5pass="", $md5next="") {

	// il faut un login non vide et qui contient au moins un point
	// car c'est cense etre une url
	if (!$login) return false;
	$result = false;
	
	// il faut au moins que ca ressemble un peu a une url !
	if (preg_match(';[.];',$login)){
		// si pas de protocole, mettre http://
		if (!preg_match(';^[a-z]{3,6}://;i',$login))
			$login = "http://".$login;
		$login = rtrim($login,'/');

		// Si l'utilisateur figure deja dans la base, y recuperer les infos
		$result = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "openid=" . sql_quote($login)));
	}
	
	// encore plus fort :
	// si le login est un login spip mais qu'on arrive la,
	// et qu'il a pas fournit de pass
	// dans ce cas, si l'utilisateur a un openid on peut tenter de la loger avec !
	if (!$result AND !$pass AND !$md5pass){
		// Si l'utilisateur figure dans la base, y recuperer les infos
		$result = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "login=" . sql_quote($login)));
		if (!$result['openid'])
			$result = false;
	}

	return is_array($result) ? $result : array(); 
}

?>
