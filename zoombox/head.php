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
	
	$pointeur = (lire_config($chemin="zoombox/zoombox_pointeur") != '') ? lire_config($chemin="zoombox/zoombox_pointeur") : '.zoombox' ;
	$theme = (lire_config($chemin="zoombox/zoombox_theme") != '') ? lire_config($chemin="zoombox/zoombox_theme") : 'zoombox' ;
	$opacity = (lire_config($chemin="zoombox/zoombox_opacity") != '') ? lire_config($chemin="zoombox/zoombox_opacity") : 0.8 ;
	$duration = (lire_config($chemin="zoombox/zoombox_duration") != '') ? lire_config($chemin="zoombox/zoombox_duration") : 800 ;
	$animation = (lire_config($chemin="zoombox/zoombox_animation") != '') ? lire_config($chemin="zoombox/zoombox_animation") : true ;
	$width = (lire_config($chemin="zoombox/zoombox_width") != '') ? lire_config($chemin="zoombox/zoombox_width") : 600 ;
	$height = (lire_config($chemin="zoombox/zoombox_height") != '') ? lire_config($chemin="zoombox/zoombox_height") : 400 ;
	$gallery = (lire_config($chemin="zoombox/zoombox_gallery") != '') ? lire_config($chemin="zoombox/zoombox_gallery") : true ;
	$autoplay = (lire_config($chemin="zoombox/zoombox_autoplay") != '') ? lire_config($chemin="zoombox/zoombox_autoplay") : false ;
	$overflow = (lire_config($chemin="zoombox/zoombox_overflow") != '') ? lire_config($chemin="zoombox/zoombox_overflow") : false ;

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
