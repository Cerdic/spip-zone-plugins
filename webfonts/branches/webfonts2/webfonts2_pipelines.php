<?php
/*
 * Plugin Webfonts2
 * (c) 2016
 * Distribue sous licence GPL
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans la pipeline des polices configurées

*/
function webfonts2_fonts_list($fonts){
	$webfonts = lire_config('webfonts2/webfonts');
	// enlever la dernière virgule
	$webfonts = explode(',',rtrim($webfonts,', '));
	
	foreach($webfonts as $font){
		$set =  explode(':',$font);
		$fonts[] = ['family'=> trim($set[0]),'variants'=> [ $set[1] ]];
	}
	
	return $fonts;
}

function webfonts2_header_prive($flux){
	
    $flux .= '<script src="'._DIR_PLUGIN_WEBFONTS2.'javascript/webfonts2.js'.'" type="text/javascript"></script>';
    return $flux;

}
 /**
  * webfonts_insert_head_css
 */
function webfonts2_insert_head_css($flux){
	static $done = false;
	if (!$done){
		$webfonts = lister_webfonts();		
		if(is_array($webfonts)){
			(defined('_FONTS_SUBSETS')) ? $subsets = _FONTS_SUBSETS : $subsets='' ;
			$font_request = googlefont_request($webfonts,$subsets);
			if (strlen($font_request)) {
				$methode = lire_config('webfonts2');
				if($methode == 'at_import'){
					$code = "<style>@import url('$font_request');</style>";
				}else{
					$code = '<link rel="stylesheet" type="text/css" href="'.$font_request.'" id="webfonts" />';
				}		
				// le placer avant les autres CSS du flux
				if (($p = strpos($flux,"<link"))!==false)
					$flux = substr_replace($flux,$code,$p,0);
				// sinon a la fin
				else
					$flux .= $code;
			}
		}
		$done = true;	
	}
	return $flux;
}





?>