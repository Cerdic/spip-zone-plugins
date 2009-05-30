<?php

/**
 * definition du plugin "flvplayer"
 */

function flvplayer($url, $width=320, $height=240){
	static	$player = NULL;
	$url = urlencode(url_absolue($url));
	if ($player == NULL)
		$player = find_in_path('flvplayer.swf');
	$playa_ = "
<object type='application/x-shockwave-flash' width='$width' height='$height' 
wmode='transparent' data='$player?file=$url&autoStart=false'>
<param name='movie' value='$player?file=$url&autoStart=false' />
<param name='wmode' value='transparent' />
</object>";
	return $playa_;
}

function flvplayer_post_propre($texte) {
	
	$reg_formats="flv";
	 
	//trouver des liens complets
	unset($matches) ;
	preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*>(.*)<\/a>/iU", $texte, $matches);
	$url_a=$matches[1];

	if(!$matches[1][0]){
		//trouver des url relatives
		unset($matches) ;
		preg_match_all("/<a(.*)href=['\"]([a-zA-Z0-9 ()\/\._&%\?+'=~-]*\.($reg_formats))['\"](.*)[^>]*>(.*)<\/a>/iU", $texte, $matches);
		$url_a=$matches[2];
	}
	if (is_array($url_a)){
		if ($player == NULL)
			$player = find_in_path('flvplayer.swf');
		foreach($url_a as $url){
			$texte .= flvplayer($url);
		}
	}
	
	return $texte;
}

	
?>