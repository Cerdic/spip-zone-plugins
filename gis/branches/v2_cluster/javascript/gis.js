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

function gis_load_json(map) {
	// récuper le zoom de la carte
	map.json_args.zoom = map.getZoom();
	// et les coords des points sw et ne de la carte
	var bounds = map.getBounds();
	// pour les ajouter à la requête ajax
	map.json_args.minlon = bounds.sw.lon;
	map.json_args.minlat = bounds.sw.lat;
	map.json_args.maxlon = bounds.ne.lon;
	map.json_args.maxlat = bounds.ne.lat;
	// applique-t-on le clustering ?
	if (map.cluster) map.json_args.cluster= true;
	// lancer la requête
	jQuery.getJSON(map.json_url, map.json_args,
		function(data) {
			if (data){
				// virer tous les markers de la carte
				map.removeAllMarkers();
				// et ajouter les markers renvoyés par la requete
				map.addJSON(data);
				if (map.cluster) gis_cluster(map);
			}
		}
	);
}

function gis_cluster(map) {
	for (i in map.markers) {
		if (map.markers[i].attributes.category == 'cluster') {
			var marker = map.markers[i];
			// éviter de reboucler sur ce marker
			marker.attributes.category = '';
			// retirer le marker de la carte
			map.removeMarker(marker);
			// le remplacer par un marker de type cluster
			marker.setIcon(map.cluster_icon, [40,40], [20,20]);
			// on surveille le clic sur le marker
			marker.click.addHandler(function(event_name, event_source, event_args) {
				console.log('click');
				var bounds = new mxn.BoundingBox(event_source.attributes.data.sw_lat, event_source.attributes.data.sw_lon, event_source.attributes.data.ne_lat, event_source.attributes.data.ne_lon);
				console.log(event_source);
				map.setBounds(bounds);
				gis_load_json(map);
			});
			// et on l'ajoute à la carte
			map.addMarker(marker);
		}
	}
}