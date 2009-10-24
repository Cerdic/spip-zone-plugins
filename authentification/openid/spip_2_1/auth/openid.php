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

//
/**
 * Authentifie via OPENID
 *	lance une redirection http au premier passage (log_step='check')
 *  et retourne la ligne SQL decrivant l'utilisateur si ok au second passage (log_step='ok')
 * @param string $login
 * @param string $pass
 * @param string $serveur
 * @param string $log_step
 * @return <type>
 */
function auth_openid_dist ($login, $pass, $serveur='', $log_step='check') {

	// il faut un login non vide ET un pass vide (sinon cela ne concerne pas OpenID)
	if (!strlen($login) OR strlen($pass)) return false;
	$auteur = false;
	
	// retrouver le login
	if (!$login = auth_openid_retrouver_login($login)
		OR !$idurl = sql_getfetsel("openid", "spip_auteurs", "login=" . sql_quote($login),"","","","",$serveur) )
		return false;
	
	if ($log_step=='check') {
		// * Si l'openid existe, la procedure continue en redirigeant
		// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
		// sur la page login, en cas de reussite, sur l'action controler_openid
		// * S'il l'openid n'existe pas, on est de retour ici, et on continue
		// pour d'autres methodes d'identification
		include_spip('inc/openid');
		$erreurs_openid = demander_authentification_openid($idurl, url_absolue(self()));
		// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
		// on la renvoie
		return $erreurs_openid;
	}
	elseif ($log_step=='ok'){
		$auteur = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login),"","","","",$serveur);
		$auteur['auth'] = 'openid'; // on se log avec cette methode, donc
	}

	return $auteur;
}

function auth_openid_formulaire_login($flux){
	include_spip('inc/openid');
	$flux['data'] = openid_login_form($flux['data'],$flux['args']['contexte']);
	return $flux;
}


/**
 * Retrouver le login de quelqu'un qui cherche a se loger
 * Reconnaitre aussi ceux qui donnent leur nom ou email au lieu du login
 *
 * @param string $login
 * @return string
 */
function auth_openid_retrouver_login($login, $serveur=''){
	include_spip('inc/openid');

	// regarder si l'utilisateur figure deja dans la base
	// il faut au moins que ca ressemble un peu a un openid !
	if (is_openid($login)
	  AND $idurl = nettoyer_openid($login) 		// si pas de protocole, mettre http://
		AND $r = sql_getfetsel("login", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "openid=" . sql_quote($idurl)),'','','','',$serveur)) {

		return $r;
	}

	// encore plus fort :
	// si le login est un login spip mais qu'on arrive la,
	// et qu'il a pas fournit de pass
	// dans ce cas, si l'utilisateur a un openid on peut tenter de la loger avec !
	if ($r = sql_getfetsel("login", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "login=" . sql_quote($login). " AND openid>''"),'','','','',$serveur)){
		return $r;
	}

	return false;
}

?>