<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

// Authentifie via OPENID et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_openid_dist ($login, $pass, $md5pass="", $md5next="") {

	// il faut un login non vide et qui contient au moins un point
	// car c'est cense etre une url
	// si on a rentre un mot de passe, alors ce n'est pas une tentative openid non plus
	if (!$login 
		OR strlen($pass) 
		OR (strlen($md5pass) AND strlen($md5next)))
		return false;
	$auteur = false;
	$idurl = "";

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
	if (!$auteur){
		// Si l'utilisateur figure dans la base, y recuperer les infos
		$auteur = sql_fetsel("*", "spip_auteurs", array("statut!=".sql_quote("5poubelle") , "login=" . sql_quote($login). " AND openid>''"));
	}

	if (!$auteur AND $idurl){
		if (verifier_openid($idurl))
			return _T("openid:form_login_openid_inconnu");
	}

	if ($auteur) {
		// refuser la premiere connexion d'un nouvel auteur : il faut utiliser le pass
		// pour confirmer l'email
		if ($auteur['statut']=='nouveau')
			return _T('openid:form_login_statut_nouveau');

		// * Si l'openid existe, la procedure continue en redirigeant
		// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
		// sur la page login, en cas de reussite, sur l'action controler_openid
		// * S'il l'openid n'existe pas, on est de retour ici, et on continue
		// pour d'autres methodes d'identification
		include_spip('inc/openid');
		// il faut remettre var_login dans l'url de retour pour que le form de login retrouve le login
		// des non admin ...
		$retour = auth_openid_url_retour_login($auteur['login'],url_absolue(parametre_url(self(),'var_login',$auteur['login'],'&')));
		$erreurs_openid = demander_authentification_openid($auteur['openid'], $retour);
		// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
		$auteur = false;
	}

	return $auteur;
}


function auth_openid_terminer_identifier_login($login, $serveur=''){
	include_spip('inc/openid');
	$retour = auth_openid_url_retour_login($login);
	$auteur = terminer_authentification_openid($retour);

	if (is_string($auteur))
		return $auteur; // erreur !

	if (is_array($auteur)
		AND isset($auteur['openid'])
		AND $openid = $auteur['openid']
	  AND $auteur = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login)." AND openid=".sql_quote($openid),"","","","",$serveur)){

		$auteur['auth'] = 'openid'; // on se log avec cette methode, donc
		return $auteur;
	}
	return false;
}



/**
 * Fournir une url de retour apres login par un SSO
 * pour finir l'authentification
 *
 * @param string $auth_methode
 * @param string $login
 * @param string $serveur
 * @return string
 */
function auth_openid_url_retour_login($login, $redirect='', $serveur=''){
	$securiser_action = charger_fonction('securiser_action','inc');
	return $securiser_action('controler_openid', $login, $redirect, true);
}


 /**
  * Loger un auteur suite a son identification
  *
  * @param array $auteur
  */
 function auth_loger($auteur){
	if (!is_array($auteur) OR !count($auteur))
		return false;

	$session = charger_fonction('session', 'inc');
	$session($auteur);
	$p = ($auteur['prefs']) ? unserialize($auteur['prefs']) : array();
	$p['cnx'] = ($session_remember == 'oui') ? 'perma' : '';
	$p = array('prefs' => serialize($p));
	sql_updateq('spip_auteurs', $p, "id_auteur=" . $auteur['id_auteur']);

	// Si on est admin, poser le cookie de correspondance
	if ($auteur['statut'] == '0minirezo') {
		include_spip('inc/cookie');
		spip_setcookie('spip_admin', '@'.$auteur['login'],
		time() + 7 * 24 * 3600);
	}

	//  bloquer ici le visiteur qui tente d'abuser de ses droits
	verifier_visiteur();
	return true;
}
?>