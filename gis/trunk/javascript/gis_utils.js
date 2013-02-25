function gis_focus_marker(id, map) {
	var carte = eval('map'+ map);
	var i, count = 0;
	for(i in carte._layers) {
		if ((carte._layers[i].feature) && (carte._layers[i].feature.id == id)) {
			carte.panTo(carte._layers[i].getLatLng());
			carte._layers[i].openPopup();
		}
	count++;
	}
}