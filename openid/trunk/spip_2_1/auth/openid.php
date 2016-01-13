<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

//
/**
 * Authentifie via OPENID
 *	lance une redirection http au premier passage (log_step='check')
 *  et retourne la ligne SQL decrivant l'utilisateur si ok au second passage (log_step='ok')
 * @param string $login
 * @param string $pass
 * @param string $serveur
 * @return <type>
 */
function auth_openid_dist ($login, $pass, $serveur='') {

	// il faut un login non vide ET un pass vide (sinon cela ne concerne pas OpenID)
	if (!strlen($login) OR strlen($pass)) return false;
	$auteur = false;

	if (!$l = auth_openid_retrouver_login($login)){
		if (is_openid($login)
			AND $idurl = nettoyer_openid($login)
			AND verifier_openid($idurl))
			return _T("openid:form_login_openid_inconnu");
		return false;
	}
	$login = $l;


	// retrouver le login
	if (!$auteur = sql_fetsel("openid,statut", "spip_auteurs", "login=" . sql_quote($login),"","","","",$serveur) )
		return false;

	if (!$idurl = $auteur['openid'])
		return false;

	if ($auteur['statut']=='nouveau')
		return _T('openid:form_login_statut_nouveau');
	
	// * Si l'openid existe, la procedure continue en redirigeant
	// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
	// sur la page login, en cas de reussite, sur l'action controler_openid
	// * S'il l'openid n'existe pas, on est de retour ici, et on continue
	// pour d'autres methodes d'identification
	include_spip('inc/openid');
	$retour = auth_url_retour_login('openid', $login, url_absolue(self()));
	$erreurs_openid = demander_authentification_openid($idurl, $retour);
	// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
	// on la renvoie
	return $erreurs_openid;
}

function auth_openid_terminer_identifier_login($login, $serveur=''){
	include_spip('inc/openid');
	$retour = auth_url_retour_login('openid', $login);
	$auteur = terminer_authentification_openid($retour);

	if (is_string($auteur))
		return $auteur; // erreur !

	if (is_array($auteur)
		AND isset($auteur['openid'])
		AND $openid = $auteur['openid']
	  AND $auteur = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login)." AND openid=".sql_quote($openid),"","","","",$serveur)){

		$auteur['auth'] = 'openid'; // on se log avec cette methode, donc

		// prevoir la redirection
		if (!_request('redirect')
		AND $r = _request('openid_return_to')
		AND $p = parametre_url($r, 'redirect')) {
			set_request('redirect', $GLOBALS['redirect'] = $p);
			#var_dump($p, $r, $_REQUEST);
		}

		return $auteur;
	}
	return false;
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

	// ou par email
	if ($r = sql_getfetsel("login", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "email=" . sql_quote($login). " AND openid>''"),'','','','',$serveur)){
		return $r;
	}


	return false;
}

?>