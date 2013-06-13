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
function action_twitter_oauth_request_dist() {
	global $visiteur_session;

	if(isset($visiteur_session['statut'])){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		
		$cfg = @unserialize($GLOBALS['meta']['microblog']);
		
		$redirect = _request('redirect');
		$redirect = parametre_url(parametre_url($redirect,'erreur_code',''),'erreur','');
		
		/**
		 * Si on a passé comme argument la valeur '-1',
		 * on souhaite dissocier un compte twitter de microblog
		 * On modifie la méta de configuration et on redirige sur l'url de redirection
		 */
		if($arg == '-1'){
			unset($cfg['twitter_token']);
			unset($cfg['twitter_token_secret']);
			ecrire_meta("microblog", serialize($cfg));
			redirige_formulaire($redirect);
		}
		
		include_spip('inc/filtres');
		include_spip('inc/twitteroauth');
		include_spip('inc/session');
		
		/**
		 * L'URL de callback qui sera utilisée suite à la validation chez twitter
		 * Elle vérifiera le retour et finira la configuration
		 */
		$oauth_callback = str_replace('&amp;','&',generer_url_action('twitter_oauth'));

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
		session_set('twitter_redirect',str_replace('&amp;','&',_request('redirect')));
		
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
				redirige_formulaire($url);
				break;
			/**
			 * Sinon on le renvoie vers une erreur
			 */
			default:
				spip_log('Erreur connexion twitter','microblog');
				spip_log($connection, 'microblog');
				$redirect = parametre_url($redirect,'erreur_code',$code);
				$redirect = parametre_url($redirect,'erreur','erreur_conf_app');
				redirige_formulaire($redirect);
				break;
		}
	}
}
?>