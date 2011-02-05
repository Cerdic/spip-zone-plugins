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
 
function auth_fblogin ($login, $pass, $serveur='') {

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
						  'pass' => '',
						  'statut' => "1comite",
						  'source' => "fblogin",
						  'fb_uid' => $uid));
				return $auteur=sql_fetsel("*", "spip_auteurs", "id_auteur=".sql_quote($id_auteur),'','','','',$serveur);
				}
				// SPIP a trouvé un e-mail correspondant, on va juste ajouter l'uid à l'auteur existant
			else {
				$auteur = sql_updateq("spip_auteurs",array('source'=>"fblogin",'fb_uid' => $uid),"email =".sql_quote($fbme['email']));
				return $auteur=sql_fetsel("*", "spip_auteurs", "fb_uid=".sql_quote($uid),'','','','',$serveur);
				}
		}
	else {
		// l'uid Facebook de l'auteur existe, il faut juste retrouver ses informations pour le logguer
		$auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=" . sql_quote($uid) . " AND source='fblogin'",'','','','',$serveur);
		return $auteur;
	}
	

	$login = $auteur['login'];

	
	// * Si la session existe, la procedure continue en redirigeant
	// vers le fournisseur d'identite. En cas d'erreur, il y a une redirection de faite
	// sur la page login, en cas de reussite, sur l'action controler_openid
	// * S'il la session n'existe pas, on est de retour ici, et on continue
	// pour d'autres methodes d'identification
	include_spip('inc/fblogin');
	$retour = auth_url_retour_login('fblogin', $login, url_absolue(self()));
	// potentiellement, on arrive ici avec une erreur si l'openid donne n'existe pas
	// on la renvoie
	return $erreurs_fblogin;
}
 

function auth_fblogin_terminer_identifier_login($login, $serveur=''){
	include_spip('inc/fblogin');
	$retour = auth_url_retour_login('fblogin', $login);
	$auteur = terminer_authentification_fblogin($retour);

	if (is_string($auteur))
		return $auteur; // erreur !

	if (is_array($auteur)
		AND isset($auteur['fb_uid'])
		AND $fb_uid = $auteur['fb_uid']
	  AND $auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=" . sql_quote($uid) . " AND source='fblogin'",'','','','',$serveur)){

		$auteur['auth'] = 'fblogin'; // on se log avec cette methode, donc
		return $auteur;
	}
	return false;
}


function auth_fblogin_retrouver_login($login, $serveur='') {
	if($auteur = sql_fetsel("*", "spip_auteurs", "login=".sql_quote($login),'','','','',$serveur)) {
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