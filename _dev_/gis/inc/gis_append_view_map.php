<?php
function inc_gis_append_view_map_dist($target_id,$view_lat,$view_long,$Markers = NULL){
	$out = "";
	$out .=
		"<script type='text/javascript'>
		/*<![CDATA[*/\n
		if (GBrowserIsCompatible()) {
		/* create the map*/
			var map = new GMap2(document.getElementById('map'));
			map.setCenter(new GLatLng(".$view_lat.",".$view_long."), 8, G_MAP_TYPE);
			icono = new GIcon();";
	if (is_array($Markers) AND count($Markers)){
		foreach($Markers as $point){
			$out .= "
			icono.image = \""._DIR_PLUGIN_GIS."img_pack/correxir.png\";
			icono.shadow = \"http://www.escoitar.org/loudblog/custom/templates/berio/shadow.png\";
			icono.iconSize = new GSize(20, 34);
			icono.shadowSize = new GSize(22, 20);
			icono.iconAnchor = new GPoint(10, 34);
			icono.infoWindowAnchor = new GPoint(5,1);
			point = new GPoint(".$point['lon'].",".$point['lat'].");
			marker = new GMarker(point, icono);
			map.addOverlay(marker);";
		}
	}
	$out .= "
		} else {
			alert('Sorry, the Google Maps API is not compatible with this browser');
		}
		/*]]>*/
	</script>";
	return $out;
}
?>