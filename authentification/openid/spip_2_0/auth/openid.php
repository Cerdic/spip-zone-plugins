<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

// Authentifie via OPENID et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_openid_dist ($login, $pass, $md5pass="", $md5next="", $log_step='check') {

	// il faut un login non vide et qui contient au moins un point
	// car c'est cense etre une url
	if (!$login) return false;
	$auteur = false;

	// il faut au moins que ca ressemble un peu a un openid !
	include_spip('inc/openid');
	if (is_openid($login)){
		// si pas de protocole, mettre http://
		$idurl = nettoyer_openid($login);

		// Si l'utilisateur figure deja dans la base, y recuperer les infos
		$auteur = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "openid=" . sql_quote($idurl)));
	}
	
	// encore plus fort :
	// si le login est un login spip mais qu'on arrive la,
	// et qu'il a pas fournit de pass
	// dans ce cas, si l'utilisateur a un openid on peut tenter de la loger avec !
	if ($log_step=='check' AND !$auteur){
		// Si l'utilisateur figure dans la base, y recuperer les infos
		$auteur = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "login=" . sql_quote($login). " AND openid>''"));
	}

	if ($log_step=='check' AND $auteur) {
		// * Si l'openid existe, la procedure continue en redirigeant
		// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
		// sur la page login, en cas de reussite, sur l'action controler_openid
		// * S'il l'openid n'existe pas, on est de retour ici, et on continue
		// pour d'autres methodes d'identification
		include_spip('inc/openid');
		$retour = parametre_url(openid_url_reception(), "url", url_absolue($cible), '&');
		$erreurs_openid = demander_authentification_openid($auteur['openid'], url_absolue(self()));
		// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
		$auteur = false;
	}

	if ($auteur AND $log_step=='ok'){
		$auteur['auth'] = 'openid';
	}

	return is_array($auteur) ? $auteur : array();
}


?>