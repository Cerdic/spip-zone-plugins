(function () {
// Plugin Leaflet L.Map.Gis
L.Map.Gis = L.Map.extend({

	includes: L.Evented.prototype,

	options:{
		mapId: 'map_gis',
		utiliser_bb: false,
		sw_lat: 0,
		ne_lat: 0,
		sw_lon: 0,
		ne_lon: 0,
		gis_layers: L.gisConfig.gis_layers,
		default_layer: L.gisConfig.default_layer,
		affiche_layers: L.gisConfig.affiche_layers,
		scaleControl: false,
		overviewControl: false,
		layersControl: false,
		layersControlOptions: {},
		noControl: false,
		tooltip: false,
		cluster: false,
		clusterOptions: {
			disableClusteringAtZoom: 0,
			showCoverageOnHover: false,
			maxClusterRadius: 80,
			spiderfyOnMaxZoom: false
		},
		pathStyles: null,
		autocenterandzoom: false,
		openId: false,
		affiche_points: true,
		json_points: {
			url: '',
			objets: '',
			limit: 500,
			env: [],
			titre: '',
			description: '',
			icone: ''
		},
		localize_visitor: false,
		localize_visitor_zoom: 0,
		centrer_fichier: true,
		kml: false,
		gpx: false,
		geojson: false,
		topojson: false,
		langue: false
	},

	initialize: function (id,options) {
		L.Util.setOptions(this, options);

		this.on('load', function () {
			// Affecter sur l'objet du DOM
			jQuery('#'+this._container.id).get(0).map = this;
			// Appeler l'éventuelle fonction de callback
			if (this.options.callback && typeof(this.options.callback) === 'function')
				this.options.callback(this);
			// trigger load sur l'objet du DOM
			jQuery('#'+this._container.id).trigger('load',this);
		});

		L.Map.prototype.initialize.call(this, id, options);

		if (this.options.utiliser_bb) {
			this.fitBounds(
				L.latLngBounds(
					[this.options.sw_lat, this.options.sw_lon],
					[this.options.ne_lat, this.options.ne_lon]
				)
			);
		}

		this.populateTileLayers(this.options.affiche_layers);

		this.initControls();

		this.loadData();

		this.addOverlays();

		if (this.options.localize_visitor) {
			var maxZoom = this.options.localize_visitor_zoom;
			this.on('locationerror',function (e) {
				maxZoom = this.options.zoom;
				alert(e.message);
			});
			this.locate({setView: true, maxZoom: maxZoom});
		}

		// Si pas de points affichés trigger ready ici
		if (!this.options.affiche_points || !Object.keys(this.options.json_points).length) {
			jQuery('#'+this._container.id).trigger('ready', this);
		}
	},

	populateTileLayers: function () {
		// Fond de carte par défaut
		if (this.options.default_layer != 'none') {
			var default_layer = this.createTileLayer(this.options.default_layer);
			this.addLayer(default_layer);
		}
		// Fonds de carte supplémentaires
		if (this.options.layersControl && !this.options.noControl && this.options.affiche_layers.length>1) {
			var layers_control = L.control.layers('','',this.options.layersControlOptions);
			if (this.options.default_layer != 'none') {
				layers_control.addBaseLayer(default_layer,this.options.gis_layers[this.options.default_layer].nom);
			}
			for (var l in this.options.affiche_layers) {
				if (this.options.affiche_layers[l]!==this.options.default_layer) {
					var layer = this.createTileLayer(this.options.affiche_layers[l]);
					if (typeof layer!=='undefined')
						layers_control.addBaseLayer(layer,this.options.gis_layers[this.options.affiche_layers[l]].nom);
				}
			}
			this.addControl(layers_control);
			// Ajouter l'objet du controle de layers à la carte pour permettre d'y accéder depuis le callback
			this.layersControl = layers_control;
		}
	},

	initControls: function () {
		this.attributionControl.setPrefix('');
		if (this.options.scaleControl)
			L.control.scale().addTo(this);
		if (this.options.overviewControl && this.options.default_layer != 'none') {
			// todo ajouter une option pour permettre de choisir la couche à afficher dans la minimap
			var minimap_layer = this.createTileLayer(this.options.default_layer);
			L.control.minimap(minimap_layer,{width: 100,height: 100, toggleDisplay: true}).addTo(this);
		}
	},

	createTileLayer: function (name) {
		var layer;
		if (typeof this.options.gis_layers[name]!=='undefined')
			eval('layer=new '+ this.options.gis_layers[name].layer +';');
		return layer;
	},

	// API setGeoJsonFeatureIcon : Pour Ajouter l'icone d'un point (feature = item d'un GeoJson)
	setGeoJsonFeatureIcon: function (feature, layer) {
		// Déclarer l'icone du points, si défini
		if (feature.properties && feature.properties.icon) {
			icon_options = {
				'iconUrl': feature.properties.icon,
				'iconSize': [feature.properties.icon_size[0], feature.properties.icon_size[1]],
				'iconAnchor': [feature.properties.icon_anchor[0], feature.properties.icon_anchor[1]]
			};
			if (feature.properties.popup_anchor)
				icon_options.popupAnchor = [feature.properties.popup_anchor[0], feature.properties.popup_anchor[1]];
			if (feature.properties.shadow)
				icon_options.shadowUrl = feature.properties.shadow;
			if (feature.properties.shadow_size)
				icon_options.shadowSize = [feature.properties.shadow_size[0], feature.properties.shadow_size[1]];
			if (feature.properties.shadow_anchor)
				icon_options.shadowAnchor = [feature.properties.shadow_anchor[0], feature.properties.shadow_anchor[1]];
			layer.setIcon(L.icon(icon_options));
		}
	},

	// API setGeoJsonFeaturePopup : Pour Ajouter le texte de popup d'un point (feature = item d'un GeoJson)
	setGeoJsonFeaturePopup: function (feature, layer) {
		// Déclarer le contenu de la popup s'il y en a
		if (feature.properties
			&& !feature.properties.noclick
			&& (feature.properties.title || feature.properties.description ||
				(this.options.langue && (feature.properties['title_'+this.options.langue] || feature.properties['description_'+this.options.langue])))) {
			var popupContent = '',
				popupOptions = '',
				tooltipContent = false,
				description_ok = false;
			if (this.options.langue) {
				langue = this.options.langue;
				if (feature.properties['title_'+langue]) {
					tooltipContent = feature.properties['title_'+langue];
					popupContent = '<strong class="title">' + feature.properties['title_'+langue] + '</strong>';
				} else if (feature.properties.title) {
					tooltipContent = feature.properties.title;
					popupContent = '<strong class="title">' + feature.properties.title + '</strong>';
				}
				if (feature.properties['description_'+langue]) {
					popupContent = popupContent + feature.properties['description_'+langue];
					description_ok = true;
				}
			} else if (feature.properties.title) {
				tooltipContent = feature.properties.title;
				popupContent = '<strong class="title">' + feature.properties.title + '</strong>';
			}
			if (!description_ok && feature.properties.description)
				popupContent = popupContent + feature.properties.description;
			if (feature.properties.popup_options)
				popupOptions = feature.properties.popup_options;
			layer.bindPopup(popupContent,popupOptions);
			if (this.options.tooltip && tooltipContent) {
				layer.bindTooltip(tooltipContent);
			}
		}
	},

	// Center and zoom or just pan to bounds/point
	centerAndZoom: function (centerOrBounds, panonly) {
		var map = this;
		var options = map.options;
		var maxZoomOriginal = options.maxZoom;
		var bounds = new L.LatLngBounds();
		bounds.extend(centerOrBounds);
		panonly = panonly === undefined ? false : panonly;
		// avoid infinite zoom if bounds focus on a point
		if (bounds._northEast.lat == bounds._southWest.lat && bounds._northEast.lng == bounds._southWest.lng) {
			var singlePoint = true;
			options.maxZoom = options.zoom;
			if (panonly) {
				options.maxZoom = map._zoom;
			}
			bounds._northEast.lat += 0.1;
			bounds._northEast.lng += 0.1;
			bounds._southWest.lat -= 0.1;
			bounds._southWest.lng -= 0.1;
		}
		map.fitBounds(bounds, options);
		map.options.maxZoom = maxZoomOriginal;
		if (options.zoom && singlePoint) {
			map.setZoom(options.zoom);
		}
	},

	// API parseGeoJson
	parseGeoJson: function (data) {
		var map = this;
		// Analyse des points et déclaration (sans regroupement des points en cluster)
		if (!map.options.cluster) {
			this.parseGeoJsonFeatures(data);
		} else {
			map.markerCluster = L.markerClusterGroup(map.options.clusterOptions).addTo(map);
			var markers = [];
			var autres = {
				type: 'FeatureCollection',
				features: []
			};
			/* Pour chaque points présents, on crée un marqueur */
			jQuery.each(data.features, function (i, feature) {
				if (feature.geometry.type == 'Point' && feature.geometry.coordinates[0]) {
					var marker = L.marker([feature.geometry.coordinates[1], feature.geometry.coordinates[0]]);

					// Déclarer l'icone du point
					map.setGeoJsonFeatureIcon(feature, marker);
					// Déclarer le contenu de la popup s'il y en a
					map.setGeoJsonFeaturePopup(feature, marker);

					// On garde en mémoire toute la feature d'origine dans le marker, comme sans clusters
					marker.feature = feature;
					// Pour compat, on continue de garde l'id à part
					marker.id = feature.id;
					markers.push(marker);
				} else {
					autres.features.push(feature);
				}
			});

			map.markerCluster.addLayers(markers);
			this.parseGeoJsonFeatures(autres);

			if (map.options.autocenterandzoom) {
				this.centerAndZoom(map.markerCluster.getBounds());
			}
			if (map.options.openId) {
				gis_focus_marker(map.options.openId,map.options.mapId);
			}
		}
	},

	parseGeoJsonFeatures: function (data) {
		var map = this;
		if (data.features && data.features.length > 0) {
			var geojson = L.geoJson('', {
				style: this.options.pathStyles ? this.options.pathStyles : function (feature) {
					if (feature.properties && feature.properties.styles)
						return feature.properties.styles;
					else
						return '';
				},
				onEachFeature: function (feature, layer) {
					// Déclarer l'icone du point
					if (feature.geometry.type == 'Point') {
						map.setGeoJsonFeatureIcon(feature, layer);
					}
					// Déclarer le contenu de la popup s'il y en a
					map.setGeoJsonFeaturePopup(feature, layer);
				},
				pointToLayer: function (feature, latlng) {
					var alt = 'Marker';
					if (feature.properties.title) {
						alt = feature.properties.title;
					}
				    return L.marker(latlng,{alt: alt});
				}
			}).addData(data).addTo(map);

			if (map.options.autocenterandzoom) {
				this.centerAndZoom(geojson.getBounds());
			}
			if (map.options.openId)
				gis_focus_marker(map.options.openId,map.options.mapId);

			if (typeof map.geojsons=='undefined') map.geojsons = [];
			map.geojsons.push(geojson);
		}
	},
	
	// API Compat GIS3
	addJSON: function (data) {
		return this.parseGeoJson(data);
	},
	
	// API Compat GIS3
	removeAllMarkers: function () {
		// virer les éléments du cluster s'il est utilisé
		if (this.options.cluster) {
			this.markerCluster.clearLayers();
		}
		// virer les points de la carte
		if (typeof this.geojsons=='undefined') this.geojsons = [];
		for (var i in this.geojsons) {
			this.geojsons[i].clearLayers();
			this.removeLayer(this.geojsons[i]);
		}
		this.geojsons = [];
	},
	
	loadData: function () {
		var map = this;
		if (map.options.affiche_points
			&& typeof(map.options.json_points) !== 'undefined'
			&& map.options.json_points.url.length) {
			// Récupération des points à mettre sur la carte, via json externe
			var args = {};
			jQuery.extend(true, args, map.options.json_points.env);
			if (typeof map.options.json_points.objets !== 'undefined') {
				args.objets = map.options.json_points.objets;
				if (args.objets == 'point_libre') {
					args.lat = map.options.center[0];
					args.lon = map.options.center[1];
					if (typeof map.options.json_points.titre !== 'undefined')
						args.titre = map.options.json_points.titre;
					if (typeof map.options.json_points.description !== 'undefined')
						args.description = map.options.json_points.description;
					if (typeof map.options.json_points.icone !== 'undefined')
						args.icone = map.options.json_points.icone;
				}
			}
			if (typeof map.options.json_points.limit !== 'undefined')
				args.limit = map.options.json_points.limit;
			jQuery.getJSON(map.options.json_points.url,args,
				function (data) {
					if (data) {
						// Charger le json (data) et déclarer les points
						map.parseGeoJson(data);
						jQuery('#'+map._container.id).trigger('ready',map);
					}
				}
			);
		}
	},
	
	addOverlays: function () {
		var map = this;
		if (map.options.kml && map.options.kml.length) {
			map.kml = {};
			for (var i in map.options.kml) {
				map.kml[i] = new L.KML(map.options.kml[i], {async: true});
				if (map.options.centrer_fichier)
					map.kml[i].on('loaded', function (e) { map.fitBounds(e.target.getBounds()); });
				map.addLayer(map.kml[i]);
			}
		}
		if (map.options.gpx && map.options.gpx.length) {
			map.gpx = {};
			for (var i in map.options.gpx) {
				map.gpx[i] = new L.GPX(map.options.gpx[i], {async: true});
				if (map.options.centrer_fichier)
					map.gpx[i].on('loaded', function (e) { map.fitBounds(e.target.getBounds()); });
				map.addLayer(map.gpx[i]);
			}
		}
		if (map.options.geojson && map.options.geojson.length) {
			for (var i in map.options.geojson) {
				jQuery.getJSON(map.options.geojson[i], function (data) {
					if (data)
						map.parseGeoJson(data);
				});
			}
		}
		if (map.options.topojson && map.options.topojson.length) {
			map.topojson = {};
			for (var i in map.options.topojson) {
				map.topojson[i] = new L.TOPOJSON(map.options.topojson[i], {async: true});
				if (map.options.centrer_fichier) {
					map.topojson[i].on('loaded', function (e) { map.fitBounds(e.target.getBounds()); });
				}
				map.addLayer(map.topojson[i]);
			}
		}
	}
});

L.map.gis = function (id, options) {
	return new L.Map.Gis(id, options);
};

})();
