<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

function inc_geomap_append_mini_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false){
	if (!strlen($view_lat) OR !is_numeric($view_lat)){
		$view_lat = isset($GLOBALS['meta']['geomap_default_lat'])?$GLOBALS['meta']['geomap_default_lat']:'42.7631'; 
		if (!strlen($view_lat) OR !is_numeric($view_lat)) $view_lat='42.7631';
	}
	if (!strlen($view_long) OR !is_numeric($view_long)){
		$view_long = isset($GLOBALS['meta']['geomap_default_lonx'])?$GLOBALS['meta']['geomap_default_lonx']:'-7.9321'; 
		if (!strlen($view_long) OR !is_numeric($view_long)) $view_long='-7.9321';
	}
	if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = isset($GLOBALS['meta']['geomap_default_zoom'])?$GLOBALS['meta']['geomap_default_zoom']:'8'; 
		if (!strlen($view_zoom) OR !is_numeric($view_zoom)) $view_zoom='8';
	}
	return 
	"<script type='text/javascript'>
		/*<![CDATA[*/\n
			if (GBrowserIsCompatible()) {
			/* create the map*/
				var lat=$view_lat;
				var long=$view_long;
				var formMap = new GMap2(document.getElementById('$target_id'));
				formMap.addControl(new GSmallMapControl());
				formMap.setCenter(new GLatLng(lat,long), ".$view_zoom.", G_MAP_TYPE);"
	. ($Marker?"
				point = new GPoint(long,lat);
				formMap.addOverlay(new GMarker(point));":"")
  ."
  			/* creamos el evento para crear nuevos marcadores*/
				GEvent.addListener(formMap, 'click', function(overlay, point){
					formMap.clearOverlays();
					if (point) {
						formMap.addOverlay(new GMarker(point));
						formMap.panTo(point);
						$('#$target_lat_id').val(point.y);
						$('#$target_long_id').val(point.x);
					}
				});"
  . ($target_zoom_id?"
				GEvent.addListener(formMap, 'zoomend', function(oldlevel, newlevel){ $('#$target_zoom_id').val(newlevel);});":"")
	."		} else {
				alert('Sorry, the Google Maps API is not compatible with this browser');
			}
		/*]]>*/
	</script>";
}
?>