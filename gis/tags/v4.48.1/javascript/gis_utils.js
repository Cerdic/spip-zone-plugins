function gis_focus_marker (id, map) {
	var carte = eval('map'+ map);
	var i, count = 0;
	for (i in carte._layers) {
		if (L.MarkerClusterGroup && carte._layers[i] instanceof L.MarkerClusterGroup) {
			carte._layers[i].eachLayer(function(layer) {
				if (layer.id && layer.id == id) {
					carte._layers[i].zoomToShowLayer(layer, function(){
						layer.openPopup();
					});
					count++;
				}
			});
			if (count > 0) {
				break;
			}
		} else if (((carte._layers[i].feature) && (carte._layers[i].feature.id == id)) || (carte._layers[i].id && carte._layers[i].id == id)) {
			carte.centerAndZoom(carte._layers[i].getLatLng(), true);
			carte._layers[i].openPopup();
			break;
		}
		count++;
	}
}