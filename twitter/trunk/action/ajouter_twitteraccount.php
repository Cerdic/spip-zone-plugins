<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction préalable à la demande d'autorisation chez twitter
 * Elle permet également de dissocier un compte twitter en passant 
 * arg dans l'environnement à la valeur '-1'
 */
function action_ajouter_twitteraccount_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip("inc/autoriser");
	if(autoriser("ajouter","twitteraccount")){

		$cfg = @unserialize($GLOBALS['meta']['microblog']);

		$redirect = _request('redirect');
		$redirect = parametre_url(parametre_url($redirect,'erreur_code',''),'erreur','','&');

		include_spip('inc/filtres');
		include_spip('inc/twitteroauth');
		include_spip('inc/session');
		
		/**
		 * L'URL de callback qui sera utilisée suite à la validation chez twitter
		 * Elle vérifiera le retour et finira la configuration
		 */
		$oauth_callback = url_absolue(generer_url_action('twitter_oauth_callback','',true));

		/**
		 * Récupération des tokens depuis twitter par rapport à notre application
		 * On les place dans la session de l'individu en cours
		 * Ainsi que l'adresse de redirection pour la seconde action
		 */
		$connection = new TwitterOAuth($cfg['twitter_consumer_key'], $cfg['twitter_consumer_secret']);
		$request_token = $connection->getRequestToken($oauth_callback);
		$token = $request_token['oauth_token'];
		session_set('oauth_token',$token);
		session_set('oauth_token_secret',$request_token['oauth_token_secret']);
		session_set('twitter_redirect',str_replace('&amp;','&',$redirect));
		
		/**
		 * Vérification du code de retour
		 */
		switch ($code = $connection->http_code) {
			/**
			 * Si le code de retour est 200 (ok)
			 * On envoie l'utilisateur vers l\'url d'autorisation
			 */
			case 200:
				$url = $connection->getAuthorizeURL($token);
				include_spip('inc/headers');
				$GLOBALS['redirect'] = $url;
				#echo redirige_formulaire($url);
				break;
			/**
			 * Sinon on le renvoie vers une erreur
			 */
			default:
				spip_log('Erreur connexion twitter','microblog');
				spip_log($connection, 'twitter'._LOG_ERREUR);
				$redirect = parametre_url($redirect,'erreur_code',$code);
				$redirect = parametre_url($redirect,'erreur','erreur_conf_app','&');
				$GLOBALS['redirect'] = $redirect;
				break;
		}
	}
}

/**
 * Ajouter un compte dans la liste des comptes dispos
 * a partir de ses tokens (meme format que dans twitter_connect()
 *
 * @param array $tokens
 *   twitter_token : token du compte a utiliser
 *   twitter_token_secret : token secret du compte a utiliser
 * @return array
 */
function twitter_ajouter_twitteraccount($tokens){
	$cfg = @unserialize($GLOBALS['meta']['microblog']);

	include_spip("inc/twitter");
	$options = $tokens;
	$options['force'] = true;
	if ($res = twitter_api_call("account/verify_credentials","get",array(),$options)){
		$cfg['twitter_accounts'][$res['screen_name']] = array(
			'token' => $tokens['twitter_token'],
			'token_secret' => $tokens['twitter_token_secret'],
		);
	}
	else {
		$cfg['twitter_accounts'][] = array(
			'token' => $tokens['twitter_token'],
			'token_secret' => $tokens['twitter_token_secret'],
		);
		spip_log("Echec account/verify_credentials lors de l'ajout d'un compte","twitter"._LOG_ERREUR);
	}
	if (!isset($cfg['default_account'])
	  OR !isset($cfg['twitter_accounts'][$cfg['default_account']]))
		$cfg['default_account'] = reset(array_keys($cfg['twitter_accounts']));

	ecrire_meta("microblog", serialize($cfg));

	return $cfg;
}
?>