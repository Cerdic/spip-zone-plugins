<?php
/*
 * Plugin spip|microblog
 * (c) Fil 2009-2010
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/*
 * Envoyer un microblog sur une des plateformes disponibles
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
		'twitter' => 'http://%user%:%pass%@twitter.com/statuses/update.xml',
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

	if (!isset($user))
		$user = $cfg['user'];
	if (!isset($pass))
		$pass = $cfg['pass'];

	// Inserer les credits d'authentification
	$api = str_replace(array('%user%','%pass%'), array(urlencode($user),urlencode($pass)), $api);

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
	include_spip('inc/distant');
	$ret = recuperer_page($api, false, false, null, $datas);
	spip_log("$service $user $status ".strlen($ret), 'microblog');
	return $ret;
}


function microblog_affiche_droite($flux){
	if (autoriser('microbloguer','status')
	AND isset($GLOBALS['meta']['microblog'])
	AND is_array($cfg = @unserialize($GLOBALS['meta']['microblog']))
	AND $cfg['invite']) {
		$flux['data'] .= recuperer_fond('modeles/microblog_update',array());
	}
	return $flux;
}