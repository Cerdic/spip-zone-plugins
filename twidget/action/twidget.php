<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function action_twidget_dist(){

	$url = $_SERVER['QUERY_STRING'];
	$url = preg_replace(",^action=twidget&w=/?,","",$url);
	$sd = "api.";
	if (strncmp($url,'search',6)==0)
		$sd = "search.";
	$url = "http://{$sd}twitter.com/".$url;

	header("Content-type: text/javascript; charset=utf-8");
	$res = twidget_get_cached_url($url);
	// header Expires pour eviter overflow par le meme client
	header("Pragma: public");
	header("Cache-Control: maxage="._EXPIRES);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+_EXPIRES) . ' GMT');
	echo $res;

	exit;
}

function twidget_get_cached_url($url,$force=false) {
	@define('_TWIDGET_CACHE',60);
	$expires = _TWIDGET_CACHE;

	// enlever le param anti cache
	$url = preg_replace(",&\d+=cachebust,","",$url);
	if (_request('debug')){
		$force = true;
	}

	$hash = md5($url);
	$dir = sous_repertoire(_DIR_CACHE,"twidget");

	$f = "$dir"."p$hash.txt";
	if (!$force
	  AND lire_fichier($f, $c)
	  AND $c = unserialize($c)
	  AND isset($c['content'])
	  // AND $contenu = trim($c['content'])
	  AND time()-$c['time']<_TWIDGET_CACHE){
		if (!defined('_EXPIRES')) define('_EXPIRES',$expires);
		return $c['content'];
	}

	spip_log("cache $f invalide (force $force)","twitget");
	// si job_queue, programmer la maj et renvoyer le contenu existant
	if (!$force
	  // AND isset($c['content'])
	  // AND $contenu = trim($c['content'])
	  AND function_exists('job_queue_add')) {
		job_queue_add ('twidget_get_cached_url', 'Twitter Widget proxy', array($url,true), 'action/twidget',true);
		if (!defined('_EXPIRES')) define('_EXPIRES',$expires);
		return $c['content'];
	}


	spip_log("requete api twitter $url","twitget");
	// mettre a jour le cache

	// recuperer le json
	if (!defined('_INC_DISTANT_USER_AGENT'))
		define('_INC_DISTANT_USER_AGENT',  $_SERVER['HTTP_USER_AGENT']);

	include_spip('inc/distant');
	$contenu = recuperer_page($url);

	if (_request('debug')){
		var_dump($url);
		var_dump($contenu);
		if (!$contenu)
			var_dump(recuperer_page($url, false, true));
	}

	$contenu = trim($contenu);

	// ne pas cacher une requete qui echoue (twitter fail)
	if ($contenu
	  OR !isset($c['content'])
	  OR !strlen(trim($c['content']))){
   	// intercepter, cacher et relocaliser les avatars
		preg_match_all(',"profile_image_url":"([^"]*)",Uims',$contenu,$regs,PREG_SET_ORDER);
		foreach($regs as $reg){
			$new = twidget_get_cached_avatar($reg[1]);
			$contenu = str_replace($reg[1],$new,$contenu);
		}

		ecrire_fichier($f, serialize(array('time'=>time(),'content'=>$contenu)));
	}

	// par defaut expire sur une minute pour limiter le rate
	// si contenu vide envoyer un expire 15min car on est sans doute
	// en limitation d'API
	if (!$contenu) {
		$expires = 15*60;
	}

	// header Expires pour eviter overflow par le meme client
	if (!defined('_EXPIRES')) define('_EXPIRES',$expires);
	return $contenu;

}

function twidget_get_cached_avatar($img_url){

	$img_url = str_replace('\/','/',$img_url);
	
	@define('_TWIDGET_CACHE_AVATAR',24*3600);
	$parts = parse_url($img_url);

	$hash = md5($parts['path']);
	$ext=".jpg";
	if ($p = strrpos($parts['path'], "."))
		$ext = substr($parts['path'], $p);

	$dir = sous_repertoire(_DIR_VAR,"twidget");
	$dir = sous_repertoire($dir,substr($hash,0,2));
	$f = $dir.$hash.$ext;

	if (file_exists($f) AND time()-filemtime($f)<_TWIDGET_CACHE_AVATAR)
		return $f;

	recuperer_page($img_url,$f);
	return $f;
}
?>
