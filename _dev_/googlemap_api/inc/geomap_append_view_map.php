<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonzalez, Berio Molina
 * (c) 2007 - Distribudo baixo licencia GNU/GPL
 *
 */

function inc_geomap_append_view_map_dist($target_id,$view_lat,$view_long,$view_zoom=NULL,$Markers = NULL,$view_icon = NULL){
	if (!strlen($view_lat) OR !is_numeric($view_lat)){
		$view_lat = isset($GLOBALS['meta']['gis_default_lat'])?$GLOBALS['meta']['gis_default_lat']:'42.7631'; 
		if (!strlen($view_lat) OR !is_numeric($view_lat)) $view_lat='42.7631';
	}
	if (!strlen($view_long) OR !is_numeric($view_long)){
		$view_long = isset($GLOBALS['meta']['gis_default_lonx'])?$GLOBALS['meta']['gis_default_lonx']:'-7.9321'; 
		if (!strlen($view_long) OR !is_numeric($view_long)) $view_long='-7.9321';
	}
	if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = isset($GLOBALS['meta']['gis_default_zoom'])?$GLOBALS['meta']['gis_default_zoom']:'8'; 
		if (!strlen($view_zoom) OR !is_numeric($view_zoom)) $view_zoom='8';
	}
	$out = '
	<script type="text/javascript">
		/*<![CDATA[*/
		if (GBrowserIsCompatible()) {
			var viewMap = new GMap2(document.getElementById(\''.$target_id.'\'));
			viewMap.setCenter(new GLatLng('.$view_lat.','.$view_long.'), '.$view_zoom.');
			viewIcono = new GIcon();';
	if (is_array($Markers) AND count($Markers)){
		foreach($Markers as $point){
			if ($view_icon){
				$out .= '
			viewIcono.image = "/IMG/'.$view_icon.'";
			viewIcono.shadow = "'._DIR_PLUGIN_GEOMAP.'img_pack/shadow.png";';
			} else {
				$out .= '
			viewIcono.image = "'._DIR_PLUGIN_GEOMAP.'img_pack/correxir.png";
			viewIcono.shadow = "'._DIR_PLUGIN_GEOMAP.'img_pack/shadow.png";';
			}
			$out .= '
			viewIcono.iconSize = new GSize(20, 34);
			viewIcono.shadowSize = new GSize(22, 20);
			viewIcono.iconAnchor = new GPoint(10, 34);
			viewIcono.infoWindowAnchor = new GPoint(5,1);
			viewPoint = new GPoint('.$point['lonx'].','.$point['lat'].');
			viewMarker = new GMarker(viewPoint, viewIcono);
			viewMap.addOverlay(viewMarker);';
		}
	}
	$out .= '
		} else {
			alert("Sorry, the Google Maps API is not compatible with this browser");
		}
		/*]]>*/
	</script>';
	return $out;
}
?>