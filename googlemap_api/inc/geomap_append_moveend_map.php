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

function inc_geomap_append_moveend_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false){
	if (!strlen($view_lat) OR !is_numeric($view_lat)){
		$view_lat = $view_lat = lire_config('geomap/latitude',0);
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
		var geocoder = null;
		var formMap = null;
		function showAddress(address) {
			if (geocoder) {
				geocoder.getLatLng(address, function(point) {
					if (!point) {
						alert(address+" not found");
					} else {
						formMap.setCenter(point);
					}
				});
			}
		}
		if (GBrowserIsCompatible()) {
			var lat='.$view_lat.';
			var long='.$view_long.';
			formMap = new GMap2(document.getElementById("'.$target_id.'"));
			formMap.addControl(new GLargeMapControl());
			formMap.addControl(new GMapTypeControl());
			formMap.setCenter(new GLatLng(lat,long), '.$view_zoom.');
			geocoder = new GClientGeocoder();'
	.($Marker?'
			point = new GPoint(parseFloat(long),parseFloat(lat));
			marker = new GMarker(point,{draggable:true});
			formMap.addOverlay(marker);
			GEvent.addListener(marker, \'dragend\', function(){
				var point = marker.getPoint();
				jQuery("#'.$target_lat_id.'").val(point.lat());
				jQuery("#'.$target_long_id.'").val(point.lng());
			});':'')
	.'
			GEvent.addListener(formMap, \'moveend\', function() {
				formMap.clearOverlays();
				var center = formMap.getCenter();
				jQuery("#'.$target_lat_id.'").val(center.lat());
				jQuery("#'.$target_long_id.'").val(center.lng());
				var point = new GLatLng(parseFloat(center.lat()), parseFloat(center.lng()));
				marker = new GMarker(point,{draggable:true}); 
				formMap.addOverlay(marker);
				GEvent.addListener(marker, \'dragend\', function(){
					var point = marker.getPoint();
				jQuery("#'.$target_lat_id.'").val(point.lat());
				jQuery("#'.$target_long_id.'").val(point.lng());
				});
			});'
	.($target_zoom_id?'
			GEvent.addListener(formMap, \'zoomend\', function(oldlevel, newlevel){
				jQuery("#'.$target_zoom_id.'").val(newlevel);}
			);':'')
	.'		} else {
				alert("'._T('geomap:geomap:erreur_api_browser').'");
			}
		/*]]>*/
	</script>';
	return $out;
}
?>