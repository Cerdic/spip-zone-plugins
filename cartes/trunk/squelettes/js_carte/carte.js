var resizemap = function (map_resize) {
	$('.map_wrapper').height($(window).height() - $('.footer').outerHeight()-$('.main').outerHeight());
	if (typeof map_resize != 'undefined' && typeof map_resize.invalidateSize == "function") {
		map_resize.invalidateSize();
	}
}
var callback_map1 = function (map) {
	resizemap(map);
	if (typeof(data_bounds) != 'undefined') {
		geojson_bounds = new L.geoJson('');
		geojson_bounds.addData(data_bounds);
		bounds = geojson_bounds.getBounds();
		map.setMaxBounds(bounds);
	}
}

$(document).ready(function () {
	$(window).on('resize', function () {
		var resized = false;
		if (map1) {
			resized = map1;
		}
		resizemap(resized)
	});
	$(window).on('load', function () {
		var resized = false;
		if (map1) {
			resized = map1;
		}
		resizemap(resized)
	});
	jQuery('#map1').on('ready',function(map){
		if(typeof map1 != 'undefined' && map1.options.options && map1.options.options.popup == "control"){
		    var info = L.control();
        	    info.onAdd = function (map) {
        	        this._div = L.DomUtil.create('div', 'info leaflet-popup-content-wrapper');
        	        this.update();
        	        return this._div;
        	    };
        
        	    // method that we will use to update the control based on feature properties passed
        	    info.update = function (text) {
        		if(typeof text != "undefined")
        		    L.DomUtil.setOpacity(this._div,1);
        		else
        		    L.DomUtil.setOpacity(this._div,0);
        	        this._div.innerHTML = text ? '<div class="leaflet-popup-content">'+text+'</div>' : '';
        	    };
        
        	    info.addTo(map1);
			map1.eachLayer(function(layer){
				layer.off('click').on('click',function(e,f){
					if(layer._popup && layer._popup._content){
					    	info.update(layer._popup._content);
						map1.panTo(e.latlng)
						return false;
					}
				});
			});
		}
		
	});
});