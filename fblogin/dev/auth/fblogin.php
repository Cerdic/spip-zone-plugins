<?php
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Authentifie via Facebook
 * @param string $login
 * @param string $pass
 * @param string $serveur
 * @return <type>
 */
function auth_fblogin_dist ($login, $pass, $serveur='') {
	// il faut un login non vide ET un pass vide (sinon cela ne concerne pas Facebook)
	if (!strlen($login) OR strlen($pass)) return false;
	$auteur = false;
	
	// On cherche si ce login ou email est lié a un UID Facebook
	$auteur = sql_fetsel(
		'fb_uid, statut',
		'spip_auteurs',
		array(
			'statut != '.sql_quote('5poubelle'),
			'login = '.sql_quote($login).' or email = '.sql_quote($login),
			'fb_uid > 0'
		), 
		'', '', '', '', $serveur
	);
	
	// Si jamais connecté, il faut se connecter une première fois avec le vrai mot de passe
	if ($auteur['statut'] == 'nouveau'){
		return _T('fblogin:form_login_statut_nouveau');
	}
	
	include_spip('inc/fblogin');
	$retour = auth_url_retour_login('fblogin', $login, url_absolue(self()));
	$erreur = fblogin_demander_authentification($retour);
	
	// Si on arrive là c'est qu'il y a une erreur
	return $erreur;
}
 
function auth_fblogin_terminer_identifier_login($login, $serveur=''){
	include_spip('inc/fblogin');
	$auteur = fblogin_terminer_authentification();
	
	if (is_string($auteur)){
		return $auteur;
	}
	
	if (is_array($auteur)
		and isset($auteur['fb_uid'])
		and $fb_uid = $auteur['fb_uid']
		and $auteur = sql_fetsel(
			'*',
			'spip_auteurs',
			array(
				'login = '.sql_quote($login).' or email = '.sql_quote($login),
				'fb_uid = '.sql_quote($fb_uid)
			),
			'', '', '', '', $serveur
		)
	){
		$auteur['auth'] = 'fblogin';
		return $auteur;
	}
	
	return false;
}

// Même sans login on regarde si on trouve un auteur connecté à FB
function auth_fblogin_retrouver_login($login, $serveur='') {
	include_spip('inc/fblogin');
	$auteur = fblogin_terminer_authentification();
	
	if (is_array($auteur)
		and isset($auteur['fb_uid'])
		and $fb_uid = $auteur['fb_uid']
		and $auteur = sql_fetsel(
			'*',
			'spip_auteurs',
			array(
				'fb_uid = '.sql_quote($fb_uid)
			),
			'', '', '', '', $serveur
		)
	){
		return $auteur;
	}
	
	return false;
}

?>
