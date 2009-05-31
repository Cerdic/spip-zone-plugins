<?php

/**
 * definition du plugin "flash_gordon" version "classe statique"
 */

	/* static public */
	function flash_gordon_flash_oo($quelquechose) {
		// ne rien faire = retourner ce qu'on nous a envoye
		
		$reg_formats="mp3";
		 
	//trouver des liens complets
	unset($matches) ;
	preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*>(.*)<\/a>/iU", $quelquechose, $matches);
$url=$matches[1][0];

if(!$matches[1][0]){
//trouver des url relatives
	unset($matches) ;
	preg_match_all("/<a(.*)href=['\"]([a-zA-Z0-9 ()\/\._&%\?+'=~-]*\.($reg_formats))['\"](.*)[^>]*>(.*)<\/a>/iU", $quelquechose, $matches);
$url=$matches[2][0];
}

		
if($url) $playa_ = '
		<div style="margin-top:10px;clear:both"><br />
<object  type="application/x-shockwave-flash" data="audio-player/player.swf" width="290" height="24" id="audioplayer1">
<param name="movie" value="audio-player/player.swf" />
<param name="FlashVars" value="playerID=1&amp;bg=0xf8f8f8&amp;leftbg=0xeeeeee&amp;lefticon=0x666666&amp;rightbg=0xcccccc&amp;rightbghover=0x999999&amp;righticon=0x666666&amp;righticonhover=0xFFFFFF&amp;text=0x666666&amp;slider=0x666666&amp;track=0xFFFFFF&amp;border=0x666666&amp;loader=0x9FFFB8&amp;soundFile='.$url.'" />
<param name="quality" value="high" />
<param name="menu" value="false" />
<param name="wmode" value="transparent" />
</object>
</div> ' ;
		
		return $quelquechose.$playa_.'<!-- flash_gordon -->';
	}

	
?>