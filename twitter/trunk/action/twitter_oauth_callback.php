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
 * Fonction vérifiant le retour de twitter 
 * Elle met dans la configuration du plugin les tokens 
 * nécessaires pour de futures connexions
 */
function action_twitter_oauth_callback_dist(){

	include_spip("inc/autoriser");
	if(autoriser("ajouter","twitteraccount")){
		include_spip('inc/twitteroauth');
		include_spip('inc/session');
		$cfg = @unserialize($GLOBALS['meta']['microblog']);
		
		if (_request('oauth_token') && ($GLOBALS['visiteur_session']['oauth_token'] !== _request('oauth_token'))) {
			session_set('status','oldtoken');
			$url = session_get('twitter_redirect') ? session_get('twitter_redirect') : $GLOBALS['meta']['url_site_spip'];
			$GLOBALS['redirect'] = $url;
		}
		else {
			$consumer_key = $cfg['twitter_consumer_key'];
			$consumer_secret = $cfg['twitter_consumer_secret'];

			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $GLOBALS['visiteur_session']['oauth_token'], $GLOBALS['visiteur_session']['oauth_token_secret']);
			$access_token = $connection->getAccessToken(_request('oauth_verifier'));
			session_set('access_token',$access_token);

			/**
			 * Si le code de retour est 200 :
			 * L'utilisateur a été vérifié et les tokens d'accès peuvent être
			 * sauvegardés pour un usage futur
			 */
			if (200 == $connection->http_code) {

				// recuperer le screenname
				$tokens = array(
					'token' => $GLOBALS['visiteur_session']['access_token']['oauth_token'],
					'token_secret' => $GLOBALS['visiteur_session']['access_token']['oauth_token_secret'],
				);

				if ($res = twitter_api_call("account/verify_credentials","get",$tokens)){
					$cfg['twitter_accounts'][$res['screen_name']] = $tokens;
				}
				else {
					$cfg['twitter_accounts'][] = $tokens;
					spip_log("Echec account/verify_credentials lors de l'ajout d'un compte","twitter"._LOG_ERREUR);
				}
				var_dump($res);
				var_dump($cfg);

				ecrire_meta("microblog", serialize($cfg));
				die();

				$url = session_get('twitter_redirect') ? session_get('twitter_redirect') : $GLOBALS['meta']['adresse_site'];

				session_set('access_token');
				session_set('twitter_redirect');

				$GLOBALS['redirect'] = $url;
			}
		}
	}
}
?>