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

function inc_geomap_append_clicable_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false){
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
			URLbase = "/plugins";
			if (GBrowserIsCompatible()) {
				var lat='.$view_lat.';
				var long='.$view_long.';
				var formMap = new GMap2(document.getElementById("'.$target_id.'"));
				formMap.addControl(new GLargeMapControl());
				formMap.addControl(new GMapTypeControl());
				// dont think this is still used
				//formMap.addControl(new mapAddressControl());
				formMap.setCenter(new GLatLng(lat,long), '.$view_zoom.');'
	.($Marker?'
				point = new GPoint(long,lat);
				marker = new GMarker(point,{draggable:true});
				formMap.addOverlay(marker);
				GEvent.addListener(marker, \'dragend\', function(){
					var center = marker.getPoint();
		  			jQuery("#'.$target_lat_id.'").val(center.lat());
					jQuery("#'.$target_long_id.'").val(center.lng());
				});':'')
	.'			
				GEvent.addListener(formMap, \'click\', function(overlay,point) {
					formMap.clearOverlays();
					if(point){
						marker = new GMarker(point,{draggable:true}); 
						formMap.addOverlay(marker);
						var center = marker.getPoint();
						var zoom = formMap.getZoom();
						jQuery("#'.$target_lat_id.'").val(center.lat());
						jQuery("#'.$target_long_id.'").val(center.lng());
						jQuery("#'.$target_zoom_id.'").val(zoom);
						GEvent.addListener(marker, \'dragend\', function(){
							var center = marker.getPoint();
		  					jQuery("#'.$target_lat_id.'").val(center.lat());
							jQuery("#'.$target_long_id.'").val(center.lng());
						});
					}
				});'
	.($target_zoom_id?'
				GEvent.addListener(formMap, "zoomend", function(oldlevel, newlevel){ jQuery("#'.$target_zoom_id.'").val(newlevel);});':'')
	.'		} else {
				alert("Sorry, the Google Maps API is not compatible with this browser");
			}
		/*]]>*/
	</script>';
	return $out;
}
?>