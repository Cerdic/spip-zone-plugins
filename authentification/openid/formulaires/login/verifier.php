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
	else 
		$login = $session_login;  // laisser une chance

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
	$p = array('prefs' => serialize($p));
	sql_updateq('spip_auteurs', $p, "id_auteur=" . $auteur['id_auteur']);
	//  bloquer ici le visiteur qui tente d'abuser de ses droits
	verifier_visiteur();
	return (is_null($prive) ? is_url_prive($cible) : $prive)
	?  login_autoriser() : array();
}

?>
