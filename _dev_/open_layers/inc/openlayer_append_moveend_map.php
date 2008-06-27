<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz‡lez
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */
 
function inc_openlayer_append_moveend_map_dist($target_id, $map_wms_name, $map_wms_url, $target_lat_id, $target_long_id, $view_lat, $view_long, $target_zoom_id=NULL, $view_zoom=NULL, $Marker=false){
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
        var map = null;
        OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
                defaultHandlerOptions: {
                    \'single\': true,
                    \'double\': false,
                    \'pixelTolerance\': 1000,
                    \'stopSingle\': false,
                    \'stopDouble\': false
                },

                initialize: function(options) {
                    this.handlerOptions = OpenLayers.Util.extend(
                        {}, this.defaultHandlerOptions
                    );
                    OpenLayers.Control.prototype.initialize.apply(
                        this, arguments
                    ); 
                    this.handler = new OpenLayers.Handler.Click(
                        this, {
                            \'click\': this.trigger
                        }, this.handlerOptions
                    );
                }, 

                trigger: function(e) {
                	var lonlat = map.getCenter();
                	var zoom = map.getZoom();
                	jQuery(\'#'.$target_lat_id.'\').val(lonlat.lat);
                	jQuery(\'#'.$target_long_id.'\').val(lonlat.lon);
                	jQuery(\'#'.$target_zoom_id.'\').val(zoom);
                }

        });
        function init(){
            map = new OpenLayers.Map(\''.$target_id.'\');
            var lon = '.$view_long.';
            var lat = '.$view_lat.';
            var zoom = '.$view_zoom.';
            var lonlat = new OpenLayers.LonLat(lon, lat);
            var wms = new OpenLayers.Layer.WMS(
                "'.$map_wms_name.'",
                "'.$map_wms_url.'",
                {layers: \'basic\'}
            );
            map.addLayer(wms);
            map.zoomTo(zoom);
            map.setCenter(lonlat);
            var click = new OpenLayers.Control.Click();
            map.addControl(click);
            click.activate();

        }
        function showAddress(address) {
        	//no funciona con el servidor de geonames da un error XMLHttpRequest
        	var lon = "";
        	var lat = "";
        	$.getJSON("http://ws.geonames.org/searchJSON?maxRows=1&q=\'" + address + "\'", function(data){
          		$.each(data.geonames, function(i,geoname){
          			lon = geoname.lng;
          			lat = geoname.lat;
          		});
        	});
        	var lonlat = new OpenLayers.LonLat(lon, lat);
        	map.setCenter(lonlat);
        	return false;
        }
        $(document).ready(function(){
        	init();
        });
    </script>';
    return $out;
}
?>