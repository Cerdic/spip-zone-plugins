var lang_direction = $('html').attr('dir') ? $('html').attr('dir') : 'ltr',
	informatifs = [];

var resizemap = function (map_resize) {
	$('.map_wrapper').height($(window).height() - $('.footer').outerHeight()-$('.main').outerHeight());
	if (typeof map_resize != 'undefined' && typeof map_resize.invalidateSize == 'function') {
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

var country_min_topojson_addlayer = function(e,topojson){
	e.layer.eachLayer(function(dist){
		var iso = dist.toGeoJSON().properties.ISO2.toLowerCase();
		if(dist.options){
			dist.options.className = 'country country-'+iso;
		}else {
			dist.eachLayer(function (dist2) {
				dist2.options.className = 'country country-'+iso;
			});
		}
	});
	window.setTimeout(function(){topojson.bringToBack();resizemap(map1);},20);
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
			map1.on('viewreset',function(){
				map1.invalidateSize();
				informatifs.forEach(function(e){console.log(e);e.label._updatePosition();})
			});
		}
		resizemap(resized);
	});

	jQuery('#map1').on('ready',function(e, map){
		var label_bloque = false;
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
					info.update(layer._popup._content);
					if (label_bloque && map1._layers[label_bloque] && map1._layers[label_bloque].label) {
						options_bloque = map1._layers[label_bloque].label.options;
						options_bloque.noHide = false;
						layerx = map1._layers[label_bloque];
						layerx.options.zIndexOffset = 0;
						if (layerx.feature && layerx.feature.properties && (layerx.feature.properties.title || (map.options.langue && layerx.feature.properties['title_'+map.options.langue]))) {
							title = (map.options.langue && layerx.feature.properties['title_'+map.options.langue]) || layerx.feature.properties.title;
							layerx.unbindLabel().bindLabel(title, options_bloque);
						}
						
						map.removeLayer(layerx);
						layerx.addTo(map);
					}
					if (layer.label) {
						options = layer.label.options;
						options.noHide = true
						label_bloque = layer._leaflet_id;
						if (layer.feature && layer.feature.properties && (layer.feature.properties.title || (map.options.langue && layer.feature.properties['title_'+map.options.langue]))) {
							title = (map.options.langue && layer.feature.properties['title_'+map.options.langue]) || layer.feature.properties.title;
							layer.unbindLabel().bindLabel(layer.feature.properties.title, options);
						}
						map.removeLayer(layer);
						layer.options.zIndexOffset = 1000;
						layer.addTo(map);
					}
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
		map.eachLayer(function(layer){
			/**
			 * Gestion des points informatifs
			 * 
			 * Ils ne sont pas clickable et sélectionnables
			 * Ils n'ont pas de popup
			 * On leur met un label qui est leur titre
			 */
			if(layer.feature && layer.feature.properties && layer.feature.properties.role && $.inArray('informatif',layer.feature.properties.role) != -1){
				layer.options.clickable = false;
				layer.options.riseOnHover = true;
				layer.options.keyboard = false;
				layer.options.icon.options.labelAnchor = [4,-4];
				if (lang_direction == 'rtl') {
					var offset = [60, -15]
				} else {
					var offset = [20, -15]
				}
				layer.unbindPopup().bindLabel(layer.feature.properties.title, { noHide: true, className: 'informatif', direction: 'right', lang_direction : lang_direction, offset: offset});
				map.removeLayer(layer);
				layer.addTo(map);
				informatifs.push(layer);
			} else if (map.options.options && map.options.options.label && layer.feature && layer.feature.properties && layer.feature.properties.title) {
				if (layer.options && typeof layer.options.riseOnHover != 'undefined')
					layer.options.riseOnHover = true;
					if (layer.feature.properties.title || (map.options.langue && layer.feature.properties['title_'+map.options.langue])) {
						title = (map.options.langue && layer.feature.properties['title_'+map.options.langue]) || layer.feature.properties.title;
						layer.bindLabel(title, { className: 'action', direction: 'auto', lang_direction : lang_direction});
					}
				map.removeLayer(layer);
				layer.addTo(map);
			}
		});
		if(map.options.options && map.options.options.layer_topojson){
			var topojson_layer = new L.TOPOJSON(map.options.options.layer_topojson, {async: true}),
				fichier_topojson = map1.options.options.layer_topojson.replace(/^.*[\\\/]/, '').replace(/\./g,'_'),
				addlayer = fichier_topojson+'_addlayer';
			if (typeof window[addlayer] == 'function') {
				topojson_layer.on('addlayer',function(e){
					eval(eval(addlayer)(e,topojson_layer));
				});
			}
			map.addLayer(topojson_layer);
			topojson_layer.bringToFront();
		}
		resizemap(map);
	});
});