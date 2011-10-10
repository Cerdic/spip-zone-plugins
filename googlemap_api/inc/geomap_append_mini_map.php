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

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_geomap_append_mini_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false){
	if (!strlen($view_lat) OR !is_numeric($view_lat)){
		$view_lat = lire_config('geomap/latitude',0);
	}
	if (!strlen($view_long) OR !is_numeric($view_long)){
		$view_long = lire_config('geomap/longitude',0);
	}
	if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = lire_config('geomap/zoom',0);
	}
	$out = '
	<script type="text/javascript">
		/*<![CDATA[*/
			if (GBrowserIsCompatible()) {
				var lat='.$view_lat.';
				var long='.$view_long.';
				var formMap = new GMap2(document.getElementById("'.$target_id.'"));
				formMap.addControl(new GSmallMapControl());
				formMap.setCenter(new GLatLng(lat,long), '.$view_zoom.'	);'
	.($Marker?'
				point = new GPoint(long,lat);
				formMap.addOverlay(new GMarker(point));':'')
	.'
				GEvent.addListener(formMap, \'click\', function(overlay, point){
					formMap.clearOverlays();
					if (point) {
						formMap.addOverlay(new GMarker(point));
						formMap.panTo(point);
						jQuery("#'.$target_lat_id.'").val(point.y);
						jQuery("#'.$target_long_id.'").val(point.x);
					}
				});'
	.($target_zoom_id?'
				GEvent.addListener(formMap, \'zoomend\', function(oldlevel, newlevel){ jQuery("#'.$target_zoom_id.'").val(newlevel);});':'')
	.'		} else {
				alert("'._T('geomap:geomap:erreur_api_browser').'");
			}
		/*]]>*/
	</script>';
	return $out;
}
?>