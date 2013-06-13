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

	if (!_request('debug'))
		header("Content-type: text/javascript; charset=utf-8");

	$res = twidget_get_cached_url($url);
	// header Expires pour eviter overflow par le meme client
	header("Pragma: public");
	header("Cache-Control: maxage="._EXPIRES);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+_EXPIRES) . ' GMT');

	// toujours renvoyer un JSON acceptable, pour declencher la callback notamment :
	if (!strlen(trim($res))){
		$res = "[]";
		if ($c=_request('callback'))
			$res = "$c($res)";
		$res.=";";
	}
	echo $res;

	exit;
}

function twidget_get_cached_url($url,$force=false) {
	// enlever le param anti cache
	$url = preg_replace(",&\d+=cachebust,","",$url);

	@define('_TWIDGET_CACHE',180);
	$expires = _TWIDGET_CACHE;

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


	$surl = explode("?",$url);
	$command = array_shift($surl);
	$surl = implode("?",$surl);
	parse_str($surl,$query);

	// reaffecter API v1 sur API v1.1
	$command_v11 = $command;
	$callback = "";
	switch($command){
		case "search.json":
			$command_v11 = "search/tweets";
			if (isset($query['rpp'])){
				$query['count'] = $query['rpp'];
				unset($query['rpp']);
			}
			break;
		default:
			$command_v11 = preg_replace(",^1/|\.json$,","",$command);
			break;
	}
	unset($query['clientsource']);
	if (isset($query['callback'])){
		$callback = $query['callback'];
		unset($query['callback']);
	}

	include_spip("microblog_fonctions");

	$res = microblog_twitter_api($command_v11,"get",$query);

	if (_request('debug')){
		var_dump($url);
		var_dump(array($command_v11,$query));
		var_dump($res);
	}

	// redresser la sortie API v1.1 => API v1
	switch($command){
		case "search.json":
			include_spip("inc/json");
			foreach(array('completed_in','max_id','max_id_str','query') as $k)
			$res[$k] = $res['search_metadata'][$k];
			$res['results'] = $res['statuses'];
			foreach($res['results'] as $k=>$r){
				$res['results'][$k]['from_user'] = $r['user']['screen_name'];
				$res['results'][$k]['profile_image_url'] = $r['user']['profile_image_url'];
			}
			unset($res['statuses']);
			$res = json_encode($res);
			break;
		default:
			$res = json_encode($res);
			break;
	}

	if ($callback)
		$res = $callback."(".$res.");";

	$contenu = trim($res);

	// ne pas cacher une requete qui echoue (twitter fail)
	if ($contenu
	  OR !isset($c['content'])
	  OR !strlen(trim($c['content']))){
   	// intercepter, cacher et relocaliser les avatars
		preg_match_all(',"profile_image_url":"([^"]*)",Uims',$contenu,$regs,PREG_SET_ORDER);
		foreach($regs as $reg){
			if (_request('debug')){
				var_dump($reg);
			}
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
	$f_rac = $f;
	if (test_espace_prive())
		$f_rac = substr($f,strlen(_DIR_RACINE));

	if (file_exists($f) AND time()-filemtime($f)<_TWIDGET_CACHE_AVATAR)
		return $f_rac;

	if (!function_exists("recuperer_page"))
		include_spip("inc/distant");
	recuperer_page($img_url,$f);
	return $f_rac;
}
?>
