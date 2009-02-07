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
		//<![CDATA[
		URLbase = "/plugins";
		if (GBrowserIsCompatible()) {
			var lat='.$view_lat.';
			var long='.$view_long.';
			var formMap = new GMap2(document.getElementById("'.$target_id.'"));
			var geocoder = new GClientGeocoder();
			formMap.addControl(new GLargeMapControl());
			formMap.addControl(new GMapTypeControl());
			formMap.setCenter(new GLatLng(lat,long), '.$view_zoom.');
			var clicable_icon = new GIcon();
			clicable_icon.image = "'._DIR_PLUGIN_GEOMAP.'img_pack/correxir.png";
			clicable_icon.shadow = "'._DIR_PLUGIN_GEOMAP.'img_pack/shadow.png";
			clicable_icon.iconSize = new GSize(20, 34);
			clicable_icon.shadowSize = new GSize(37, 34);	
			clicable_icon.iconAnchor = new GPoint(10, 34);
			clicable_icon.infoWindowAnchor = new GPoint(5, 1);'		
	.($Marker?'
			point = new GPoint(long,lat);
			marker = new GMarker(point,{draggable:true,icon:clicable_icon});
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
					marker = new GMarker(point,{draggable:true,icon:clicable_icon}); 
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
	.'
		}
		jQuery(document).ready(function(){
			$("form#formulaire_address").submit(function(){
				var address = $("#map_address").attr("value");
    			if (geocoder) {
					geocoder.getLatLng(address, function(point) {
						if (!point) {
							alert(address + " not found");
						} else {
							formMap.setCenter(point);
							formMap.clearOverlays();
							marker = new GMarker(point,{draggable:true,icon:clicable_icon});
							formMap.addOverlay(marker);
							marker.openInfoWindowHtml(address);
							jQuery("#'.$target_lat_id.'").val(point.lat());
							jQuery("#'.$target_long_id.'").val(point.lng());
							jQuery("#'.$target_zoom_id.'").val(formMap.getZoom());
							GEvent.addListener(marker, \'dragend\', function(){
								var center = marker.getPoint();
	 							jQuery("#'.$target_lat_id.'").val(center.lat());
								jQuery("#'.$target_long_id.'").val(center.lng());
							});
						}
					});
				}
				return false;
    		});
		});
		//]]>
	</script>';
	return $out;
}
?>