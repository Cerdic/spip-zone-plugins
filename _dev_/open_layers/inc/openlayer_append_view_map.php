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
	if (!strlen($map_wms_name)){
		$map_wms_name = isset($GLOBALS['meta']['openlayer_wmsname'])?$GLOBALS['meta']['openlayer_wmsname']:'OpenLayers WMS'; 
		if (!strlen($map_wms_name)) $map_wms_name='OpenLayers WMS';
	}
	if (!strlen($map_wms_url)){
		$map_wms_url = isset($GLOBALS['meta']['openlayer_wmsurl'])?$GLOBALS['meta']['openlayer_wmsurl']:'http://labs.metacarta.com/wms/vmap0'; 
		if (!strlen($map_wms_url)) $map_wms_url='http://labs.metacarta.com/wms/vmap0';
	}
	$out = '
	<script type="text/javascript">
        var viewMap = null;
        var viewMapMarkers  = null;
        function initViewMap(){
            viewMap = new OpenLayers.Map(\''.$target_id.'\', { controls: [] });
            var lon = '.$view_long.';
            var lat = '.$view_lat.';
            var zoom = '.$view_zoom.';
            var lonlat = new OpenLayers.LonLat(lon, lat);
            var wms = new OpenLayers.Layer.WMS(
                "'.$map_wms_name.'",
                "'.$map_wms_url.'",
                {layers: \'basic\'}
            );
            viewMap.addLayer(wms);
            viewMap.zoomTo(zoom);
            viewMap.setCenter(lonlat);
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
						\'/IMG/'.$view_icon.'\',
						size,
						null,
						calculateOffset
					);';
			} else {
				$out .= '
			size = new OpenLayers.Size(20,34);
			calculateOffset = function(size) { return new OpenLayers.Pixel(-(size.w/2), -size.h); };
			icon = new OpenLayers.Icon(
						\''._DIR_PLUGIN_GEOMAP.'img_pack/correxir.png\',
						size,
						null,
						calculateOffset
					);';
			}
			$out .= '
			marcador = new OpenLayers.Marker(lonlat, icon);
            viewMapMarkers.addMarker(marcador);';
		}
	}
	$out .= '
        }
        $(document).ready(function(){
        	initViewMap();
        });
    </script>';
	//por algunha razon o view map interfire co cliclable map en openlayers impedindo o funcionamento do cotrol que permite facer click
	//return $out;
}
?>