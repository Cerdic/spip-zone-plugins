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

*/
function webfonts2_fonts_list($fonts){
	return $fonts;
}

 /**
  * webfonts_insert_head_css
 */
function webfonts2_insert_head_css($flux){
	static $done = false;
	if (!$done){
		
		
		
		$webfonts = lister_webfonts();
		
		//var_dump($webfonts);
		if(is_array($webfonts)){
			foreach($webfonts as $font){
				$variants = implode(',',$font['variants']);
				$subsets = '&subset=';
				(isset($font['subsets'])) ? $subsets .= implode(',',$font['subsets']) : $subsets = '';
				$fonts[] = urlencode($font['family']).':'.$variants.$subsets;	
			}
			
			$fonts = implode('|',$fonts);
			if (strlen($fonts)) {
				$code = '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.$fonts.'" id="webfonts" />';
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



function lister_webfonts(){
	
	$fonts = pipeline('fonts_list',array(
		'args'=>array(),
		'data'=>$fonts
	));
	return $fonts;
}

?>