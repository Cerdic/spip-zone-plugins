<?php

function Zoombox_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= Zoombox_call_css();
	}
	return $flux;
}

function Zoombox_insert_head($flux){
	$flux = Zoombox_insert_head_css($flux);
	
	if(!$GLOBALS["spip_pipeline"]["insert_js"])
		$flux .= Zoombox_call_js();

	return $flux;
}

function Zoombox_call_js() {
	
	if(function_exists('lire_config')){
		$pointeur = lire_config("config_zoombox/zoombox_pointeur", '.zoombox');
	 � �$theme = lire_config("zoombox/zoombox_theme", 'zoombox');
	 � �$opacity = lire_config("zoombox/zoombox_opacity",'0.8') ;
	 � �$duration = lire_config("zoombox/zoombox_duration", '800') ;
	 � �$animation = lire_config("zoombox/zoombox_animation", 'true');
	 � �$width = lire_config("zoombox/zoombox_width", '600');
	 � �$height = lire_config("zoombox/zoombox_height", '400') ;
	 � �$gallery = lire_config("zoombox/zoombox_gallery", 'true');
	 � �$autoplay = lire_config("zoombox/zoombox_autoplay", 'false');
	 � �$overflow = lire_config("zoombox/zoombox_overflow", 'false') ;
 	}
 	else{
		$pointeur = '.zoombox';
	 � �$theme = 'zoombox';
	 � �$opacity = '0.8';
	 � �$duration = '800';
	 � �$animation = 'true';
	 � �$width = '600';
	 � �$height = '400';
	 � �$gallery = 'true';
	 � �$autoplay = 'false';
	 � �$overflow = 'false';
 	}

	$flux = '<script src=\''.url_absolue(find_in_path('zoombox.js')).'\' type=\'text/javascript\'></script>';
	$flux .= '<script type="text/javascript"><!--
	jQuery(function($){
		$("'.$pointeur.'").addClass("zoombox");
    	$(".zoombox").zoombox({
    							theme : "'.$theme.'",
    							opacity : '.$opacity.',
    							duration : '.$duration.',
    							animation : '.$animation.',
    							width : '.$width.',
    							height : '.$height.',
    							gallery : '.$gallery.',
    							autoplay : '.$autoplay.',
    							overflow : '.$overflow.'
    						 });
    });
// --></script>';
	return $flux;
}

function Zoombox_call_css() {
	$flux = '<link rel="stylesheet" href="'.url_absolue(find_in_path('zoombox.css')).'" type="text/css" media="all" />';
	return $flux;
}
?>
