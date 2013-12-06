/*
 *	Carto / WTFPL 2.0
 *	-----------------
 *	- affiche toutes les cameras par defaut
 *	- si "id_camera" : met cette camera en valeur, les autres en transparent
 *		- si "edition" :  fonctions d'edition nécessite le chargement de
 *		   carto.Formulaire et carto.Formulaire_[privé/public]	
 */

window.carto = {};

carto.Carte = function(id, opts){
	"use strict";
	
	var map, // objet carte leaflet
		mapMode, // mode de la carte (navigation, drag, pointage..)
		calquePrincipal, // calque de visualisation
		calqueSelection,	 // calque de mise en avant & edition
		clusterer
		 = null;
	
	var fondsDeCarte = {}; // fonds de carte (MapQuest, Google sat...)
	var couchesOpt = {}; // couches optionelles
	
	var lat = ( $.cookie('spip_carto_lat') != null ) ? $.cookie('spip_carto_lat') : 45.75;
	var lon = ( $.cookie('spip_carto_lon') != null ) ? $.cookie('spip_carto_lon') : 4.85;
	var zoom = ( $.cookie('spip_carto_zoom') != null ) ? $.cookie('spip_carto_zoom') : 13;
	
	if( opts.position ){
		lat = opts.position.lat;
		lon = opts.position.lon;
		zoom = opts.position.zoom;
	}
	
	var attrib = {
		osm : '<a href="http://www.openstreetmap.org/" target="_blank" title="Cartographie par la communauté OpenStreetMaps">OSM</a>, <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank" title="Publié sous licence Creative Commons Attribution-ShareAlike 2.0">CC-by-SA</a>',
		mapquest : '<a href="http://www.mapquest.com/" target="_blank" title="Fond de carte par MapQuest">MapQuest</a>',
		leaflet : 'moteur: <a href="http://leaflet.cloudmade.com">Leaflet</a>', // à priori optionnel, on va pas non plus citer toute la communauté libre direct sur la carte..
		sep : ' / ', // separateur
	}
		
	//---------------------------------------------------------------
	//	Fonctions de construction
	//---------------------------------------------------------------
	
	init();
	
	// initialisation globale
	function init()
	{
		map = new L.Map(id, { attributionControl: false });
		map.setView( new L.LatLng( lat, lon), zoom );
		
		if (!opts.id_camera) map.addControl( new L.Control.Attribution({ prefix:''}) );
		
		setupCluster();
		setupFondDeCarte();
		setupCalquePrincipal();
		setDefaultMapMode();
		if (opts.id_camera != "") setupCalqueSelection();
		
		if (opts.id_camera == "" && opts.visiteur_enregistre) map.addControl( new L.Control.Button({
			title: "Ajouter une caméra",
			url: ( opts.visiteur_enregistre ) ?
				opts.chemin_site+'/ecrire/?exec=camera_edit&new=oui'
					: 'http://carto.rebellyon.info/spip.php?article20'
		}) );
		var controls = map.addControl( new L.Control.Carto( fondsDeCarte, couchesOpt ));
		
		// enregistrement de la vue courante > cookie
		map.on('moveend', sauvegarderVueCourante );
		sauvegarderVueCourante();
	}
	
	// fonds de carte, pour l'instant uniquement mapquest, p-ê ajouter Google sat
	function setupFondDeCarte()
	{
		var mapquest = new L.TileLayer('http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',{
			maxZoom: 18,
			subdomains: ['otile1','otile2','otile3','otile4'],
			attribution: attrib.mapquest+attrib.sep+attrib.osm
		});
		map.addLayer(mapquest); // par default
		fondsDeCarte["Mapquest"] = mapquest;
		
		var toner = new L.StamenTileLayer("toner");
		fondsDeCarte["Toner"] = toner;

		var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: attrib.osm
		});
		fondsDeCarte["OSM / Mapnik"] = osm;
		
		var googlesat = new L.Google('SATELLITE'/*, {maxZoom: 19}*/);
		fondsDeCarte["Google"] = googlesat;

		//if(opts.visiteur_enregistre){
			var zones = new L.TileLayer( opts.chemin_pages+'cameras_tiles&type=openstreetmap.org&z={z}&x={x}&y={y}', {//&type=http://a.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: attrib.osm,
				opacity: 0.7
			});
			couchesOpt["Zones surveillées"] = zones;
			/*
			var zones_hit = new L.TileLayer( opts.chemin_pages+'cameras_tiles&type=openstreetmap.org-hit&z={z}&x={x}&y={y}', {//&type=http://a.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: attrib.osm,
				opacity: 1
			});
			couchesOpt["zones obstacles (debug)"] = zones_hit;*/
			
			
		//}
	}
	
	// calque principal ou sont ajoutées la pluspart des POI
	function setupCalquePrincipal()
	{
		calquePrincipal = new L.GeoJSON(null, {
			pointToLayer: function (latlng) {
				var marker = new L.Marker.Compass(latlng);
				return marker;
			}
		});

		calquePrincipal.on('featureparse', function(e) {
			e.layer.properties = e.properties;
			traitementVisuelCamera(e);
			
			// Si un POI est selectionné
		    if( e.layer.setIcon && e.properties && opts.id_camera != ""){
				// on baisse l'opacité du marqueur
				e.layer.setMarkerOpacity(.5);
			// Sinon
			}else{
				if (e.properties && e.properties.id_camera){
					// au clic, on affiche une popup et charge les infos complémentaires
					e.layer.on('click', function(e){
						var popup = new L.Popup();
						popup.setLatLng( this.getLatLng() );
						popup.setContent('<img src="'+opts.chemin_plugin+'img/ajax_load.gif" height="24" width="24"/>');
						map.openPopup(popup);
						$.get(
							opts.chemin_pages+'camera_popup&id_camera='+this.properties.id_camera+'&lang='+opts.lang,
							function( data ) { popup.setContent( data ); }
						);
					});
				}
			}
		});

		var urlPoints = opts.chemin_pages+'cameras&format=json&details=2&lang='+opts.lang;
		if (opts.id_camera != "")
			urlPoints += '&exclure='+opts.id_camera;
		
		$.getJSON(urlPoints, function(data) {
			calquePrincipal.addGeoJSON(data);
			fillClusterer();
		});
		//calquePrincipal.addGeoJSON(opts.jsonPrincipal);
		
		/* cams OSM */
		if(opts.visiteur_enregistre){
			var osm_cams = new L.GeoJSON(null, {
				pointToLayer: function (latlng) {
					//alert('hello');
					var marker = new L.Marker.Compass(latlng);
					return marker;
				}
			});
		
			var urlPoints = opts.chemin_pages+'cameras_tmp_cams_osm_lyon';
			$.get(urlPoints, null, function(data) {
					var geojson = osm2geo(data);
					osm_cams.addGeoJSON( geojson );
			});
			couchesOpt["Caméras OSM"] = osm_cams;
		}
		
		fillClusterer();
	}

	// calque de selection qui sert à isoler un élement à mettre en valeur ou editer
	function setupCalqueSelection()
	{
		calqueSelection = new L.GeoJSON(null,{
			pointToLayer: function (latlng) {
				var marker = new L.Marker.Compass(latlng);
				marker.on('dragend', onDragEnd);
				return marker;
			}
		});

		calqueSelection.on( 'featureparse', function(e){
			e.layer.properties = e.properties;
			traitementVisuelCamera(e);
		});

		var urlPoints = opts.chemin_pages+'cameras&format=json&details=2&id_camera='+opts.id_camera;
		$.getJSON(urlPoints, function(data) {
			calqueSelection.addGeoJSON(data);
		});
		
		map.addLayer( calqueSelection );
	}
	
	function setupCluster(){
		
		clusterer = new LeafClusterer(map, [], {gridSize: 60, maxZoom: 16, styles: 
			[{
			    url: opts.chemin_plugin+'img/carte/clusters/kc1.png',
			    height: 37,
			    width: 37,
			    anchor: [18, 0],
			    textColor: '#000000',
			    textSize: 10
			}, {
			    url: opts.chemin_plugin+'img/carte/clusters/kc2.png',
			    height: 45,
			    width: 45,
			    anchor: [23, 0],
			    textColor: '#000000',
			    textSize: 11
			}, {
			    url: opts.chemin_plugin+'img/carte/clusters/kc3.png',
			    height: 54,
			    width: 54,
			    anchor: [27, 0],
			    textColor: '#000000',
			    textSize: 12
			}]
		});
	}
	
	function fillClusterer(){
		var markers=[];
		for (var x in calquePrincipal._layers){
		    markers.push(calquePrincipal._layers[x]);
		}
		clusterer.addMarkers(markers);
	}
	
	//---------------------------------------------------------------
	//	Fonctions et variables communes (styles, abstraction)
	//---------------------------------------------------------------
	
	var CamIcon = L.Icon.extend({ options : {
		iconUrl: opts.chemin_plugin+'img/carte/icones/nue.png',
		shadowUrl: opts.chemin_plugin+'img/carte/icones/vues/nue.png',
		iconSize: new L.Point(24, 24),
		shadowSize: new L.Point(64, 64)
	}});

	var CamDomeIcon = new CamIcon({
		iconUrl: opts.chemin_plugin+'img/carte/icones/dome.png',
		shadowUrl: opts.chemin_plugin+'img/carte/icones/vues/dome.png'
	});
	
	// appelé qd parse JSON
	function traitementVisuelCamera(e){
		visuelCamera(e.layer, e.properties);
	}
	
	function visuelCamera(marker, properties){
		//console.log('visuel camera');
		if (marker.setIcon && properties)
		{
			var baseDir = opts.chemin_plugin+'img/carte/marqueurs/';
			var iconDir = 'icones/';
			var vueDir = 'vues/';
			var icn = "nue";
			
			switch(properties.apparence){
				case'dome':
					if( (properties.angle > 45) && (properties.angle < 135) ) {
						icn = 'dome-vert';
					}else{
						icn = 'dome';
					}
					break;
				case'encastre': icn = 'encastre'; break;
				case'boite': icn = 'boite'; break;
				case'radar': icn = 'radar'; break;
				default: icn = 'nue'; break;
			}
			
			if(properties.statut_spip){
				switch(properties.statut_spip){
					case'publie': iconDir = 'icones-publie/'; break;
					case'prepa': iconDir = 'icones-prepa/'; break;
					case'prop': iconDir = 'icones-prop/'; break;
				}
			}
			
			if (properties.op_type && properties.op_type == "private") vueDir = 'vues-prive/';
			
			
			marker.setIcon( new CamIcon({
				iconUrl: baseDir+iconDir+icn+'.png'
				, shadowUrl: baseDir+vueDir+icn+'.png'
			}) );
			marker.setIconAngle( (properties.direction) ? parseFloat(properties.direction)-90 : -90 );
			marker.setShadowAngle( (properties.direction) ? parseFloat(properties.direction)-90 : -90 );
		}
	}
	
	//---------------------------------------------------------------
	//	Evenements
	//---------------------------------------------------------------
	
	function onDragEnd(e){
		//console.log("dragEnd"+e.target.getLatLng().toSource() );
		setFeatureAttribute("latlon", e.target.getLatLng() );
		api.onFeatureUpdate( "latlon", e.target.getLatLng() );
	}
	
	function onPointPos(e){
		setFeatureAttribute("latlon", e.latlng);
		api.onFeatureUpdate( "latlon", e.latlng );
	}
	
	//---------------------------------------------------------------
	//	Fonctions utilitaires
	//---------------------------------------------------------------
	
	
	function sauvegarderVueCourante(){
		$.cookie('spip_carto_lat', map.getCenter().lat );
		$.cookie('spip_carto_lon', map.getCenter().lng );
		$.cookie('spip_carto_zoom', map.getZoom() );
	}
	
	function getSelectedFeature(){
		if(calqueSelection && calqueSelection._layers && firstField(calqueSelection._layers).val){
			return firstField(calqueSelection._layers).val;
		}else{
			return null;
		}
	}
	
	function getSelectedFeatureId(){
		return firstField(calqueSelection._layers).key;
	}
	
	// renvoie la premier champ d'un objet
	function firstField(obj){
		
		for (var i in obj)
		    if (obj.hasOwnProperty(i) && typeof(i) !== 'function')
		        break;
		
		return {key:i, val:obj[i]};
	}
	
	// initialise la mode de la carte
	function setDefaultMapMode(){
		// desactive drag
		var marker = getSelectedFeature();
		if(marker && marker.dragging){
			marker.dragging.disable();
			marker.options.draggable = false;
		}
		// desactive pointeur
		map.off("click", onPointPos);
		mapMode = "nav";
	}
	
	// modifie le mode de la carte (navigation, déplacement/pointage de POI..
	function setMapMode(mode, callback){
		console.log('setMapMode : '+mode);
		
		setDefaultMapMode();
		
		if(mode){
			switch(mode){
				case "point":
					map.on("click", onPointPos );
					mapMode = "point";
					break;
				
				case "drag":
					var marker = getSelectedFeature();
					if(marker && marker.dragging){
						marker.dragging.enable()
						marker.options.draggable = true;
					}
				
				case "default":
				default:
					mapMode = "nav";
					break;
			}
		}
		
		//if(callback && callback==true) api.onMapModeUpdate( mapMode );
	}
	
	// modifie l'attribut d'un marquer (et met à jour sa visualisation)
	function setFeatureAttribute(attr, val, callback){
		console.log('setFeatureAttribute('+attr+','+val+')');
		
		var marker = getSelectedFeature();
		
		if (marker == null){
			//alert("marker null");
			marker = new L.Marker.Compass(map.getCenter());
			marker.on('dragend', onDragEnd);
			marker.properties ={};
			visuelCamera(marker, {});
			calqueSelection.addLayer(marker);
		}
		
		switch(attr){
			case "direction":
				marker.properties.direction = val;
				visuelCamera(marker, marker.properties);
				break;
			
			case "angle":
				marker.properties.angle = val;
				visuelCamera(marker, marker.properties);
				break;
				
			case "latlon":
				marker.setLatLng(val);
				break;
			
			case "apparence":
				marker.properties.apparence = val;
				visuelCamera(marker, marker.properties);
				break;
				
			case "op_type":
				marker.properties.op_type = val;
				visuelCamera(marker, marker.properties);
				break;
			
			// verifier toString(old_val) != toString (new_val)
			// pour eviter les boucles IO
			
			default:
				break;
		}
		
		//if(callback && callback==true) api.onFeatureAttributeUpdate( mapMode );
	}
	
	
	//---------------------------------------------------------------
	//	Pied
	//---------------------------------------------------------------
	
	// methodes publiques
	var api = {
		getContainerId: function(){ return id },
		setMapMode: function(mode){ setMapMode(mode) },
		onMapModeUpdate: function(mode){ console.log("onMapModeUpdate peut être écouté!"); },
		setFeatureAttribute: function(attr, val){ setFeatureAttribute(attr, val) },
		onFeatureAttributeUpdate: function(attr, val){ console.log("onFeatureUpdate peut être écouté!"); }
	}
	
	return api;

};

