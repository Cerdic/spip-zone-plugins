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
                	jQuery("#'.$target_lat_id.'").val(lonlat.fromDisplayToData().lat);
                	jQuery("#'.$target_long_id.'").val(lonlat.fromDisplayToData().lon);
                	jQuery("#'.$target_zoom_id.'").val(zoom);
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
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            var click = new OpenLayers.Control.Click();
            map.addControl(click);
            click.activate();

        }
        $(document).ready(function(){
        	init();
			$("form#formulaire_address").submit(function(){
				var address = $("#map_address").attr("value");
				geocode(address,map);
				return false;
    		});
        });
    </script>';
    return $out;
}
?>