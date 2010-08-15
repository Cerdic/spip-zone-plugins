<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function action_twidget_dist(){

	$url = $_SERVER['QUERY_STRING'];
	$url = preg_replace(",^action=twidget&w=,","",$url);
	$sd = "";
	if (strncmp($url,'search',6)==0)
		$sd = "search.";
	$url = "http://{$sd}twitter.com/".$url;

	echo twidget_get_cached_url($url);
	exit;
}

function twidget_get_cached_url($url,$force=true) {
	@define('_TWIDGET_CACHE',60);

	$hash = md5($url);
	$dir = sous_repertoire(_DIR_CACHE,"twidget");
	if (lire_fichier($f = "$dir/p$hash.txt", $c)
	  AND $c = unserialize($c)
		AND time()-$c['time']<_TWIDGET_CACHE)
		return $c['content'];

	// si job_queue, programmer la maj et renvoyer le contenu existant
	if (isset($c['content']) AND !$force AND function_exists('job_queue_add')) {
	 job_queue_add ('twidget_get_cached_url', 'Twitter Widget proxy', array($url,true), 'action/widget',true);
	 return $c['content'];
	}

	// mettre a jour le cache

	// recuperer le json
	include_spip('inc/distant');
	$contenu = recuperer_page($url);

	// intercepter, cacher et relocaliser les avatars
	preg_match_all(',"profile_image_url":"([^"]*)",Uims',$contenu,$regs,PREG_SET_ORDER);
	foreach($regs as $reg){
		$contenu = str_replace($reg[1],twidget_get_cached_avatar($reg[1]),$contenu);
	}

	ecrire_fichier($f, serialize(array('time'=>time(),'content'=>$contenu)));
	return $contenu;

}

function twidget_get_cached_avatar($img_url){
	@define('_TWIDGET_CACHE_AVATAR',24*3600);
	$parts = parse_url($img_url);

	$hash = md5($parts['path']);
	$ext = substr($parts['path'], strrpos($parts['path'], "."));

	$dir = sous_repertoire(_DIR_VAR,"twidget");
	$dir = sous_repertoire($dir,substr($hash,0,2));
	$f = $dir.$hash.$ext;

	if (file_exists($f) AND time()-filemtime($f)<_TWIDGET_CACHE_AVATAR)
		return $f;

	recuperer_page($img_url,$f);
	return $f;
}
?>