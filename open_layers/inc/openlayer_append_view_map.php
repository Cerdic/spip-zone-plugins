<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz�lez
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */
 
function inc_openlayer_append_view_map_dist($target_id,$view_lat,$view_long,$view_zoom=NULL,$Markers = NULL,$view_icon = NULL){
	if (!strlen($view_lat) OR !is_numeric($view_lat)){
		$view_lat = lire_config('openlayer/latitude',0);
	}
	if (!strlen($view_long) OR !is_numeric($view_long)){
		$view_long = lire_config('openlayer/longitude',0);
	}
	if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = lire_config('openlayer/zoom',0);
	}
	if (!strlen($map_wms_name)){
		$map_wms_name = lire_config('openlayer/nom_serveur_openlayer','OpenLayers WMS');
	}
	if (!strlen($map_wms_url)){
		$map_wms_url = lire_config('openlayer/url_script_openlayer','http://labs.metacarta.com/wms/vmap0');
	}
	$out = '
	<script type="text/javascript">
				OpenLayers.ImgPath = "'._DIR_PLUGIN_OPENLAYER.'img_pack/mapui/";
        var viewMap = null;
        var viewMapMarkers  = null;
        function initViewMap(){
            viewMap = new OpenLayers.Map("'.$target_id.'",{
            	controls: [new OpenLayers.Control.LayerSwitcher()],
                maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
                numZoomLevels: 19,
                maxResolution: 156543.0399,
                units: "m",
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                theme : "'.find_in_path("img_pack/mapui/style.css").'"
            });
            var lon = '.$view_long.';
            var lat = '.$view_lat.';
            var zoom = '.$view_zoom.';
            var lonlat = new OpenLayers.LonLat(lon, lat);
            var wms = new OpenLayers.Layer.WMS(
                "'.$map_wms_name.'",
                "'.$map_wms_url.'",
                {layers: \'basic\'}
            );
            var osm = new OpenLayers.Layer.OSM();
            viewMap.addLayers([osm, wms]);
            viewMap.zoomTo(zoom);
            viewMap.setCenter(lonlat.fromDataToDisplay());
            viewMapMarkers = new OpenLayers.Layer.Markers("Markers");
			viewMap.addLayer(viewMapMarkers);
			var marcador = null;
			var icon = null;
			var size = null;
			var calculateOffset = null;';
	if (is_array($Markers) AND count($Markers)){
		foreach($Markers as $point){
			if ($view_icon){
				$out .= '
			size = new OpenLayers.Size(20,34);
			calculateOffset = function(size) { return new OpenLayers.Pixel(-(size.w/2), -size.h); };
			icon = new OpenLayers.Icon(
						"/IMG/'.$view_icon.'",
						size,
						null,
						calculateOffset
					);';
			} else {
				$out .= '
			size = new OpenLayers.Size(20,34);
			calculateOffset = function(size) { return new OpenLayers.Pixel(-(size.w/2), -size.h); };
			icon = new OpenLayers.Icon(
						"'._DIR_PLUGIN_OPENLAYER.'img_pack/correxir.png",
						size,
						null,
						calculateOffset
					);';
			}
			$out .= '
			marcador = new OpenLayers.Marker(lonlat.fromDataToDisplay(), icon);
            viewMapMarkers.addMarker(marcador);';
		}
	}
	$out .= '
        }
        $(document).ready(function(){
        	initViewMap();
        });
    </script>';
	return $out;
}
?>