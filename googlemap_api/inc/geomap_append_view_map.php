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

function inc_geomap_append_view_map_dist($target_id,$view_lat,$view_long,$view_zoom=NULL,$Markers = NULL,$view_icon = NULL){
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
		function loadViewMap() {
			if (GBrowserIsCompatible()) {
				var viewMap = new GMap2(document.getElementById(\''.$target_id.'\'));
				viewMap.setCenter(new GLatLng('.$view_lat.','.$view_long.'), '.$view_zoom.');
				var view_icon = new GIcon();';
	if (is_array($Markers) AND count($Markers)){
		foreach($Markers as $point){
			if ($view_icon){
				$out .= '
				view_icon.image = "../IMG/'.$view_icon.'";';
			} else {
				$out .= '
				view_icon.image = "'._DIR_PLUGIN_GEOMAP.'img_pack/correxir.png";';
			}
			$out .= '
				view_icon.shadow = "'._DIR_PLUGIN_GEOMAP.'img_pack/shadow.png";
				view_icon.iconSize = new GSize(20, 34);
				view_icon.shadowSize = new GSize(37, 34);	
				view_icon.iconAnchor = new GPoint(10, 34);
				view_icon.infoWindowAnchor = new GPoint(5, 1);

				viewPoint = new GPoint('.$point['lonx'].','.$point['lat'].');
				viewMarker = new GMarker(viewPoint, view_icon);
				viewMap.addOverlay(viewMarker);';
		}
	}
	$out .= '
			}
		}
		jQuery(document).ready(function(){
			loadViewMap();
		});
		//]]>
	</script>';
	return $out;
}
?>