<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Authentifie via fblgoin
 * @param string $login
 * @param string $pass
 * @param string $serveur
 * @return <type>
 */
 
function auth_fblogin_dist ($login, $pass, $serveur='') {

// il faut vérifier que l'on détecte une session FB, on commence par inclure la bibliothèque FB
	include_spip('inc/facebook');
	include_spip('inc/fblogin');
	
	fblogin_log("Je suis passé par ici", 2);
	
	// Création de l'objet Facebook
	$facebook = new Facebook(array(
	  'appId'  => '_FB_APP_ID',
	  'secret' => '_FB_SECRET_ID',
	  'cookie' => true,
	));
	
	$session = $facebook->getSession();
	
	// On ne trouve pas de cookie, on renvoie vers FB
	if (!$session) {
		header('Location:'.$facebook->getLoginUrl(array('req_perms'=>'email','locale'=>'fr_FR')));
		return false;
	}
	// On a trouvé un cookie, on a donc les infos sur l'auteur. Je les stocke dans $fbme
	else {
		try {
			$_SESSION['fb_session'] = $session;
			$uid = $facebook->getUser();
			$fbme = $facebook->api('/me');
		}
		catch (FacebookApiException $erreurs_fblogin){return $erreurs_fblogin;};
	}
	
	$password = sha1(uniqid());
	
	// On va vérifier que l'uid est bien présent dans la table auteurs
	if (!sql_countsel("spip_auteurs","fb_uid=$uid")) {
		//Il n'y a pas d'uid. On verifie si l'auteur avait déjà un compte SPIP en s'appuyant sur son e-mail
			if (!sql_countsel("spip_auteurs","email =".sql_quote($fbme['email']))) {
				// SPIP n'a pas trouvé de correspondance entre l'email FB et les e-mails de la table auteurs
				// On crée donc un nouvel auteur pour ne pas dépendre de FB
				$id_auteur = sql_insertq('spip_auteurs',
					array('nom'=> $fbme['name'], 
						  'email' => $fbme['email'],
						  'login' => $fbme['email'],
						  'pass' => $password,
						  'statut' => "1comite",
						  'source' => "fblogin",
						  'fb_uid' => $uid));
				$auteur=sql_fetsel("*", "spip_auteurs", "id_auteur=".sql_quote($id_auteur),'','','','',$serveur);
				}
				// SPIP a trouvé un e-mail correspondant, on va juste ajouter l'uid à l'auteur existant
			else {
				$auteur = sql_updateq("spip_auteurs",array('source'=>"fblogin",'fb_uid' => $uid),"email =".sql_quote($fbme['email']));
				$auteur=sql_fetsel("*", "spip_auteurs", "fb_uid=".sql_quote($uid),'','','','',$serveur);
				}
		}
	else {
		// l'uid Facebook de l'auteur existe, il faut juste retrouver ses informations pour le logguer
		$auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=" . sql_quote($uid) . " AND source='fblogin'",'','','','',$serveur);
	}
	
	$login = $auteur['login'];
	return $auteur;
}
 
function auth_fblogin_terminer_identifier_login($login, $serveur=''){
	// Création de l'objet Facebook
	$facebook = new Facebook(array(
	  'appId'  => '_FB_APP_ID',
	  'secret' => '_FB_SECRET_ID',
	  'cookie' => true,
	));
	
	$session = $facebook->getSession();

	$_SESSION['fb_session'] = $session;
	$uid = $facebook->getUser();
	$fbme = $facebook->api('/me');
	
	$auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=" . sql_quote($uid) . " AND source='fblogin'",'','','','',$serveur);

	return $auteur;
}

function auth_fblogin_retrouver_login($login, $serveur='') {
	// Création de l'objet Facebook
	$facebook = new Facebook(array(
	  'appId'  => '_FB_APP_ID',
	  'secret' => '_FB_SECRET_ID',
	  'cookie' => true,
	));
	
	$session = $facebook->getSession();

	$_SESSION['fb_session'] = $session;
	$uid = $facebook->getUser();
	$fbme = $facebook->api('/me');

	
	if($auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=".sql_quote($uid),'','','','',$serveur)) {
		return $auteur;
	}
	
	return false;
}

function auth_fblogin_formulaire_login($flux){
	include_spip('inc/fblogin');
	$flux['data'] = fblogin_login_form($flux['data'],$flux['args']['contexte']);
	return $flux;
}


?>