<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Charge le client PHP pour dialoguer avec l'API Facebook
 *
 * @return Retourne une instance de la classe du client Facebook 
 */
function fblogin_charger_client(){
	static $client = NULL;
	
	if (is_null($client)){
		// Inclure la librairie Facebook
		include_spip('lib/facebook-php-sdk-9513f08/src/facebook');
		// Inclure la librairie de configuration
		include_spip('inc/config');
		
		if ($app_id = lire_config('fblogin/app_id') and $secret_key = lire_config('fblogin/secret_key')){
			$parametres = array(
				'appId'  => $app_id,
				'secret' => $secret_key,
				'cookie' => true,
			);
		
			$client = new Facebook($parametres);
		
			// Problèmes avec SSL
			Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
			Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
		}
	}
	
	return $client;
}

/*
 * Générer une URL de retour après authentification
 *
 * @param string $redirect L'URL sur laquelle revenir tout à la fin
 * @return string Retourne une URL d'action pour finaliser l'authentification
 */
function fblogin_url_retour_inscription($redirect=''){
	$securiser_action = charger_fonction('securiser_action','inc');
	return $securiser_action('inscrire_fblogin', '', $redirect, true);
}

/*
 * Partir chez Facebook pour s'authentifier puis revenir
 *
 * Lancer une demande d'auth par Facebook
 * consiste à rediriger vers le serveur Facebook
 * qui renverra sur l'url $retour apres identification
 *
 * Si tout se passe bien, la fonction quitte par une redirection+exit
 * En cas d'echec, la fonction renvoie une erreur
 *
 * @param string $retour URL de retour
 * @return string
 */
function fblogin_demander_authentification($retour){
	if ($facebook = fblogin_charger_client()){
		// User ID
		$user = $facebook->getUser();
	
		// S'il y a un utilisateur reconnu, c'est que peut-être le visiteur est connecté à Facebook
		// Mais pas sûr à 100% car le jeton peut être périmé, donc on try{}
		if ($user) {
			try {
				// Est-ce que le visiteur est vraiment connecté ? Si oui on récupère son profil
				$profil = $facebook->api('/me');
			} catch (FacebookApiException $e) {
				spip_log($e->getMessage(), 'fblogin');
				$user = $profil = null;
			}
		}
	
		// Maintenant on peut être sûr de savoir s'il y a vraiment une connexion ou pas
		// Pour que ce soit bon il faut au moins l'email
		include_spip('inc/headers');
		if ($user and $profil['email']) {
			echo '<div class="formulaire_spip">'
			. redirige_formulaire($retour)
			. '</div>';
			exit;
		}
		// Si c'est pas bon il faut aller se connecter en demandant les bonnes autorisations
		else{
			// Les autorisations passent dans un pipeline pour pouvoir en ajouter suivant les besoins
			$autorisations = pipeline(
				'fblogin_autorisations',
				'email'
			);
			
			$url_login = $facebook->getLoginUrl(array(
				'redirect_uri' => $retour,
				'scope' => 	$autorisations,
			));
		
			echo '<div class="formulaire_spip">'
			. redirige_formulaire($url_login)
			. '</div>';
			exit;
		}
	}
	else{
		return _T('fblogin:erreur_client');
	}
}

/**
 * Finir l'authentification apres le retour depuis le serveur Facebook
 * renvoie une chaîne d'erreur en cas d'erreur
 * un tableau decrivant l'utilisateur en cas de succès
 *
 * @return mixed
 */
function fblogin_terminer_authentification(){
	 if ($facebook = fblogin_charger_client()){
		// User ID
		$user = $facebook->getUser();
		
		// S'il y a un utilisateur reconnu, c'est que peut-être le visiteur est connecté à Facebook
		// Mais pas sûr à 100% car le jeton peut être périmé, donc on try{}
		if ($user) {
			try {
				// Est-ce que le visiteur est vraiment connecté ? Si oui on récupère son profil
				$profil = $facebook->api('/me');
			} catch (FacebookApiException $e) {
				spip_log($e->getMessage(), 'fblogin');
				$user = $profil = null;
			}
		}
		
		// Maintenant on sait si connecté ou pas
		if ($user and $fb_uid = $profil['id'] and $email = $profil['email']){
			$identite = array();
			// Celui de FB sinon le login de l'email
			$identite['login'] = isset($profil['nickname']) ? $profil['nickname'] : reset(explode('@', $email));
			$identite['email'] = $email;
			$identite['nom'] = isset($profil['name']) ? $profil['name'] : $identite['login'];
			$identite['fb_uid'] = $fb_uid;
			// Un pipeline pour ajouter des informations en plus de celles de base, à partir du profil récupéré
			$identite = pipeline(
				'fblogin_recuperer_identite',
				array(
					'args' => $profil,
					'data' => $identite
				)
			);
			return $identite;
		}
		// Sinon on cherche une erreur
		else{
			if ($error = _request('error')){
				return _request('error_description');
			}
		}
	}
	else{
		return _T('fblogin:erreur_client');
	}
}

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
      <script src='http://connect.facebook.net/fr_FR/all.js'>
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
      <fb:login-button perms='email' onlogin='FB_JS.reload();'>
         Login with Facebook
      </fb:login-button>";
      
     
	return $texte;

	
}

?>
