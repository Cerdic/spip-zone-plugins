<?php	

	include_spip('inc/config');

	function action_mymap_add_gmap_to_article() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = isset($GLOBALS['meta']['mymap_default_zoom'])?$GLOBALS['meta']['mymap_default_zoom']:'8'; 
		if (!strlen($view_zoom) OR !is_numeric($view_zoom)) $view_zoom='8';
		}
		if(sizeof($view_lat)==0 OR (sizeof($view_lat)> 0  AND !is_numeric($view_lat))){
		$view_lat = isset($GLOBALS['meta']['mymap_default_lat'])?$GLOBALS['meta']['mymap_default_lat']:'47.15984'; 
		if (!strlen($view_lat) OR !is_numeric($view_lat)) $view_lat='47.15984';
		}	
		if(sizeof($view_long)==0 OR (sizeof($view_long)> 0  AND !is_numeric($view_long))){
		$view_long = isset($GLOBALS['meta']['mymap_default_lonx'])?$GLOBALS['meta']['mymap_default_lonx']:'2.988281'; 
		if (!strlen($view_long) OR !is_numeric($view_long)) $view_long='2.988281';
		}
		$result = spip_query("INSERT INTO spip_mymap_articles ( `id_article` , `lat` , `lonx` , `zoom` ) VALUES ('".$_POST["id_article"]."', '".$view_lat."', '".$view_long."', '".$view_zoom."')");
		
	}
?>