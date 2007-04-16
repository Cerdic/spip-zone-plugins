<?php
function inc_gis_append_clicable_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long,$Marker = false){
	return 
	"<script type='text/javascript'>
		/*<![CDATA[*/\n
			if (GBrowserIsCompatible()) {
			/* create the map*/
				var lat=$view_lat;
				var long=$view_long;
				var formMap = new GMap2(document.getElementById('$target_id'));
				formMap.addControl(new GLargeMapControl());
				formMap.addControl(new GMapTypeControl());
				formMap.setCenter(new GLatLng(lat,long), 8, G_MAP_TYPE);"
	. ($Marker?"
				point = new GPoint(long,lat);
				formMap.addOverlay(new GMarker(point));":"")
  ."
  			/* creamos el evento para crear nuevos marcadores*/
				GEvent.addListener(formMap, 'click', function(overlay, point){
					formMap.clearOverlays();
					if (point) {
						formMap.addOverlay(new GMarker(point));
						formMap.panTo(point);
						$('#$target_lat_id').val(point.y);
						$('#$target_long_id').val(point.x);
					}
				});
			} else {
				alert('Sorry, the Google Maps API is not compatible with this browser');
			}
		/*]]>*/
	</script>";
}
?>