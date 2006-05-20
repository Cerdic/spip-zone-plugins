<?php

/**
 * definition du plugin "dew player"
 */
	function dewplayer($url){
		static	$player = NULL;
		if ($player == NULL)
			$player = find_in_path('dewplayer.swf');
		$playa_ = "
<object type='application/x-shockwave-flash' 
data='$player?son=$url' width='200' height='20'>
<param name='movie' value='$player?son=$url'/>
</object>";
	
		return $playa_;
		
	}

	/* static public */
	function dewplayer_post_propre($quelquechose) {

		// ne rien faire = retourner ce qu'on nous a envoye
		
		$reg_formats="mp3";
		 
		//trouver des liens complets
		unset($matches) ;
		preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*>(.*)<\/a>/iU", $quelquechose, $matches);
		$url_a=$matches[1];

		if(!$matches[1][0]){
			//trouver des url relatives
			unset($matches) ;
			preg_match_all("/<a(.*)href=['\"]([a-zA-Z0-9 ()\/\._&%\?+'=~-]*\.($reg_formats))['\"](.*)[^>]*>(.*)<\/a>/iU", $quelquechose, $matches);
			$url_a=$matches[2];
		}
		if (is_array($url_a)){
			if ($player == NULL)
				$player = find_in_path('dewplayer.swf');
			foreach($url_a as $url){
				$quelquechose .= dewplayer($url);
			}
		}
		
		return $quelquechose;
	}

	
?>