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
function auth_openid_dist ($login, $pass, $md5pass="", $md5next="", $log_step='check') {

	// il faut un login non vide et qui contient au moins un point
	// car c'est cense etre une url
	if (!$login) return false;
	$auteur = false;
	
	// il faut au moins que ca ressemble un peu a un openid !
	if (is_openid($login)){
		include_spip('inc/openid');
		// si pas de protocole, mettre http://
		$login = nettoyer_openid($login);

		// Si l'utilisateur figure deja dans la base, y recuperer les infos
		$auteur = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "openid=" . sql_quote($login)));
	}
	
	// encore plus fort :
	// si le login est un login spip mais qu'on arrive la,
	// et qu'il a pas fournit de pass
	// dans ce cas, si l'utilisateur a un openid on peut tenter de la loger avec !
	if ($log_step=='check' AND !$auteur){
		// Si l'utilisateur figure dans la base, y recuperer les infos
		$auteur = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "login=" . sql_quote($login)));
		if (!$auteur['openid'])
			$auteur = false;
	}

	if ($log_step=='check' AND $auteur) {
		// * Si l'openid existe, la procedure continue en redirigeant
		// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
		// sur la page login, en cas de reussite, sur l'action controler_openid
		// * S'il l'openid n'existe pas, on est de retour ici, et on continue
		// pour d'autres methodes d'identification
		include_spip('inc/openid');
		$erreurs_openid = demander_authentification_openid($auteur['openid'], url_absolue(self()));
		// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
		$auteur = false;
	}

	if ($auteur AND $log_step=='ok'){
		$auteur['auth'] = 'openid';
	}

	return is_array($auteur) ? $auteur : array();
}

// determine si un login est de type openid (une url avec http ou https)
function is_openid($login){
	// Detection s'il s'agit d'un URL à traiter comme un openID
	// RFC3986 Regular expression for matching URIs
	#if (preg_match('_^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?:#(.*))?$_', $login, $uri_parts)
	#	AND ($uri_parts[1] == "http" OR $uri_parts[1] == "https")) {

	// s'il y a un point, c'est potentiellement un login openid
	// ca permet d'eliminer un bon nombre de pseudos tout en
	// autorisant les connexions openid sans avoir besoin de renseigner le http://
	if (strpos($login, '.')!==false) {
		return true;
	} else {
		return false;
	}
}

?>