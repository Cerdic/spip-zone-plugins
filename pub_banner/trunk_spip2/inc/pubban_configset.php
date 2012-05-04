<?php
/**
 * @name 		Configuration
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */

/**
 * Declaration de configuration par defaut
 */
$GLOBALS['_PUBBAN_CONF'] = array(
	'server' 		=> '',
	'table_pub' 	=> 'publicites',
	'table_empl' 	=> 'bannieres',
	'table_stats' 	=> 'pubban_stats',
	'table_join'	=> 'bannieres_publicites',
);

/**
 * Chargement de la config courante
 */
if(isset($GLOBALS['meta']['pubban_config']))
	$config = unserialize($GLOBALS['meta']['pubban_config']);
else $config = $GLOBALS['_PUBBAN_CONF'];

// -------------------------
// Process
// -------------------------

function pubban_recuperer_statut($url){	
	include_spip('inc/distant');
	// ouvrir la connexion et envoyer la requete et ses en-tetes
	list($f, $fopen) = init_http('GET', $url, false, '', '');
	if (!$f) return false;

	// Sauf en fopen, envoyer le flux d'entree
	// et recuperer les en-tetes de reponses
	if ($fopen)
		$status = '';
	else {
		$s = @trim(fgets($f, 16384));
		if (!preg_match(',^HTTP/[0-9]+\.[0-9]+ ([0-9]+),', $s, $r)) {
			return 0;
		}
		$status = intval($r[1]);
	}
	return $status;
}

function pubban_enregistrer_config($args){
	$mess = array();
	if(!is_array($args)) return;
	$_conf = pubban_recuperer_config();

	if($args['adds_ok']=='oui' AND strlen($args['adds_squelette'])){
		$url_adds_skel = $GLOBALS['meta']['adresse_site'].'/?page='
			.substr($args['adds_squelette'], 0, strpos($args['adds_squelette'], '.'));
		$stat_adds_skel = pubban_recuperer_statut($url_adds_skel);
		if(substr($stat_adds_skel,0,1) != '2') 
			$mess['error'] = _T('pubban:erreur_url_statut', array('url'=>$url_adds_skel, 'statut'=>$stat_adds_skel));
		else $_conf['adds_squelette'] = $url_adds_skel;
	}
	if(strlen($args['adds_commande'])){
		$url_adds_comm = $GLOBALS['meta']['adresse_site'].'/?page='
			.substr($args['adds_commande'], 0, strrpos($args['adds_commande'], '.'));
		$stat_adds_comm = pubban_recuperer_statut($url_adds_comm);
		if(substr($stat_adds_comm,0,1) != '2') 
			$mess['error'] = _T('pubban:erreur_url_statut', array('url'=>$url_adds_comm, 'statut'=>$stat_adds_comm));
		else $_conf['adds_commande'] = $url_adds_comm;
	}
	$conf = array_merge($_conf, $args);
	include_spip('inc/meta');
	ecrire_meta('pubban_config', serialize($conf), 'non');
	ecrire_metas();
	$mess['ok'] = _T('pubban:config_ok');
	return $mess;
}

function pubban_recuperer_config($str=''){
	$_conf = isset($GLOBALS['meta']['pubban_config']) ? array_merge($GLOBALS['_PUBBAN_CONF']['config'], unserialize($GLOBALS['meta']['pubban_config'])) : $GLOBALS['_PUBBAN_CONF']['config'];
	if(strlen($str)) {
		if(isset($_conf[$str])) return $_conf[$str];
		return false;
	}
	return $_conf;
}

function pubban_boite_info(){
	include_spip('inc/plugin');
	// Compat SPIP 2.0=>2.1
	if (function_exists('plugin_get_infos')) {
		$infos = plugin_get_infos(_DIR_PLUGIN_PUBBAN);
	} else {
		$get_infos = charger_fonction('get_infos','plugins');
		$infos = $get_infos(_DIR_PLUGIN_PUBBAN);
	}
	$infos['revision'] = version_svn_courante (_DIR_PLUGIN_PUBBAN);
	return(
		debut_boite_info(true)
		. "<div class='verdana1 spip_xx-small' style='text-align: left; font-size: 0.8em;'>"
		. "<center><big><strong>"._T('pubban:pubban').' '.$infos['version']."</strong>"
		. "<br /><i>"._T('pubban:plugin_spip')."</i></big><br /><img src='".url_absolue(find_in_path('img/ico-pubban.png'))."' border='0' style='margin-top:1em' /></center>"
		. "<p><b>"
		. _T('pubban:num_version_base')." : ".$infos['version_base']
		. "<br />"._T('pubban:num_version_svn')." : ".$infos['revision']
		. "</b></p>"
		. "<p><b>"._T('pubban:documentation_info')." :</b><br /><a href='"._PUBBAN_URL."'>"._PUBBAN_URL."</a></p>"
		. "<p><b>"._T('pubban:url_update')." :</b><br /><a href='"._PUBBAN_UPDATE."'>"._PUBBAN_UPDATE."</a></p>"
		. "<p><b>"._T('pubban:url_traceur')." :</b><br /><a href='"._PUBBAN_TRAC."'>"._PUBBAN_TRAC."</a></p>"
		. "</div>".fin_boite_info(true)
	);
}

?>