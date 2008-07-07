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
 
 function inc_openlayer_append_clicable_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false){
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
                    var lonlat = map.getLonLatFromViewPortPx(e.xy);
                    var zoom = map.getZoom();
                    markers.destroy();
                    markers = new OpenLayers.Layer.Markers("Markers");
                    map.addLayer(markers);
                    var marcador = new OpenLayers.Marker(lonlat, icon);
                    markers.addMarker(marcador);
                    jQuery("#'.$target_lat_id.'").val(lonlat.fromDisplayToData().lat);
                    jQuery("#'.$target_long_id.'").val(lonlat.fromDisplayToData().lon);'
                    . ($target_zoom_id?'jQuery("#'.$target_zoom_id.'").val(zoom);':'') .
                    'map.setCenter(lonlat);
                }

        });
        function init(){
            map = new OpenLayers.Map("'.$target_id.'",{
                maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
                numZoomLevels: 19,
                maxResolution: 156543.0399,
                units: "m",
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326")
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
            var nasa = new OpenLayers.Layer.WMS( "NASA Global Mosaic",
                "http://t1.hypercube.telascience.org/cgi-bin/landsat7", 
                {layers: "landsat7"}
            );
            var osm = new OpenLayers.Layer.OSM.Osmarender("Osmarender");
            map.addLayers([osm, nasa, wms]);
            map.zoomTo(zoom);
            map.setCenter(lonlat.fromDataToDisplay());
            markers = new OpenLayers.Layer.Markers("Markers");
            map.addLayer(markers);
            var size = new OpenLayers.Size(20,34);
            var calculateOffset = function(size) { return new OpenLayers.Pixel(-(size.w/2), -size.h); };
            icon = new OpenLayers.Icon(
                \''._DIR_PLUGIN_OPENLAYER.'img_pack/correxir.png\',
                size,
                null,
                calculateOffset
            );
            '. ($Marker?'var marcador = new OpenLayers.Marker(lonlat.fromDataToDisplay(), icon);
            markers.addMarker(marcador);':'').'
            map.addControl(new OpenLayers.Control.LayerSwitcher());
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