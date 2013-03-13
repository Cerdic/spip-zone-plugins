var gis_init_map = function(mapcfg) {
	var map_container = mapcfg["mapid"];

	// Création de la carte Leafleat
	var map = new L.Map(map_container,{
		scrollWheelZoom: mapcfg["scrollWheelZoom"],
		zoomControl: mapcfg["zoomControl"],
		maxZoom: mapcfg["maxZoom"]
	});
	// affecter sur la globale homonyme a mapid/map_container (compat ascendante)
	eval(map_container+"=map;");
	// affecter sur l'objet du DOM
	jQuery("#"+map_container).get(0).map=map;

	// Appeler l'éventuelle fonction de callback et trigger "load"
	map.on('load',function(e){
		if (mapcfg["callback"] && typeof(mapcfg["callback"]) === "function") {
			var callback = mapcfg["callback"];
			callback(e.target);
		}
		jQuery("#"+map_container).trigger('load',e.target);
	});

	// Déterminer la position initiale de la carte
	if (!mapcfg['utiliser_bb']){
		map.setView(new L.LatLng(mapcfg['lat'], mapcfg['lon']), mapcfg['zoom']);
	}
	else {
		map.fitBounds(
			new L.LatLngBounds(
				new L.LatLng(mapcfg['sw_lat'], mapcfg['sw_lon']),
				new L.LatLng(mapcfg['ne_lat'], mapcfg['ne_lon'])
			)
		);
	}

	var get_layer=function(name){
		var layer;
		if (typeof mapcfg['layers'][name]!=="undefined")
		eval("layer=new "+ mapcfg['layers'][name]["layer"]+";");
		return layer;
	}

	// Fond de carte par défaut (layer)
	var default_layer = get_layer(mapcfg['default_layer']);
	map.addLayer(default_layer);

	if (mapcfg['control_type'] && !mapcfg['no_control'] && mapcfg['affiche_layers'].length>1){
		var layers_control = new L.Control.Layers();
		layers_control.addBaseLayer(default_layer,mapcfg['layers'][mapcfg['default_layer']]["nom"]);
		for(var l in mapcfg['affiche_layers']){
			if (mapcfg['affiche_layers'][l]!==mapcfg['default_layer']){
				var layer = get_layer(mapcfg['affiche_layers'][l]);
				if (typeof layer!=="undefined")
					layers_control.addBaseLayer(layer,mapcfg['layers'][mapcfg['affiche_layers'][l]]["nom"]);
			}
		}
		map.addControl(layers_control);
		// ajouter l'objet du controle de layers à la carte pour permettre d'y accéder depuis le callback
		map.layersControl = layers_control;
		// classe noajax sur le layer_control pour éviter l'ajout de hidden par SPIP
		jQuery(layers_control._form).addClass('noajax');
	}

	map.setView(new L.LatLng(mapcfg['lat'],mapcfg['lon']),mapcfg['zoom']);
	map.attributionControl.setPrefix('');

	// Ajout des contrôles de la carte
	if (!mapcfg['no_control']){
		if (mapcfg['scale'])
			map.addControl(new L.Control.Scale());
		if (mapcfg['fullscreen'])
			map.addControl(new L.Control.FullScreen());
		if (mapcfg['overview']){
			var minimap_layer = get_layer(mapcfg['default_layer']);
			var miniMap = new L.Control.MiniMap(minimap_layer,{width: 100,height: 100}).addTo(map);
		}
	}

	// API setGeoJsonFeatureIcon : Pour Ajouter l'icone d'un point (feature = item d'un GeoJson)
	map.setGeoJsonFeatureIcon = function (feature, layer) {
		// Déclarer l'icone du points, si défini
		if (feature.properties && feature.properties.icon){
			icon_options = {
				'iconUrl': feature.properties.icon,
				'iconSize': new L.Point( feature.properties.icon_size[0], feature.properties.icon_size[1] ),
				'iconAnchor': new L.Point( feature.properties.icon_anchor[0], feature.properties.icon_anchor[1] ),
				'popupAnchor': new L.Point( feature.properties.popup_anchor[0], feature.properties.popup_anchor[1] )
			};
			if (feature.properties.shadow)
				icon_options.shadowUrl = feature.properties.shadow;
			if (feature.properties.shadow_size)
				icon_options.shadowSize = new L.Point( feature.properties.shadow_size[0], feature.properties.shadow_size[1] );
			layer.setIcon(new L.Icon(icon_options));
		}
	}

	// API setGeoJsonFeaturePopup : Pour Ajouter le texte de popup d'un point (feature = item d'un GeoJson)
	map.setGeoJsonFeaturePopup = function (feature, layer) {
		// Déclarer le contenu de la popup s'il y en a
		if (feature.properties && (feature.properties.title || feature.properties.description)){
			var popupContent = '';
			if (feature.properties.title)
				popupContent = '<strong class="title">' + feature.properties.title + '</strong>';
			if (feature.properties.description)
				popupContent = popupContent + feature.properties.description;
			layer.bindPopup(popupContent);
		}
	}

	/*
		Il y a pour le moment 2 façons d'analyser le GeoJson calculé
		en fonction de si on veut faire du clustering (regrouper les points proches)
		ou non. Il y a certainement moyen de regrouper en un seul élément
		la plupart du code, en se passant du js L.geoJson même hors clustering.
		À réfléchir.
	*/
	// API parseGeoJson
	if (!mapcfg['cluster']){
		// Analyse des points et déclaration (sans regroupement des points en cluster)
		map.parseGeoJson = function(data) {
			if (data.features.length > 0) {
				var geojson = new L.geoJson('', {
					style: mapcfg['path_styles'],
					onEachFeature: function (feature, layer) {
						// Déclarer l'icone du point
						map.setGeoJsonFeatureIcon(feature, layer);
						// Déclarer le contenu de la popup s'il y en a
						map.setGeoJsonFeaturePopup(feature, layer);
					}
				}).addTo(map);
				geojson.addData(data);
				if (mapcfg['autocenterandzoom'])
					map.fitBounds(geojson.getBounds());
				if (mapcfg['open_id'].length)
					gis_focus_marker(mapcfg['open_id'],map_container);

				if (typeof map.geojsons=="undefined") map.geojsons = [];
				map.geojsons.push(geojson);
			}
		}
	}
	else {
		// Analyse des points et déclaration (en regroupant les points en cluster)
		map.parseGeoJson = function(data) {
			map.markers = [];
			/* Pour chaque points présents, on crée un marqueur */
			$.each(data.features, function(i, feature) {
				if (feature.geometry.coordinates[0]) {
					var latlng = new L.LatLng(feature.geometry.coordinates[1], feature.geometry.coordinates[0]);
					var marker = new L.Marker(latlng);
					// Déclarer l'icone du point
					map.setGeoJsonFeatureIcon(feature, marker);
					// Déclarer le contenu de la popup s'il y en a
					map.setGeoJsonFeaturePopup(feature, marker);

					marker.id = feature.id;
					map.markers.push(marker);
				}
			});
			/* Ajout des marqueurs dans un clustering JS client */
			var options = {};
			if (mapcfg["clusterMaxZoom"])
				options["maxZoom"] = mapcfg["clusterMaxZoom"];
			if (mapcfg["clusterStyles"].length)
				options["styles"] = mapcfg["clusterStyles"];
			map.markerCluster = new L.Marker.Clusterer(map, markers, options);
		}
	}

	// API Compat Gis3 : addJSON et removeAllMarkers
	map.addJSON = map.parseGeoJson
	map.removeAllMarkers = function(){
		if (typeof map.geojsons=="undefined") map.geojsons = [];
		for(i in map.geojsons){
			map.geojsons[i].clearLayers();
			map.removeLayer(map.geojsons[i]);
		}
		map.geojsons = [];
	}

	if (mapcfg['affiche_points']
		&& typeof(mapcfg['json_points'])!=="undefined"
		&& mapcfg['json_points']['url'].length){
		// Récupération des points à mettre sur la carte, via json externe
		var args = {};
		jQuery.extend(true, args, mapcfg['json_points']['env']);
		if (typeof mapcfg['json_points']['objets']!=="undefined"){
			args["objets"] = mapcfg['json_points']['objets'];
			if (args["objets"]=="point_libre"){
				args["lat"]=mapcfg['lat'];
				args["lon"]=mapcfg['lon'];
				if (typeof mapcfg['json_points']['titre']!=="undefined")
					args["titre"]= mapcfg['json_points']['titre'];
				if (typeof mapcfg['json_points']['description']!=="undefined")
					args["description"]=mapcfg['json_points']['description'];
				if (typeof mapcfg['json_points']['icone']!=="undefined")
					args["icone"]=mapcfg['json_points']['icone'];
			}
		}
		if (typeof mapcfg['json_points']['limit']!=="undefined")
			args["limit"] = mapcfg['json_points']['limit'];
		jQuery.getJSON(mapcfg['json_points']['url'],args,
			function(data) {
				if (data){
					// Charger le json (data) et déclarer les points
					map.parseGeoJson(data);
					jQuery("#"+map_container).trigger('ready',map);
				}
			}
		);
	}

	if (mapcfg['kml'] && mapcfg['kml'].length){
		map.kml = {};
		for(var i in mapcfg['kml']){
			map.kml[i] = new L.KML(mapcfg['kml'][i], {async: true});
			map.kml[i].on("loaded", function(e) { map.fitBounds(e.target.getBounds()); });
			map.addLayer(map.kml[i]);
		}
	}
	if (mapcfg['gpx'] && mapcfg['gpx'].length){
		map.gpx = {};
		for(var i in mapcfg['gpx']){
			map.gpx[i] = new L.GPX(mapcfg['gpx'][i], {async: true});
			map.gpx[i].on("loaded", function(e) { map.fitBounds(e.target.getBounds()); });
			map.addLayer(map.gpx[i]);
		}
	}

	if (mapcfg['localize_visitor'])
		map.locate({setView: true, maxZoom: mapcfg['zoom']});

	// si pas de points trigger ici
	if (!mapcfg['affiche_points'] || !mapcfg['json_points'].length)
		jQuery("#"+map_container).trigger('ready',map);
}