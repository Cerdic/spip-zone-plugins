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

		if(!is_array($tokens))
			$tokens = array();

		$tokens = array_intersect_key($tokens,
			array(
				'twitter_consumer_key'=>'',
				'twitter_consumer_secret'=>'',
				'twitter_account'=>'',
				'twitter_token'=>'',
				'twitter_token_secret'=>'',
			));


		if (!isset($tokens['twitter_consumer_key']) OR !isset($tokens['twitter_consumer_secret'])){
			$tokens['twitter_consumer_key'] = $cfg['twitter_consumer_key'];
			$tokens['twitter_consumer_secret'] = $cfg['twitter_consumer_secret'];
		}
		if (!isset($tokens['twitter_token']) OR !isset($tokens['twitter_token_secret'])){
			$account = $cfg['default_account'];
			if (isset($tokens['twitter_account']) AND isset($cfg['twitter_accounts'][$tokens['twitter_account']]))
				$account = $tokens['twitter_account'];

			if (!isset($cfg['twitter_accounts'][$account]))
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


/**
 * Fonction d'utilisation simple de l'API twitter oAuth
 *
 * @param $command string : la commande à passer
 * @param $type string : le type de commande (get/post/delete)
 * @param $params array : les paramètres dans un array de la commande
 * @param array $options
 *   string return_type : le retour souhaité par défaut cela renverra la chaine ou l'array retourné par la commande.
 *                        Sinon on peut utiliser les valeurs http_code,http_info,url
 *
 *   twitter_consumer_key : key de l'application a utiliser
 *   twitter_consumer_secret : secret de l'application a utiliser
 *
 *   twitter_account : pour utiliser un compte twitter pre-configure plutot que celui par defaut
 * ou
 *   twitter_token : token du compte a utiliser
 *   twitter_token_secret : token secret du compte a utiliser
 * @return bool|string|array
 */
function twitter_api_call($command,$type='get',$params=array(),$options=null){
	include_spip('inc/microblog');
	if (!$connection = twitter_connect($options))
		return false;

	switch($type){
		case 'get':
			$content = $connection->get($command,$params);
			break;
		case 'post':
			$content = $connection->post($command,$params);
			break;
		case 'delete':
			$content = $connection->delete($command,$params);
			break;
		default:
			$content = $connection->get($command,$params);
	}

	$retour = isset($options['return_type'])?$options['return_type']:'';
	switch($retour){
		case 'http_code':
			return $connection->http_code;
		case 'http_info':
			return $connection->http_info;
		case 'url':
			return $connection->url;
		default:
			if (!is_string($content) AND is_array($content)) {
				// recopie ?
				$contents = array();
				foreach($content as $key => $val){
					$contents[$key] = $val;
				}
				return $contents;
			}
			else{
				return $content;
			}

	}
}