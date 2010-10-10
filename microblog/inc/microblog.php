<?php
/**
 * Plugin spip|microblog
 * (c) Fil 2009-2010
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
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
 */
function microblog($status, $user=null, $pass=null, $service=null, $api=null){
	$cfg = @unserialize($GLOBALS['meta']['microblog']);

	// Certains define prennent le pas sur le reste (mode TEST)
	if (defined('_TEST_MICROBLOG_SERVICE')) {
		if (_TEST_MICROBLOG_SERVICE == '') {
			spip_log('microblog desactive par _TEST_MICROBLOG_SERVICE');
			return false;
		}
		else
			$service = _TEST_MICROBLOG_SERVICE;
	}
	if (defined('_TEST_MICROBLOG_USER')) $user = _TEST_MICROBLOG_USER;
	if (defined('_TEST_MICROBLOG_PASS')) $pass = _TEST_MICROBLOG_PASS;


	// services connus
	$apis = array(
		'spipo' => 'http://%user%:%pass%@spip.org/api/statuses/update.xml',
		'identica' => 'http://%user%:%pass%@identi.ca/api/statuses/update.xml',
		'twitter' => 'http://twitter.com/statuses/update.xml',
		'supertweet' => 'http://%user%:%pass%@api.supertweet.net/statuses/update.xml'
	);

	// Choix de l'API
	if (!isset($api)) {
		if (!isset($service))
			$service = $cfg['service'];
		if (!isset($apis[$service]))
			return false;
		$api = $apis[$service];
	}
	
	/**
	 * Si l'API utilisée est twitter, on force le passage en oAuth
	 */
	if($service == 'twitter'){
		if(
			isset($cfg['twitter_consumer_key']) 
				&& isset($cfg['twitter_consumer_secret'])
				&& isset($cfg['twitter_token'])
				&& isset($cfg['twitter_token_secret'])){
			// Cas de twitter et oAuth
			include_spip('inc/twitteroauth');
			$consumer_key = $cfg['twitter_consumer_key'];
			$consumer_secret = $cfg['twitter_consumer_secret'];

			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $cfg['twitter_token'], $cfg['twitter_token_secret']);
			
			if($connection){
				$oAuth = true;
			}
			else{
				spip_log('Erreur de connexion à twitter, verifier la configuration','microblog');
				return false;
			}
		}
		else{
			spip_log('Erreur de connexion à twitter, verifier la configuration','microblog');
			return false;
		}
	}else{
		if (!isset($user))
			$user = $cfg['user'];
		if (!isset($pass))
			$pass = $cfg['pass'];

		// Inserer les credits d'authentification
		$api = str_replace(array('%user%','%pass%'), array(urlencode($user),urlencode($pass)), $api);
	}
	
	// Preparer le message (utf8 < 140 caracteres)
	include_spip('inc/charsets');
	$status = trim(preg_replace(',\s+,', ' ', $status));
	$status = unicode2charset(charset2unicode($status), 'utf-8');
	$status = substr($status, 0, 140);

	if (!strlen($status))
		return false;

	$datas = array('status' => $status);

	// anti-begaiment
	$begaie = md5("$service $user $status");
	if ($begaie == $GLOBALS['meta']['microblog_begaie']) {
		spip_log("begaie $service $user $status", 'microblog');
		return false;
	} else
		ecrire_meta('microblog_begaie', $begaie);

	// ping et renvoyer la reponse xml
	if($oAuth){
		$ret = 'ok';
		$api = 'statuses/update';
		$connection->post($api,$datas);
		if (200 != $connection->http_code){
			spip_log('Erreur '.$connection->http_code,'microblog');
			return false;
		}
	}else{
		include_spip('inc/distant');
		$ret = recuperer_page($api, false, false, null, $datas);
		spip_log("$service $user $status ".strlen($ret), 'microblog');
	}
	
	return $ret;
}
