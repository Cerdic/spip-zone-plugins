<?php
/**
 * Plugin fblogin
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

@define('_fblogin_LOG', true);

/**
 * Ajout au formulaire de login
 *
 * @param string $texte
 * @param array $contexte
 * @return string
 */
function fblogin_login_form($texte,$contexte){
	$scriptfblogin = "";

	$texte .= "<div id='fb-root'></div>
      <script src='http://connect.facebook.net/en_US/all.js'>
      </script>
      <script>
      window.fbAsyncInit = function() {
         FB.init({ 
            appId:'_FB_APP_ID', cookie:true, 
            status:true, xfbml:true 
         });
        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
      }
      </script>
      <fb:login-button perms='email'>
         Login with Facebook
      </fb:login-button>";
      
     
	return $texte;
}


function terminer_authentification_fblogin($retour) {
	// il faut vérifier que l'on détecte une session FB, on commence par inclure la bibliothèque FB
	include_spip('inc/facebook');

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
		catch (FacebookApiException $erreurs_fblogin){print_r($erreurs_fblogin);};
	}
	
	
	
	$auteur = sql_fetsel("*", "spip_auteurs", "fb_uid=" . sql_quote($uid) . " AND source='fblogin'",'','','','',$serveur);
	
	if($auteur) {return $auteur;}

	$retour = auth_url_retour_login('fblogin', $auteur['login'], url_absolue(self()));
	
	
	// potentiellement, on arrive ici avec une erreur si le login FB donne n'existe pas
	// on la renvoie
	return $erreurs_fblogin;
}



/**
 * Logs pour fblogin, avec plusieurs niveaux pour le debug (1 a 3)
 *
 * @param mixed $data : contenu du log
 * @param int(1) $niveau : niveau de complexite du log
 * @return null
**/
function fblogin_log($data, $niveau=1){
	if (!defined('_fblogin_LOG') OR _fblogin_LOG < $niveau) return;
	spip_log('fblogin: '.$data, 'fblogin');
}



/**
 * Initialisation de l'authent fblogin
 *
 * @return Auth_fblogin_Consumer
 */
function init_auth_fblogin() {


	session_start();
	
	// il faut vérifier que l'on détecte une session FB
	include_spip('inc/facebook');

	// Création de l'objet Facebook
	$facebook = new Facebook(array(
	  'appId'  => '_FB_APP_ID',
	  'secret' => '_FB_SECRET_ID',
	  'cookie' => true,
	));
	
	$session = $facebook->getSession();
	
	return $session;
}


/**
 * Lancer une demande d'auth par fblogin
 * consiste a verifier que le cookie est legitime,
 * sinon on redirige vers le serveur Facebook,
 *
 * Si tout se passe bien, la fonction quitte par une redirection+exit
 * En cas d'echec, la fonction renvoie une erreur
 *
 * @param string $url_fblogin
 * @param string $retour
 * @return string
 */
function demander_authentification_fblogin($retour){
	fblogin_log("Traitement login fblogin pour $url_fblogin",2);
	
	include_spip('inc/facebook');
	
	// Begin the fblogin authentication process.
	$facebook = init_auth_fblogin();
	fblogin_log("Initialisation faite", 3);
	
	$session = $facebook->getSession();
	
	$uid = $facebook->getUser();
	$fbme = $facebook->api('/me');

	
	// Handle failure status return values.
	if (!$session) {
		// ici, on peut rentrer dire que le fblogin n'est pas connu...
		// plutot que de rediriger et passer la main a d'autres methodes d'auth
		fblogin_log("Ce login ($session) n'est pas connu", 2);
		header('Location:'.$fbme->getLoginUrl(array('req_perms'=>'email','locale'=>'fr_FR')));
		return _T('fblogin:erreur_fblogin');
	} 
	
		fblogin_log("Adresse de retour : $retour", 2);
		// on demande quelques informations, dont le login obligatoire
		if ($sreg_request = sql_insertq("spip_auteurs",
					array('nom'=> $fbme['name'], 
						  'email' => $fbme['email'],
						  'login' => $fbme['email'],
						  'fb_uid' => $uid))) // Optional
  		 {
			fblogin_log("Ajout des extensions demandees", 3);
        	$auth_request->addExtension($sreg_request);
		}

		$erreur = "";
		
	
	if ($erreur) {
		fblogin_log("Rentrer avec l'erreur", 3);
		return $erreur;
	}
	
}



/**
 * Fournir une url de retour pour l'inscription par fblogin
 * pour finir l'inscription
 *
 * @param string $idurl
 * @param string $redirect
 * @return string
 */
function fblogin_url_retour_insc($idurl, $redirect=''){
	$securiser_action = charger_fonction('securiser_action','inc');
	return $securiser_action('inscrire_fblogin', $idurl, $redirect, true);
}

?>