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
	if (lire_config('gis/geocoding'))
		$geocoding = true;
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
				'.($geocoding?'
				geocode(point.y,point.x);':'').'
			});':'')
	.($geocoding?'
			// reverse geocoding
			var geocode = function(lat,lonx) {
				var geocoder;
				geocoder = new GClientGeocoder();
				function showAddress(response) {
					console.log(response);
					if (!response || response.Status.code != 200) {
						return false;
					} else {
						$("#pays,#code_pays,#region,#ville,#code_postal").val("");
						CountryName = "";
						CountryNameCode = "";
						AdministrativeAreaName = "";
						LocalityName = "";
						PostalCodeNumber = "";
						place = response.Placemark[0];
						$("#map_adresse").val(place.address);
						if (Country = place.AddressDetails.Country){
							if (CountryName = Country.CountryName)
								$("#pays").val(CountryName);
							if (CountryNameCode = Country.CountryNameCode)
								$("#code_pays").val(CountryNameCode);
							if (AdministrativeArea = Country.AdministrativeArea){
								if (AdministrativeAreaName = AdministrativeArea.AdministrativeAreaName)
									$("#region").val(AdministrativeAreaName);
								if ((SubAdministrativeArea = AdministrativeArea.SubAdministrativeArea) && (Locality = SubAdministrativeArea.Locality)){
									if (LocalityName = Locality.LocalityName)
										$("#ville").val(LocalityName);
									if ((PostalCode = Locality.PostalCode) && (PostalCodeNumber = PostalCode.PostalCodeNumber))
										$("#code_postal").val(PostalCodeNumber);
								}
							}
						}
					}
				}
				geocoder.getLocations(new GLatLng(lat,lonx), showAddress);
			};':'')
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
					'.($geocoding?'
					geocode(point.y,point.x);':'').'
					GEvent.addListener(marker, \'dragend\', function(){
						var center = marker.getPoint();
	 					jQuery("#'.$target_lat_id.'").val(center.lat());
						jQuery("#'.$target_long_id.'").val(center.lng());
						'.($geocoding?'
						geocode(point.y,point.x);':'').'
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
							'.($geocoding?'
							geocode(point.y,point.x);':'').'
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