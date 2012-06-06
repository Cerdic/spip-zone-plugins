// Fonctions adaptées de https://github.com/CloudMade/Leaflet/blob/master/src/layer/GeoJSON.js#L41
// voir aussi https://github.com/CloudMade/Leaflet/issues/712

var layerToGeometry = function(layer) {
	var json, type, latlng, latlngs = [], i;
	
	if (L.Marker && (layer instanceof L.Marker)) {
		type = 'Point';
		latlng = LatLngToCoords(layer._latlng);
		return JSON.stringify({"type": type, "coordinates": latlng});
		
	} else if (L.Polygon && (layer instanceof L.Polygon)) {
		type = 'Polygon';
		latlngs = LatLngsToCoords(layer._latlngs, 1);
		latlngs.push(latlngs[0]); // un polygon en geojson et WKT doit être fermé par le premier point
		return JSON.stringify({"type": type, "coordinates": [latlngs]});
		
	} else if (L.Polyline && (layer instanceof L.Polyline)) {
		type = 'LineString';
		latlngs = LatLngsToCoords(layer._latlngs);
		return JSON.stringify({"type": type, "coordinates": latlngs});
		
	}
}

var LatLngToCoords = function (LatLng, reverse) { // (LatLng, Boolean) -> Array
	var lat = parseFloat(reverse ? LatLng.lng : LatLng.lat),
		lng = parseFloat(reverse ? LatLng.lat : LatLng.lng);

	return [lng,lat];
}

var LatLngsToCoords = function (LatLngs, levelsDeep, reverse) { // (LatLngs, Number, Boolean) -> Array
	var coord,
		coords = [],
		i, len;

	for (i = 0, len = LatLngs.length; i < len; i++) {
		coord = levelsDeep ?
				LatLngToCoords(LatLngs[i], levelsDeep - 1, reverse) :
				LatLngToCoords(LatLngs[i], reverse);
		coords.push(coord);
	}

	return coords;
}