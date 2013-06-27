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
function action_twitter_oauth_authorize_dist(){

	include_spip('inc/twitteroauth');
	include_spip('inc/session');

	$redirect = session_get('twitter_redirect') ? session_get('twitter_redirect') : $GLOBALS['meta']['url_site_spip'];
	if (isset($GLOBALS['visiteur_session']['oauth_token'])
		AND $GLOBALS['visiteur_session']['oauth_token']){

		if(_request('denied')){
			spip_log("action_twitter_oauth_authorize_dist : denied",'twitter'._LOG_ERREUR);
			$redirect = parametre_url($redirect,'erreur','auth_denied','&');
			session_set('oauth_status','denied');
			$GLOBALS['redirect'] = $redirect;
		}
		elseif (_request('oauth_token') && ($GLOBALS['visiteur_session']['oauth_token'] !== _request('oauth_token'))) {
			spip_log("action_twitter_oauth_authorize_dist : old_token",'twitter'._LOG_ERREUR);
			$redirect = parametre_url($redirect,'erreur','old_token','&');
			session_set('oauth_status','oldtoken');
			$GLOBALS['redirect'] = $redirect;
		}
		else {
			$cfg = @unserialize($GLOBALS['meta']['microblog']);
			$consumer_key = $cfg['twitter_consumer_key'];
			$consumer_secret = $cfg['twitter_consumer_secret'];

			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $GLOBALS['visiteur_session']['oauth_token'], $GLOBALS['visiteur_session']['oauth_token_secret']);
			$access_token = $connection->getAccessToken(_request('oauth_verifier'));
			session_set('access_token',$access_token);

			/**
			 * Si le code de retour est 200 :
			 * L'utilisateur a été vérifié et
			 * les tokens d'accès peuvent être sauvegardés pour un usage futur
			 * on appelle la callback en session qui en fait ce qu'elle veut
			 */
			if (200 == $connection->http_code) {

				if ($callback = session_get('twitter_callback')
				  AND $callback = charger_fonction($callback,"action",true)){
					// si la callback retourne quelque chose c'est une url de redirect
					if ($r = $callback(true, $redirect))
						$redirect = $r;
				}

				$GLOBALS['redirect'] = $redirect;
			}
			else {
				spip_log("Erreur '".$connection->http_code."' au retour pour recuperation des tokens dans action_twitter_oauth_callback_dist",'twitter'._LOG_ERREUR);
				$GLOBALS['redirect'] = $redirect;
			}
		}
	}
	else {
		// rien a faire ici !
		$GLOBALS['redirect'] = $redirect;
	}

	// vider la session
	foreach(array('access_token','oauth_token','oauth_token_secret','twitter_redirect','twitter_callback') as $k)
		if (isset($GLOBALS['visiteur_session'][$k]))
			session_set($k);
}

function twitter_oauth_authorize($callback, $redirect, $sign_in=true){
	$cfg = @unserialize($GLOBALS['meta']['microblog']);

	$redirect = parametre_url(parametre_url($redirect,'erreur_code',''),'erreur','','&');

	include_spip('inc/filtres');
	include_spip('inc/twitteroauth');
	include_spip('inc/session');

	/**
	 * L'URL de callback qui sera utilisée suite à la validation chez twitter
	 * Elle vérifiera le retour et finira la configuration
	 */
	$oauth_callback = url_absolue(generer_url_action('twitter_oauth_authorize','',true));

	/**
	 * Récupération des tokens depuis twitter par rapport à notre application
	 * On les place dans la session de l'individu en cours
	 * Ainsi que l'adresse de redirection pour la seconde action
	 */
	try {
		$connection = new TwitterOAuth($cfg['twitter_consumer_key'], $cfg['twitter_consumer_secret']);
		$request_token = $connection->getRequestToken($oauth_callback);
		$token = $request_token['oauth_token'];
		session_set('oauth_token',$token);
		session_set('oauth_token_secret',$request_token['oauth_token_secret']);
		session_set('twitter_redirect',str_replace('&amp;','&',$redirect));
		session_set('twitter_callback',$callback);

		/**
		 * Vérification du code de retour
		 */
		switch ($code = $connection->http_code) {
			/**
			 * Si le code de retour est 200 (ok)
			 * On envoie l'utilisateur vers l'url d'autorisation
			 */
			case 200:
				$url = $connection->getAuthorizeURL($token, $sign_in);
				include_spip('inc/headers');
				$GLOBALS['redirect'] = $url;
				#echo redirige_formulaire($url);
				break;
			/**
			 * Sinon on le renvoie vers le redirect avec une erreur
			 */
			default:
				spip_log('Erreur connexion twitter','twitter'._LOG_ERREUR);
				spip_log($connection, 'twitter'._LOG_ERREUR);
				$redirect = parametre_url($redirect,'erreur_code',$code);
				$redirect = parametre_url($redirect,'erreur','erreur_conf_app','&');
				$GLOBALS['redirect'] = $redirect;
				break;
		}
	}
	catch(Exception $e){
		session_set('oauth_erreur_message',$e->getMessage());
		$redirect = parametre_url($redirect,'erreur',"erreur_oauth",'&');
		$GLOBALS['redirect'] = $redirect;
	}
}
?>