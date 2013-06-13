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
 * Envoyer un microblog sur une des plateformes disponibles
 * 
 * $status : ce qu'on veut ecrire
 * $user, $pass : identifiants
 * $service : quel service
 * $api : si on est vraiment desespere :-)
 * $tokens : dans le cas de oAuth chez twitter pouvoir passer des tokens différents 
 * de ceux de la conf générale du site
 */
if (!function_exists('microblog')){
function microblog($status, $user=null, $pass=null, $service=null, $api=null, $tokens=null){
	return tweet($status, $tokens);
}
}


/**
 * Envoyer un message sur Twitter
 * @param $status
 * @param null $tokens
 *   permet d'utiliser des tokens specifiques et pas ceux pre-configures
 *   (voir twitter_connect)
 * @return bool|string
 */
function tweet($status, $tokens = null){
	// Certains define prennent le pas sur le reste (mode TEST)
	if (defined('_TEST_MICROBLOG_SERVICE')) {
		if (_TEST_MICROBLOG_SERVICE == '') {
			spip_log('microblog desactive par _TEST_MICROBLOG_SERVICE');
			return false;
		}
	}

	/**
	 * Si l'API utilisée est twitter, on force le passage en oAuth
	 */
	$oAuthConnection = twitter_connect($tokens);

	// si pas d'api utilisable on sort
	if (!$oAuthConnection)
		return false;
	
	// Preparer le message (utf8 < 140 caracteres)
	include_spip('inc/charsets');
	$status = trim(preg_replace(',\s+,', ' ', $status));
	$status = unicode2charset(charset2unicode($status), 'utf-8');
	$status = substr($status, 0, 140);

	if (!strlen($status)) {
		spip_log('Rien a bloguer','twitter');
		return false;
	}

	$datas = array('status' => $status);

	// anti-begaiment
	$begaie = md5(serialize(array($tokens,$status)));
	if ($begaie == $GLOBALS['meta']['twitter_begaie']) {
		spip_log("begaie $status", 'twitter'._LOG_INFO_IMPORTANTE);
		return false;
	}

	// ping et renvoyer la reponse xml
	$ret = 'ok';
	$api = 'statuses/update';
	$oAuthConnection->post($api,$datas);
	if (200 != $oAuthConnection->http_code){
		spip_log('Erreur '.$oAuthConnection->http_code,'twitter');
		$ret = false;
	}

	// noter l'envoi pour ne pas twitter 2 fois de suite la meme chose
	if ($ret)
		ecrire_meta('twitter_begaie', $begaie);

	return $ret;
}

/**
 * @param null|array $tokens
 *   twitter_consumer_key : key de l'application a utiliser
 *   twitter_consumer_secret : secret de l'application a utiliser
 *
 *   twitter_account : pour utiliser un compte twitter pre-configure plutot que celui par defaut
 * ou
 *   twitter_token : token du compte a utiliser
 *   twitter_token_secret : token secret du compte a utiliser
 *
 *
 * @return bool|TwitterOAuth
 */
function twitter_connect($tokens=null){
	static $connection = null;

	$t = md5(serialize($tokens));

	if (!isset($connection[$t])){
		$cfg = @unserialize($GLOBALS['meta']['microblog']);

		$tokens = array_intersect($tokens,
			array(
				'twitter_consumer_key'=>'',
				'twitter_consumer_secret'=>'',
				'twitter_account'=>'',
				'twitter_token'=>'',
				'twitter_token_secret'=>'',
			));
		if(!is_array($tokens))
			$tokens = array();

		if (!isset($tokens['twitter_consumer_key']) OR !isset($tokens['twitter_consumer_secret'])){
			$tokens['twitter_consumer_key'] = $cfg['twitter_consumer_key'];
			$tokens['twitter_consumer_secret'] = $cfg['twitter_consumer_secret'];
		}
		if (!isset($tokens['twitter_token']) OR !isset($tokens['twitter_token_secret'])){
			$account = $cfg['default_account'];
			if (isset($tokens['twitter_account']) AND isset($cfg['twitter_accounts'][$tokens['twitter_account']]))
				$account = $tokens['twitter_account'];
			if (isset($cfg['twitter_accounts'][$account]))
				$account = reset(array_keys($cfg['twitter_accounts']));
			if (isset($cfg['twitter_accounts'][$account])){
				$tokens['twitter_token'] = $cfg['twitter_accounts'][$account]['token'];
				$tokens['twitter_token_secret'] = $cfg['twitter_accounts'][$account]['token_secret'];
			}
		}

		if(
			isset($tokens['twitter_consumer_key'])
				&& isset($tokens['twitter_consumer_secret'])
				&& isset($tokens['twitter_token'])
				&& isset($tokens['twitter_token_secret'])){
			// Cas de twitter et oAuth
			$t2 = md5(serialize($tokens));
			include_spip('inc/twitteroauth');
			$connection[$t] = $connection[$t2] = new TwitterOAuth(
				$tokens['twitter_consumer_key'],
				$tokens['twitter_consumer_secret'],
				$tokens['twitter_token'],
				$tokens['twitter_token_secret']);

			if(!$connection[$t2]) {
				spip_log('Erreur de connexion à twitter, verifier la configuration','twitter'._LOG_ERREUR);
				return false;
			}
		}
		else{
			spip_log('Erreur de connexion à twitter, verifier la configuration','twitter'._LOG_ERREUR);
			return false;
		}
	}
	return $connection[$t];
}
