<?php

/*****************************************************************\
 * spip|microblog
 *                      (c) Fil 2009
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
 * distribue sous licence GNU/LGPL
 *
 * Exemple :
 *   include_spip('inc/microblog');
 *   $res = microblog('Bonjour, monde', 'user', 'pass', 'spipo');
 *
\*****************************************************************/

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

	// services connus
	$apis = array(
		'spipo' => 'http://%user%:%pass%@spip.org/api/statuses/update.xml',
		'identica' => 'http://%user%:%pass%@identi.ca/api/statuses/update.xml',
		'twitter' => 'http://%user%:%pass%@twitter.com/statuses/update.xml'
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




/*
 * Buzzer les notifications
 */

function Microblog_notifications($x) {
  include_spip('inc/filtres_mini');
  include_spip('inc/texte');

	$status = null;
	$cfg = @unserialize($GLOBALS['meta']['microblog']);
	switch($x['args']['quoi']) {
		case 'forumposte':      // post forums
			if ($cfg['evt_forumposte']
			AND $id = intval($x['args']['id'])) {
				$url = url_absolue(generer_url_entite($id, 'forum'));
				$t = sql_fetsel('titre,texte', 'spip_forum', 'id_forum='.$id);
				$titre = couper(typo($t['titre'].' | '.$t['texte']),
					120 - strlen('#forum  ') - strlen($url));
				$status = "$titre #forum $url";
			}
			break;
        
		case 'instituerarticle':    // publier | proposer articles
        if ($id = intval($x['args']['id'])
			AND ( ($cfg['evt_publierarticles'] 
                AND $x['args']['options']['statut'] == 'publie') // publier
            OR ($cfg['evt_proposerarticles'] 
                AND $x['args']['options']['statut'] == 'prop' 
                AND $x['args']['options']['statut_ancien'] != 'publie') )  // proposer
        ) {
				$url = str_replace('amp;','',url_absolue(generer_url_entite($id, 'article')));
				$t = sql_fetsel('titre,descriptif,texte', 'spip_articles', 'id_article='.$id);
				$etat = str_replace(array('prop','publie'),
                array(_T('microblog:propose'),_T('microblog:publie')),
                $x['args']['options']['statut']);
            $titre = couper(typo($t['titre']
                    .' | '._T('microblog:article').' '.$etat
                    .' | '.($t['descriptif'] != '' ? $t['descriptif'].' | ' : '')
                    .$t['texte']),
					120 - strlen($url));
				$status = "$titre $url";
			}        
			break;
	}

	if (!is_null($status))
		microblog($status);

	if (!is_null($status_prive))
		microblog($status_prive, $user_prive, $pass_prive, $service_prive);

	return $x;
}

