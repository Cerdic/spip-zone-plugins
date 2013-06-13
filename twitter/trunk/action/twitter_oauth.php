<?php
/**
 * Plugin spip|microblog
 * (c) Fil 2009-2010
 *
 * Envoyer des micromessages depuis SPIP vers twitter ou laconica
 * Distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction vérifiant le retour de twitter 
 * Elle met dans la configuration du plugin les tokens 
 * nécessaires pour de futures connexions
 */
function action_twitter_oauth_dist(){
	global $visiteur_session;
	
	if($visiteur_session['statut']){
		include_spip('inc/twitteroauth');
		include_spip('inc/session');
		$cfg = @unserialize($GLOBALS['meta']['microblog']);
		
		if (_request('oauth_token') && ($visiteur_session['oauth_token'] !== _request('oauth_token'))) {
			session_set('status','oldtoken');
			$url = session_get('twitter_redirect') ? session_get('twitter_redirect') : $GLOBALS['meta']['url_site_spip'];
			redirige_formulaire($url);
		}

		$consumer_key = $cfg['twitter_consumer_key'];
		$consumer_secret = $cfg['twitter_consumer_secret'];

		$connection = new TwitterOAuth($consumer_key, $consumer_secret, $visiteur_session['oauth_token'], $visiteur_session['oauth_token_secret']);
		$access_token = $connection->getAccessToken(_request('oauth_verifier'));
		session_set('access_token',$access_token);
		
		/**
		 * Si le code de retour est 200 :
		 * L'utilisateur a été vérifié et les tokens d'accès peuvent être 
		 * sauvegardés pour un usage futur
		 */
		if (200 == $connection->http_code) {
			
			$cfg['twitter_token'] = $visiteur_session['access_token']['oauth_token'];
			$cfg['twitter_token_secret'] = $visiteur_session['access_token']['oauth_token_secret'];
			ecrire_meta("microblog", serialize($cfg));
			
			$url = session_get('twitter_redirect') ? session_get('twitter_redirect') : $GLOBALS['meta']['adresse_site'];
			
			session_set('access_token','');
			session_set('twitter_redirect','');
			
			redirige_formulaire($url);
		}
	}
}
?>