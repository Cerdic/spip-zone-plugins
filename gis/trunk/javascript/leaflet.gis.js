(function() {
// Plugin Leaflet L.Map.Gis
L.Map.Gis = L.Map.extend({
	
	includes: L.Mixin.Events,
	
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
		cluster: false,
		clusterOptions: {
			disableClusteringAtZoom: 0,
			showCoverageOnHover: false,
			maxClusterRadius: 80
		},
		pathStyles: null,
		autocenterandzoom: false,
		openId: false,
		affiche_points: true,
		json_points: {
			url: "",
			objets: "",
			limit: 500,
			env: [],
			titre: "",
			description: "",
			icone: ""
		},
		localize_visitor: false,
		localize_visitor_zoom: 0,
		centrer_fichier: true,
		kml: false,
		gpx: false,
		geojson: false
	},
	
	initialize: function (id,options) {
		L.Util.setOptions(this, options);
		
		this.on('load',function(e){
			// Affecter sur l'objet du DOM
			jQuery("#"+this._container.id).get(0).map = this;
			// Appeler l'éventuelle fonction de callback
			if (this.options.callback && typeof(this.options.callback) === "function")
				this.options.callback(this);
			// trigger load sur l'objet du DOM
			jQuery("#"+this._container.id).trigger('load',this);
		});
		
		L.Map.prototype.initialize.call(this, id, options);
		
		if (this.options.utiliser_bb){
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
		
		if (this.options.localize_visitor){
			var maxZoom = this.options.localize_visitor_zoom;
			this.on('locationerror',function(e){
				maxZoom = this.options.zoom;
				alert(e.message);
			});
			this.locate({setView: true, maxZoom: maxZoom});
		}
		
		// Si pas de points affichés trigger ready ici
		if (!this.options.affiche_points || !this.options.json_points.length)
			jQuery("#"+this._container.id).trigger('ready',this);
	},

	populateTileLayers: function (tilelayers) {
		// Fond de carte par défaut
		var default_layer = this.createTileLayer(this.options.default_layer);
		this.addLayer(default_layer);
		// Fonds de carte supplémentaires
		if (this.options.layersControl && !this.options.noControl && this.options.affiche_layers.length>1){
			var layers_control = L.control.layers('','',this.options.layersControlOptions);
			layers_control.addBaseLayer(default_layer,this.options.gis_layers[this.options.default_layer].nom);
			for(var l in this.options.affiche_layers){
				if (this.options.affiche_layers[l]!==this.options.default_layer){
					var layer = this.createTileLayer(this.options.affiche_layers[l]);
					if (typeof layer!=="undefined")
						layers_control.addBaseLayer(layer,this.options.gis_layers[this.options.affiche_layers[l]].nom);
				}
			}
			this.addControl(layers_control);
			// Ajouter l'objet du controle de layers à la carte pour permettre d'y accéder depuis le callback
			this.layersControl = layers_control;
			// Classe noajax sur le layer_control pour éviter l'ajout de hidden par SPIP
			L.DomUtil.addClass(layers_control._form,'noajax');
		}
	},

	initControls: function () {
		this.attributionControl.setPrefix('');
		if (this.options.scaleControl)
			L.control.scale().addTo(this);
		if (this.options.overviewControl){
			// todo ajouter une option pour permettre de choisir la couche à afficher dans la minimap
			var minimap_layer = this.createTileLayer(this.options.default_layer);
			L.control.minimap(minimap_layer,{width: 100,height: 100, toggleDisplay: true}).addTo(this);
		}
	},
	
	createTileLayer: function (name) {
		var layer;
		if (typeof this.options.gis_layers[name]!=="undefined")
			eval("layer=new "+ this.options.gis_layers[name].layer +";");
		return layer;
	},

	// API setGeoJsonFeatureIcon : Pour Ajouter l'icone d'un point (feature = item d'un GeoJson)
	setGeoJsonFeatureIcon: function (feature, layer) {
		// Déclarer l'icone du points, si défini
		if (feature.properties && feature.properties.icon){
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
			layer.setIcon(L.icon(icon_options));
		}
	},
	
	// API setGeoJsonFeaturePopup : Pour Ajouter le texte de popup d'un point (feature = item d'un GeoJson)
	setGeoJsonFeaturePopup: function (feature, layer) {
		// Déclarer le contenu de la popup s'il y en a
		if (feature.properties && (feature.properties.title || feature.properties.description)){
			var popupContent = '';
			var popupOptions = '';
			if (feature.properties.title)
				popupContent = '<strong class="title">' + feature.properties.title + '</strong>';
			if (feature.properties.description)
				popupContent = popupContent + feature.properties.description;
			if (feature.properties.popup_options)
				popupOptions = feature.properties.popup_options;
			layer.bindPopup(popupContent,popupOptions);
		}
	},
	
	// API parseGeoJson
	parseGeoJson: function(data) {
		var map = this;
		// Analyse des points et déclaration (sans regroupement des points en cluster)
		if (!map.options.cluster){
			if (data.features.length > 0){
				var geojson = L.geoJson('', {
					style: this.options.pathStyles,
					onEachFeature: function (feature, layer) {
						// Déclarer l'icone du point
						map.setGeoJsonFeatureIcon(feature, layer);
						// Déclarer le contenu de la popup s'il y en a
						map.setGeoJsonFeaturePopup(feature, layer);
					}
				}).addData(data).addTo(map);
				
				if (map.options.autocenterandzoom){
					if (data.features.length == 1 && data.features[0].geometry.type == 'Point')
						map.setView(geojson.getBounds().getCenter(), map.options.zoom);
					else
						map.fitBounds(geojson.getBounds());
				}
				if (map.options.openId)
					gis_focus_marker(map.options.openId,map.options.mapId);

				if (typeof map.geojsons=="undefined") map.geojsons = [];
				map.geojsons.push(geojson);
			}
		} else {
			map.markers = L.markerClusterGroup(map.options.clusterOptions);

			/* Pour chaque points présents, on crée un marqueur */
			jQuery.each(data.features, function(i, feature){
				if (feature.geometry.coordinates[0]){
					var marker = L.marker([feature.geometry.coordinates[1], feature.geometry.coordinates[0]]);

					// Déclarer l'icone du point
					map.setGeoJsonFeatureIcon(feature, marker);
					// Déclarer le contenu de la popup s'il y en a
					map.setGeoJsonFeaturePopup(feature, marker);

					marker.id = feature.id;
					map.markers.addLayer(marker);
				}
			});

			map.addLayer(map.markers);

			if (map.options.autocenterandzoom){
				if (data.features.length > 1)
					map.fitBounds(map.markers.getBounds());
				else
					map.setView(map.markers.getBounds().getCenter(), map.options.zoom);
			}
		}
	},
	
	// API Compat GIS3
	addJSON: function(data) {
		return this.parseGeoJson(data);
	},
	
	// API Compat GIS3
	removeAllMarkers: function(){
		if (typeof this.geojsons=="undefined") this.geojsons = [];
		for(var i in this.geojsons){
			this.geojsons[i].clearLayers();
			this.removeLayer(this.geojsons[i]);
		}
		this.geojsons = [];
	},
	
	loadData: function () {
		var map = this;
		if (map.options.affiche_points
			&& typeof(map.options.json_points) !== "undefined"
			&& map.options.json_points.url.length){
			// Récupération des points à mettre sur la carte, via json externe
			var args = {};
			jQuery.extend(true, args, map.options.json_points.env);
			if (typeof map.options.json_points.objets !== "undefined"){
				args.objets = map.options.json_points.objets;
				// FIXME !
				if (args.objets == "point_libre"){
					args.lat = mapcfg.lat;
					args.lon = mapcfg.lon;
					if (typeof mapcfg.json_points.titre !== "undefined")
						args.titre = mapcfg.json_points.titre;
					if (typeof mapcfg.json_points.description !== "undefined")
						args.description = mapcfg.json_points.description;
					if (typeof mapcfg.json_points.icone !== "undefined")
						args.icone = mapcfg.json_points.icone;
				}
			}
			if (typeof map.options.json_points.limit !== "undefined")
				args.limit = map.options.json_points.limit;
			jQuery.getJSON(map.options.json_points.url,args,
				function(data) {
					if (data){
						// Charger le json (data) et déclarer les points
						map.parseGeoJson(data);
						jQuery("#"+map._container.id).trigger('ready',map);
					}
				}
			);
		}
	},
	
	addOverlays: function () {
		var map = this;
		if (map.options.kml && map.options.kml.length){
			map.kml = {};
			for(var i in map.options.kml){
				map.kml[i] = new L.KML(map.options.kml[i], {async: true});
				if (map.options.centrer_fichier)
					map.kml[i].on("loaded", function(e) { map.fitBounds(e.target.getBounds()); });
				map.addLayer(map.kml[i]);
			}
		}
		if (map.options.gpx && map.options.gpx.length){
			map.gpx = {};
			for(var i in map.options.gpx){
				map.gpx[i] = new L.GPX(map.options.gpx[i], {async: true});
				if (map.options.centrer_fichier)
					map.gpx[i].on("loaded", function(e) { map.fitBounds(e.target.getBounds()); });
				map.addLayer(map.gpx[i]);
			}
		}
		if (map.options.geojson && map.options.geojson.length){
			for(var i in map.options.geojson){
				jQuery.getJSON(map.options.geojson[i], function(data){
					if (data)
						map.parseGeoJson(data);
				});
			}
		}
	}
});

L.map.gis = function (id, options) {
	return new L.Map.Gis(id, options);
};

})();