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
function microblog($status, $user=null, $pass=null, $service=null, $api=null, $tokens=null){
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
		'identica' => 'http://%user%:%pass%@identi.ca/api/statuses/update.xml',
		'twitter' => 'oAuth',
	);

	// Choix de l'API
	if (!isset($api)) {
		if (!isset($service))
			$service = $cfg['service'];
		if (!isset($apis[$service])){
			spip_log("Aucune API disponible pour $service",'microblog');
			return false;
		}
		$api = $apis[$service];
	}
	/**
	 * Si l'API utilisée est twitter, on force le passage en oAuth
	 */
	$oAuthConnection = null;
	if($api == 'oAuth'){
		$api = false;
		$oAuthConnection = twitter_connect($tokens);
	}
	else {
		if (!isset($user))
			$user = $cfg['user'];
		if (!isset($pass))
			$pass = $cfg['pass'];

		// Inserer les credits d'authentification
		$api = str_replace(array('%user%','%pass%'), array(urlencode($user),urlencode($pass)), $api);
	}

	// si pas d'api utilisable on sort
	if (!$api AND !$oAuthConnection)
		return false;
	
	// Preparer le message (utf8 < 140 caracteres)
	include_spip('inc/charsets');
	$status = trim(preg_replace(',\s+,', ' ', $status));
	$status = unicode2charset(charset2unicode($status), 'utf-8');
	$status = substr($status, 0, 140);

	if (!strlen($status)) {
		spip_log('Rien a bloguer','microblog');
		return false;
	}

	$datas = array('status' => $status);

	// anti-begaiment
	$begaie = md5("$service $user $status");
	if ($begaie == $GLOBALS['meta']['microblog_begaie']) {
		spip_log("begaie $service $user $status", 'microblog');
		return false;
	}

	// ping et renvoyer la reponse xml
	if($oAuthConnection){
		$ret = 'ok';
		$api = 'statuses/update';
		$oAuthConnection->post($api,$datas);
		if (200 != $oAuthConnection->http_code){
			spip_log('Erreur '.$oAuthConnection->http_code,'microblog');
			$ret = false;
		}
	}
	elseif ($api) {
		include_spip('inc/distant');
		$ret = recuperer_page($api, false, false, null, $datas);
		spip_log("$service $user $status ".strlen($ret), 'microblog');
	}

	// noter l'envoi pour ne pas twitter 2 fois de suite la meme chose
	if ($ret)
		ecrire_meta('microblog_begaie', $begaie);

	return $ret;
}

function twitter_connect($tokens=null){
	static $connection = null;

	if (!$connection){
		$cfg = @unserialize($GLOBALS['meta']['microblog']);
		if(is_array($tokens)){
			$cfg = array_merge($cfg,$tokens);
		}
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

			if(!$connection) {
				spip_log('Erreur de connexion à twitter, verifier la configuration','microblog');
				return false;
			}
		}
		else{
			spip_log('Erreur de connexion à twitter, verifier la configuration','microblog');
			return false;
		}
	}
	return $connection;
}

/**
 * Affichage du formulaire de microblog
 *
 * @param array $flux
 * @return array
 */
function microblog_affiche_milieu($flux){
	if ($exec = $flux['args']['exec']
		AND // SPIP 3
				((include_spip('base/objets')
				AND function_exists('trouver_objet_exec')
				AND $e = trouver_objet_exec($exec)
				AND $e['type']=='article'
				AND $e['edition']!==true
				AND $id_article = $flux['args']['id_article']
				AND include_spip('inc/config')
				AND $cfg = lire_config('microblog')
		    )
			OR // SPIP 2.x
				($exec=='articles'
				AND $id_article = $flux['args']['id_article']
				AND $cfg = @unserialize($GLOBALS['meta']['microblog'])
				)
			)
		AND
			($cfg['evt_publierarticles'] OR $cfg['evt_proposerarticles'])
		AND $cfg['invite']
		){
		$deplie = false;
		$ids = 'formulaire_editer_microblog-article-' . $id_article;
		include_spip("inc/presentation"); // bouton_block_depliable et al non dispo en SPIP 3 sinon
		$bouton = bouton_block_depliable(_T('twitter:titre_microblog'), $deplie, $ids);
		$out = debut_cadre('e', find_in_path('microblog-24.gif','themes/spip/images/'),'',$bouton, '', '', true);
		$out .= recuperer_fond('prive/editer/microblog', array_merge($_GET, array('objet'=>'article','id_objet'=>$id_article)));
		$out .= fin_cadre();
		if ($p = strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$out,$p,0);
		else
			$flux['data'] .= $out;
	}

	return $flux;
}