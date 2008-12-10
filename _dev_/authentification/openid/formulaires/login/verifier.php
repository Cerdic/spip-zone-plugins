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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('formulaires/login');

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


function formulaires_login_verifier($cible="",$login="",$prive=null){
	
	$session_login = _request('var_login');
	$session_password = _request('password');
	$session_md5pass = _request('session_password_md5');
	$session_md5next = _request('next_session_password_md5');
	$session_remember = _request('session_remember');

	if (!$session_login) {
		# pas de login saisi !
		return array('message_erreur' =>
			_T('login_identifiant_inconnu',
				array('login' => htmlspecialchars($login))));
	}
		
	$row = retrouver_login($session_login);
	if ($row) 
		$login = $row['login'];
	elseif (spip_connect_ldap()) 
		$login = $session_login;  // laisser une chance	
	else {
		
		#openid# tester le login openid
		$erreurs_openid = "";
		if (is_openid($session_login)) {
			// * Si quelqu'un possede effectivement cet openid,
			// on demande l'authentification
			$auth_openid = charger_fonction('openid','auth');
			if ($auth_openid($session_login)) {
				// * Si l'openid existe, la procedure continue en redirigeant 
				// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
				// sur la page login, en cas de reussite, sur l'action controler_openid
				// * S'il l'openid n'existe pas, on est de retour ici, et on continue
				// pour d'autres methodes d'identification
				include_spip('inc/openid');
				$erreurs_openid = demander_authentification_openid($session_login, $cible);
				// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
			}
		}
		#/openid#
		
		include_spip('inc/cookie');
		spip_setcookie("spip_admin", "", time() - 3600);
		return array('message_erreur' =>
			_T('login_identifiant_inconnu' . ($erreurs_openid ? "<br />" . $erreurs_openid:""),
			array('login' => htmlspecialchars($session_login))));
	}
	$auteur = verifier_login($login, $session_password, $session_md5pass, $session_md5next);
	if (!$auteur) {
		if (strlen($session_password) OR strlen($session_md5pass))
			return array('password' => _T('login_erreur_pass'));
		// sinon c'est un login en deux passe old style (ou js en panne)
		// pas de message d'erreur
		else return array('password' => ' ');
	}
	// on a ete authentifie, construire la session
	// en gerant la duree demandee pour son cookie 
	if ($session_remember !== NULL)
		$auteur['cookie'] = $session_remember;
	$session = charger_fonction('session', 'inc');
	$session($auteur);
	$p = ($auteur['prefs']) ? unserialize($auteur['prefs']) : array();
	$p['cnx'] = ($session_remember == 'oui') ? 'perma' : '';
	$p = array('prefs' => serialize($prefs));
	sql_updateq('spip_auteurs', $p, "id_auteur=" . $auteur['id_auteur']);
	//  bloquer ici le visiteur qui tente d'abuser de ses droits
	verifier_visiteur();
	return (is_null($prive) ? is_url_prive($cible) : $prive)
	?  login_autoriser() : array();
}

?>
