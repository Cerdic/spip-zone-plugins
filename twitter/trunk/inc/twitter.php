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

if (!defined("_TWITTER_API_CALL_MICROCACHE_DELAY")) define("_TWITTER_API_CALL_MICROCACHE_DELAY",180);

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
			spip_log('microblog desactive par _TEST_MICROBLOG_SERVICE',"twitter"._LOG_INFO_IMPORTANTE);
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
		$tokens = twitter_tokens($tokens);

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
 * Determiner les tokens de connexion en fonction de ceux passes
 * et de la configuration par defaut
 *
 * @param array $tokens
 * @return array
 */
function twitter_tokens($tokens){
	$cfg = @unserialize($GLOBALS['meta']['microblog']);

	if(!is_array($tokens))
		$tokens = array();

	$t = array_intersect_key($tokens,
		array(
			'twitter_consumer_key'=>'',
			'twitter_consumer_secret'=>'',
			'twitter_account'=>'',
			'twitter_token'=>'',
			'twitter_token_secret'=>'',
		));

	if (!isset($t['twitter_consumer_key']) OR !isset($t['twitter_consumer_secret'])){
		$t['twitter_consumer_key'] = $cfg['twitter_consumer_key'];
		$t['twitter_consumer_secret'] = $cfg['twitter_consumer_secret'];
	}

	if (!isset($t['twitter_token']) OR !isset($t['twitter_token_secret'])){
		$account = $cfg['default_account'];
		if (isset($t['twitter_account']) AND isset($cfg['twitter_accounts'][$t['twitter_account']]))
			$account = $t['twitter_account'];

		if (!isset($cfg['twitter_accounts'][$account]))
			$account = reset(array_keys($cfg['twitter_accounts']));

		if (isset($cfg['twitter_accounts'][$account])){
			$t['twitter_token'] = $cfg['twitter_accounts'][$account]['token'];
			$t['twitter_token_secret'] = $cfg['twitter_accounts'][$account]['token_secret'];
		}
	}
	return $t;
}

/**
 * Fonction d'utilisation simple de l'API twitter oAuth
 *
 * @param $command string : la commande à passer
 * @param $type string : le type de commande (get/post/delete)
 * @param $params array : les paramètres dans un array de la commande
 * @param array $options
 *   bool force : true pour forcer la requete hors cache
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

	// api_call en cache ?
	$cache_key = null;
	if ($type !== 'get'
		OR (isset($options['force']) AND $options['force'])
		OR !include_spip("inc/memoization")
	  OR !function_exists("cache_get")
	  OR !$t = twitter_tokens($options)
	  OR !$cache_key = "twitter_api_call-".md5(serialize(array($command,$params,$t)))
	  OR !$res = cache_get($cache_key)
	  OR $res['time']+_TWITTER_API_CALL_MICROCACHE_DELAY<$_SERVER['REQUEST_TIME']){

		if ($connection = twitter_connect($options)){

			$res = array();
			switch($type){
				case 'post':
					$res['content'] = $connection->post($command,$params);
					break;
				case 'delete':
					$res['content'] = $connection->delete($command,$params);
					break;
				case 'get':
				default:
					$res['content'] = $connection->get($command,$params);
					break;
			}
			$res['http_code'] = $connection->http_code;
			$res['http_info'] = $connection->http_info;
			$res['url'] = $connection->url;
			$res['time'] = $_SERVER['REQUEST_TIME'];

			if ($cache_key)
				cache_set($cache_key,$res,_TWITTER_API_CALL_MICROCACHE_DELAY*2);
		}
		else {
			if (!$res)
				return false;
			spip_log("twitter_api_call:$command echec connexion, on utilise le cache perime","twitter".LOG_INFO_IMPORTANTE);
		}
	}

	$retour = isset($options['return_type'])?$options['return_type']:'content';
	if (!isset($res[$retour]))
		$retour = 'content';

	switch($retour){
		default:
			return $res[$retour];
		case 'content':
			if (!is_string($res['content']) AND is_array($res['content'])) {
				// recopie ?
				$copy = array();
				foreach($res['content'] as $key => $val){
					$copy[$key] = $val;
				}
				return $copy;
			}
			return $res['content'];
			break;
	}
}