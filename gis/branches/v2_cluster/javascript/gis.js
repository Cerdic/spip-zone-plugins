var gis_get_navigator_location = function(map,zoom) {
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			map.setCenterAndZoom(new mxn.LatLonPoint(position.coords.latitude,position.coords.longitude),zoom ? zoom : 0);
		});
	}
}

function gis_focus_marker(id, map) {
	var map = eval('map'+ map);
	for (i in map.markers) {
		if (map.markers[i].attributes.marker == id) {
			map.setCenter(map.markers[i].location);
			map.markers[i].openBubble();
		} else {
			map.markers[i].closeBubble();
		}
	}
}

/* a reprendre de GIS V1 ?
function zoomIci(latit, lonxit, zoom, idmap) {
    var map = eval('map'+ idmap);
    map.panTo(new GLatLng(latit, lonxit));
    map.setZoom(zoom)
}
*/

//mxn.addProxyMethods(Mapstraction,[addGeoJSON]);

function gis_cluster(map, img) {
	//var map = eval('map'+ map);
	for (i in map.markers) {
		if (map.markers[i].attributes.category == 'cluster') {
			var marker = map.markers[i];
			map.removeMarker(marker);
			marker.setIcon(img, [40,40], [20,20]);
			marker.click.addHandler(function(event_name, event_source, event_args) {
				console.log(event_source);
				event_source.closeInfoBubble.fire();
				map.removeAllMarkers();
				var bounds = new mxn.BoundingBox(event_source.attributes.data.sw_lat, event_source.attributes.data.sw_lon, event_source.attributes.data.ne_lat, event_source.attributes.data.ne_lon);
				map.setBounds(bounds); 
			});
			map.addMarker(marker);
		}
	}
}