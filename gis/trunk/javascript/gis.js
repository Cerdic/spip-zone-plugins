var gis_get_navigator_location = function(map,zoom){
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			map.setCenterAndZoom(new mxn.LatLonPoint(position.coords.latitude,position.coords.longitude),zoom ? zoom : 0);
		});
	}
}

function gis_close_infowindows(map) {
	var map = eval('map'+ map);
	for (i in map.markers) {
		map.markers[i].closeBubble();
	}
}

function gis_focus_marker(id, map) {
	var mxn = eval('map'+ map);
	gis_close_infowindows(map);
	for (i in mxn.markers) {
		if (mxn.markers[i].attributes.marker == id) {
			mxn.setCenter(mxn.markers[i].location);
			mxn.markers[i].openBubble();
		}
	}
}

function gis_autofocus_marker(map) {
	var mxn = eval('map'+ map);
	for (i in mxn.markers) {
		mxn.markers[i].openInfoBubble.addHandler(function(name, source, args) {
			gis_close_infowindows(map);
		});
	}
}

/* a reprendre de GIS V1 ?
function zoomIci(latit, lonxit, zoom, idmap) {
    var map = eval('map'+ idmap);
    map.panTo(new GLatLng(latit, lonxit));
    map.setZoom(zoom)
}
*/