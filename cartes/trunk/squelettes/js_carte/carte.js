var resizemap = function (map_resize) {
	$('.map_wrapper').height($(window).height() - $('.footer').outerHeight()-$('.main').outerHeight());
	if (typeof map_resize != 'undefined' && typeof map_resize.invalidateSize == 'function') {
		map_resize.invalidateSize(true);
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

var country_min_topojson_addlayer = function(e,topojson){
    e.layer.eachLayer(function(dist){
	var iso = dist.toGeoJSON().properties.ISO2.toLowerCase();
	dist.bindPopup("<strong>"+dist.toGeoJSON().properties.NAME_FR+"</strong>",{closeButton:false,className:'popupcountry'});
	if(dist.options){
		dist.options.className = 'country country-'+iso;
	}else {
		dist.eachLayer(function (dist2) {
			dist2.options.className = 'country country-'+iso;
		});
	}
    });
    window.setTimeout(function(){topojson.bringToBack()},5);
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
		resizemap(resized);
	});

	jQuery('#map1').on('ready',function(e, map){
	    if(map.options.options && map.options.options.popup == 'control'){
		var info = L.control();
		info.onAdd = function (map) {
			this._div = L.DomUtil.create('div', 'info leaflet-popup-content-wrapper');
			this.update();
			return this._div;
		};

		// method that we will use to update the control based on feature properties passed
		info.update = function (text) {
			if(typeof text != 'undefined'){
				L.DomUtil.setOpacity(this._div,1);
			}
			else {
				L.DomUtil.setOpacity(this._div,0);
			}
			this._div.innerHTML = text ? '<div class="leaflet-popup-content">'+text+'</div>' : '';
		};

		info.addTo(map);
		map.eachLayer(function(layer){
			layer.off('click').on('click',function(e,f){
				if(layer._popup && layer._popup._content){
				    	console.log($.inArray('informatif',layer.feature.role));
				    	console.log(layer.feature);
				    	if(layer.feature.properties.role && $.inArray('informatif',layer.feature.properties.role) != -1)
				    	    return false;
					info.update(layer._popup._content);
					map.closePopup();
					map.panTo(e.latlng);
					return false;
				}
			});
		});
		map.on('popupopen',function(){
		    	info.update();
		});
	    }
	    if(map.options.options && map.options.options.layer_topojson){
	    	var topojson_layer = new L.TOPOJSON(map.options.options.layer_topojson, {async: true}),
	    		fichier_topojson = map1.options.options.layer_topojson.replace(/^.*[\\\/]/, '').replace(/\./g,'_'),
	    		addlayer = fichier_topojson+'_addlayer';
	    	if(typeof window[addlayer] == 'function'){
        	    	topojson_layer.on('addlayer',function(e){
        	    	    	eval(eval(addlayer)(e,topojson_layer));
        	    	});
	    	}
	    	map.addLayer(topojson_layer);
	    	topojson_layer.bringToFront();
	    }
	});
});