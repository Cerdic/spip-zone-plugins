/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */
	
//// Définition d'un objet pour contenir les données d'une carte

// Définition d'un objet qui recevra des paramètres spécifiques au site
var SiteInfo = new Object();

// Tableau des cartes
MapWrapper.maps = new Array();

// Définition de la classe Map
function MapWrapper(name)
{
	this.name = name;			// nom de la carte
	
	this.curParams = {};
	this.map = null;			// Objet Mapstraction
	this.div = null;			// Objet jQuery autour de la DIV qui contient la carte
	this.bSizeInited = false;	// Initialisation de la taille
	
	this.markers = new Array();	// liste des marqueurs
	this.nextMarkerID = 1;		// ID du prochain marqueur
	this.layers = new Array();	// liste des fichiers de couche (KML)
	this.nextLayerID = 1;		// ID de la prochaine couche KML
	this.icons = new Array();	// liste des icônes définies
	this.infoWindow = null;		// marqueur sur lequel la fenêtre est ouverte
	
	// Mécanisme de listeners
	this.listeners = new Array();
};

// Récupérer ou créer une carte
MapWrapper.getMap = function(name, bCreate)
{
	if (!isObject(MapWrapper.maps[name]))
	{
		if (bCreate)
			MapWrapper.maps[name] = new MapWrapper(name);
		else
			return null;
	}
	return MapWrapper.maps[name];
}

// Suppression d'une carte
MapWrapper.freeMap = function(name)
{
	if (isObject(MapWrapper.maps[name]))
	{
		MapWrapper.maps[name].unload();
		MapWrapper.maps[name] = null;
		delete MapWrapper.maps[name];
	}
}

// Suppression de toutes les cartes
MapWrapper.freeAllMaps = function(name)
{
	for (name in MapWrapper.maps)
	{
		MapWrapper.maps[name].unload();
		MapWrapper.maps[name] = null;
		delete MapWrapper.maps[name];
	}
	MapWrapper.maps = new Array();
}

// Accesseurs rapides "à la" jQuery
gMap = function(name)
{
	if (!isObject(MapWrapper.maps))
		return null;
	return MapWrapper.getMap(name, false);
};


// Définition des paramètres de la carte
// Ces defaut servent à la fois de template pour la création des structure de paramètres, et de valeurs par défaut
MapWrapper.defaultParams = {
	viewLatitude: 0,
	viewLongitude: 0,
	viewZoom: 1,
	provider: "openlayers",
	caps: {
			desc: false,	// inutilisé, seulement là parce que l'ensemble est copié
			key: false,
			geocoder: false,
			kml: true,
			maptypes: false,
			auto_updt_controls: true,
			drag_markers: false,
			shadow_icon: false,
			marker_click_handler: false,
			ctrl_zoom: true,
			ctrl_pan: true,
			ctrl_scale: false,
			ctrl_overview: true,
			ctrl_maptypes: true
		},
	map_type: "mixte",
	ctrl_map_type: true,
	ctrl_pan: true, 
	ctrl_zoom: 'small',
	ctrl_scale: true,
	ctrl_overview: false,
	handleResize: true,
	draggable: false
};

// Définition des paramètre d'une icone
MapWrapper.IconDef = function(params)
{
	if (!isObject(params))
	{
		this.urlIconFile = SiteInfo.defaultIcon;
		this.urlShadowFile = SiteInfo.defaultShadow;
		this.widthIcon = SiteInfo.defaultIconWidth;
		this.heightIcon = SiteInfo.defaultIconHeight;
		this.widthShadow = SiteInfo.defaultShadowWidth;
		this.heightShadow = SiteInfo.defaultShadowHeight;
		this.anchorX = this.widthIcon / 2;
		this.anchorY = this.heightIcon;
		this.anchorShadowX = this.anchorX;
		this.anchorShadowY = this.anchorY;
		this.urlCompleteFile = SiteInfo.defaultCompleteIcon;
		this.widthComplete = SiteInfo.defaultCompleteWidth;
		this.heightComplete = SiteInfo.defaultCompleteHeight;
		this.anchorCompleteX = this.widthIcon / 2;
		this.anchorCompleteY = this.heightIcon;
		this.popupOffsetX = 0; // position calculée à partir de top/center
		this.popupOffsetY = this.heightIcon / 4;
	}
	else
	{
		this.urlIconFile = isObject(params.urlIconFile) ? params.urlIconFile : SiteInfo.defaultIcon;
		this.urlShadowFile = isObject(params.urlShadowFile) ? params.urlShadowFile : SiteInfo.defaultShadow;
		this.widthIcon = isObject(params.widthIcon) ? params.widthIcon : SiteInfo.defaultIconWidth;
		this.heightIcon = isObject(params.heightIcon) ? params.heightIcon : SiteInfo.defaultIconHeight;
		this.widthShadow = isObject(params.widthShadow) ? params.widthShadow : SiteInfo.defaultShadowWidth;
		this.heightShadow = isObject(params.heightShadow) ? params.heightShadow : SiteInfo.defaultShadowHeight;
		this.anchorX = isObject(params.anchorX) ? params.anchorX : this.widthIcon / 2;
		this.anchorY = isObject(params.anchorY) ? params.anchorY : this.heightIcon;
		this.anchorShadowX = isObject(params.anchorShadowX) ? params.anchorShadowX : this.anchorX;
		this.anchorShadowY = isObject(params.anchorShadowY) ? params.anchorShadowY : this.anchorY;
		this.urlCompleteFile = isObject(params.urlCompleteFile) ? params.urlCompleteFile : null;
		this.widthComplete = isObject(params.widthComplete) ? params.widthComplete : SiteInfo.defaultCompleteWidth;
		this.heightComplete = isObject(params.heightComplete) ? params.heightComplete : SiteInfo.defaultCompleteHeight;
		this.anchorCompleteX = isObject(params.anchorCompleteX) ? params.anchorCompleteX : this.widthIcon / 2;
		this.anchorCompleteY = isObject(params.anchorCompleteY) ? params.anchorCompleteY : this.heightIcon;
		this.popupOffsetX = isObject(params.popupOffsetX) ? params.popupOffsetX -(this.widthIcon / 2) : 0; // position calculée à partir de top/center
		this.popupOffsetY = isObject(params.popupOffsetY) ? params.popupOffsetY : this.heightIcon / 4;
	}
};

// Callback de resize
MapWrapper.cbOnResize = function(event)
{
	var map = event.data.map;
	if (isObject(map))
		map.onResize();
}

// Définition de l'objet carte
MapWrapper.prototype =
{
	// Outils pour récupérer la taille de la div, allègrement pompés de OpenLayers...
	// Les fonctions height et width de jQuery se reposent en dernier recours sur les
	// styles CSS, ce ne sont pas les tailles calculées, il faut donc descendre sur 
	// les tailles de l'élement DOM.
    getDivSize: function()
	{
		if (!isObject(this.div))
			return {width:0, height:0};
		var element = this.div.get(0);
		var width = element.clientWidth;
		var height = element.clientHeight;
        if (((width == 0) && (height == 0)) || (isNaN(width) && isNaN(height)))
		{
            width = element.offsetWidth;
            height = element.offsetHeight;
        }
        if ((width == 0 && height == 0) || (isNaN(width) && isNaN(height)))
		{
            width = parseInt(element.style.width);
            height = parseInt(element.style.height);
        }
        return {width:width, height:height};
    },
	isDivSized: function()
	{
		var size = this.getDivSize();
		return ((size.width == 0 && size.height == 0) || (isNaN(size.width) && isNaN(size.height))) ? false : true;
	},
	_isDivOK: function()
	{
		// OpenLayers ne supporte pas qu'on fixe le centre avant que la div qui contient la carte ne
		// soit dimensionnée...
		// À l'inverse, GoogleMaps ne supporte pas qu'on fasse une quelconque opération avant d'avoir
		// fait un setCenter...
		// Donc code spécifique fournisseur.
		return ((this.curParams.provider != 'openlayers') || this.isDivSized());
	},
	
	// Test des capacités
	_hasCap: function(name)
	{
		return this.curParams.caps[name];
	},

	// Chargement de la carte
	load: function(divElementId, params)
	{
		return this.jload(jQuery("#"+divElementId), params);
	},
	jload: function(div, params)
	{
    	// Si la carte est déjà créée, ne pas la refaire
		if (isObject(this.map))
			return this.update(params);
			
		// Tester le conteneur
		if (!isObject(div) || (div.length == 0))
			return false;

		// Copier les paramètres dans curParams, en prenant les valeurs par défaut là où elles ne sont pas fournies
		function _param(name) { return (isObject(params) && (typeof params[name] != 'undefined')) ? params[name] : MapWrapper.defaultParams[name]; }
		for (var elem in MapWrapper.defaultParams)
			this.curParams[elem] = _param(elem);

		// Créer et initialiser la carte
		this.div = div;
		this.map = new mxn.Mapstraction(this.div.attr('id'), this.curParams['provider']);
		if (this._hasCap('maptypes'))
			this.setMapType(this.curParams['map_type']);
		
		// Centre de la carte
		if (this._isDivOK())
		{
			this.map.setCenterAndZoom(new mxn.LatLonPoint(this.curParams['viewLatitude'], this.curParams['viewLongitude']), this.curParams['viewZoom']);
			this.bSizeInited = true;
		}
		
		// Contrôles
		this._updateControls();
		
		// Création d'une icone par defaut
		this.setIcon("default");
		
		// Gestion propre du resize
		if (this.curParams.handleResize === true)
			this.div.bind("resize", { map: this }, MapWrapper.cbOnResize);
		
		// La carte est prête
		this.div.triggerGmapReady(this.name);
		
		return true;
	},
	
	// Test si l'objet map est créé
	isLoaded: function()
	{
    	return (isObject(this.map)) ? true : false;
	},
	
	// Ajout des listeners
	// click-on-map = clic souris sur la carte -> function(event, latlng)
	// clic-on-point = clic sur un marqueur
	// drag-point = déplacement d'un marqueur -> function(event, zoom)
	addListener: function(event, listener)
	{
		if (!isObject(this.map))
			return false;
		
		// Ajouter l'évènement
		if (!isObject(this.listeners[event]))
			this.listeners[event] = new Array();
		this.listeners[event].push(listener);
		
		// Gestion spéciale sur les évènements récupéré directement sur la carte : on les ajoute ici
		// Pour les évènement récupérés sur les marqueurs, on les ajoute quand ils sont créés
		if (this.listeners[event].length == 1)
		{
			var objThis = this;
			switch (event)
			{
			// Clic souris sur la carte
			case "click-on-map":
				{
					this.map.click.addHandler(function(event, source, args)
					{
						var latlng = args.location;
						if (latlng != null)
							objThis.fireEvent("click-on-map", latlng.lat, latlng.lon);
					});
				}
				break;
			// Changement du facteur de zoom
			case "zoom":
				{
					this.map.changeZoom.addHandler(function(n, s, a)
					{
						objThis.fireEvent("zoom", objThis.map.getZoom());
					});
				}
				break;
			}
		}
		
		return true;
	},
	
	// Envoyer les évènements
	fireEvent: function(event)
	{
		for (index in this.listeners[event])
			this.listeners[event][index].apply(this, arguments);
	},
	
	// Libérer tous les listeners
	freeListeners: function()
	{
		delete this.listeners;
		this.listeners = new Array();
		this.map.click.removeAllHandlers();
		this.map.changeZoom.removeAllHandlers();
	},
	
	// Effacement de la carte
	unload: function()
	{
		this.freeListeners();
		
		if (isObject(this.infoWindow))
			this.closeInfoWindow;
			
		this.div.unbind("resize", MapWrapper.cbOnResize);
		this.div = null;

		if (isObject(this.curParams))
			delete this.curParams;
		this.curParams = null;
		
		if (isObject(this.map))
			delete this.map;
		this.map = null;
		this.bSizeInited = false;
	},
	
	// Affectation du type de carte
	_translateMapType: function(type)
	{
		// Exception des fournisseurs
		if ((this.curParams.provider == 'ovi') && (type == 'mixte'))
			type = "satellite";

		if (type === "plan")
			return mxn.Mapstraction.ROAD;
		else if (type === "satellite")
			return mxn.Mapstraction.SATELLITE;
		else if (type === "mixte")
			return mxn.Mapstraction.HYBRID;
		else if (type === "physic")
			return mxn.Mapstraction.PHYSICAL;
	},
	setMapType: function(type)
	{
		if (!isObject(this.map) || !isObject(type))
			return false;
		if (this._hasCap('maptypes'))
		{
			this.map.setMapType(this._translateMapType(type));
			this.curParams.map_type = type;
		}
		return true;
	},
	
	// Changement des paramètres
	_updateControls: function()
	{
		var controls = new Array();
		if (this.curParams['ctrl_pan'] !== false)
			controls.pan = true;
		if (this.curParams['ctrl_map_type'] !== false)
			controls.map_type = true;
		if (this.curParams['ctrl_zoom'] !== false)
			controls.zoom = this.curParams['ctrl_zoom'];
		if (this.curParams['ctrl_scale'] !== false)
			controls.scale = true;
		if (this.curParams['ctrl_overview'] !== false)
			controls.overview = true;
		this.map.addControls(controls);
	},
	update: function(params)
	{
		if (!isObject(this.map))
			return false;
			
		// Copier les paramètres
		for (var elem in MapWrapper.defaultParams)
			if (isObject(params[elem]))
				this.curParams[elem] = params[elem];

		// Modifier la carte
		if (this._hasCap('maptypes'))
			this.setMapType(this.curParams['map_type']);
		
		// Centre de la carte
		if (this._isDivOK())
		{
			this.map.setCenterAndZoom(new mxn.LatLonPoint(this.curParams['viewLatitude'], this.curParams['viewLongitude']), this.curParams['viewZoom']);
			this.bSizeInited = true;
		}
		
		// Contrôles
		this._updateControls();
		
		return true;
	},
	
	// Changement du centre
	_updateCurViewport: function()
	{
		this.curParams.viewZoom = this.map.getZoom();
		var center = this.map.getCenter();
		this.curParams.viewLatitude = center.lat;
		this.curParams.viewLongitude = center.lon;
	},
	setViewport: function(latitude, longitude, zoom)
	{
		if (!isObject(this.map) || !this._isDivOK())
			return false;
		var point = new mxn.LatLonPoint(latitude, longitude);
		this.map.setCenterAndZoom(point, zoom);
		this._updateCurViewport();
		return true;
	},
	setViewportBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		if (!isObject(this.map) || !this._isDivOK())
			return false;
		var bounds = new mxn.BoundingBox(minLatitude, minLongitude, maxLatitude, maxLongitude);
		this.map.setBounds(bounds);
		this._updateCurViewport();
		return true;
	},
	getViewport: function()
	{
		var vp = new Array();
		if (isObject(this.map))
		{
			var center = this.map.getCenter();
			vp['latitude'] = center.lat;
			vp['longitude'] = center.lon;
			vp['zoom'] = this.map.getZoom();
		}
		else
		{
			vp['latitude'] = 0.0;
			vp['longitude'] = 0.0;
			vp['zoom'] = 1;
		}
		return vp;
	},
	setCenter: function(latitude, longitude)
	{
		if (!isObject(this.map) || !this._isDivOK())
			return false;
		var point = new mxn.LatLonPoint(latitude, longitude);
		this.map.setCenter(point, null); // il faut mettre le deuxième paramètre, sinon l'implémentation Cloudmade plante...
		this._updateCurViewport();
		return true;
	},
	panTo: function(latitude, longitude)
	{
		if (!isObject(this.map) || !this._isDivOK())
			return false;
		var point = new mxn.LatLonPoint(latitude, longitude);
		this.map.setCenter(point, {pan: true});
		this._updateCurViewport();
		return true;
	},
	panToBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		return this.setViewportBounds(minLatitude, minLongitude, maxLatitude, maxLongitude);
	},
	setZoom: function(zoom)
	{
		if (!isObject(this.map) || !this._isDivOK())
			return false;
		this.map.setZoom(zoom);
		this._updateCurViewport();
		return true;
	},
	
	// Redimensionnement
	onResize: function()
	{
		if (!isObject(this.map) || !this._isDivOK())
			return;
		var center;
		if (this.bSizeInited)
			center = this.map.getCenter();
		else
			center = new mxn.LatLonPoint(this.curParams['viewLatitude'], this.curParams['viewLongitude']);
		var size = this.getDivSize();
		this.map.resizeTo(size.width, size.height);
		this.map.setCenterAndZoom(center, this.curParams['viewZoom']);
		this.bSizeInited = true;
	},
	
	
	//// GEOCODER
	
	// Recherche par le geocoder
	searchGeocoder: function(address, callback)
	{
		if (!this._hasCap('geocoder'))
			return false;
		var thisObject = this;
		var geocoder = new mxn.Geocoder(this.curParams['provider'],
			function(return_location)
			{
				callback.call(thisObject, return_location.point.lat, return_location.point.lon);
			},
			function() {}); // pas de callback d'erreur
		geocoder.geocode({ address: address });
		return true;
	},
	queryGeocoder: function(address, callback)
	{
		if (!this._hasCap('geocoder'))
			return false;
		var thisObject = this;
		var geocoder = new mxn.Geocoder(this.curParams['provider'],
			function(return_location)
			{
				var locations = new Array();
				locations[0] = new Array();
				if (return_location.address)
					locations[0].name = return_location.address;
				else
				{
					var addrParts = Array();
					if (return_location.street.length)
						addrParts.push(return_location.street);
					if (return_location.locality.length)
						addrParts.push(return_location.locality);
					if (return_location.postcode.length)
						addrParts.push(return_location.postcode);
					if (return_location.region.length)
						addrParts.push(return_location.region);
					if (return_location.country.length)
						addrParts.push(return_location.country);
					locations[0].name = addrParts.join(" ");
				}
				locations[0].latitude = return_location.point.lat;
				locations[0].longitude = return_location.point.lon;
				callback.call(thisObject, locations);
			},
			function() {}); // pas de callback d'erreur
		geocoder.geocode({ address: address });
		return true;
	},
	
	
	//// ICONS
	
	// Ajout ou modification d'une icone
	// name : nom de l'icone
	// params : paramètres (selon la définition de MapWrapper.IconDef
	setIcon: function(name, params)
	{
		this.icons[name] = new MapWrapper.IconDef(params);
	},
	
	// Récupération d'une icone (usage interne : l'objet renvoyé est spécifique à l'implémentation)
	getIcon: function(name)
	{
		return this.icons[name];
	},

	// Test si un marqueur existe
	existIcon: function(name)
	{
		return (isObject(this.getIcon(name)));
	},
	
	
	//// MARKERS
	
	// Récupérer un identifiant de marqueur non utilisé
	getNewMarkerID: function()
	{
		var id = this.nextMarkerID;
		while (this.markers[id])
			id++;
		this.nextMarkerID = id+1;
		return id;
	},
	
	// Récupérer l'objet specifique du marqueur
	// Au cas où on voudrait faire des développements spécifique à une implémentation
	getMarkerObject: function(id)
	{
		var marker = null;
		if (id instanceof mxn.Marker)
			marker = id;
		else
			marker = this.markers[id];
		if (!isObject(marker) || !(marker instanceof mxn.Marker))
			return null;
		return marker;
	},
	
	// Test si un marqueur existe
	existMarker: function(id)
	{
		return (isObject(this.getMarkerObject(id)));
	},
	
	// Recalcul des propriétés d'un marqueur
	// id: identifiant unique du marqueur
	getMarkerDefinition: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return null;
			
		var params = new Object();
		for (prop in marker.extraData.params)
			params[prop] = marker.extraData.params[prop];
		
		var center = marker.location;
		params.latitude = center.lat;
		params.longitude = center.lon;
		
		return params;
	},
	
	// Suppression d'un marqueur
	removeMarker: function(id)
	{
		if (!isObject(this.map))
			return;
			
		// Récupérer le marqueur (et son id si on a passé l'objet en paramètre)
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		id = marker.extraData.id;

		// Détruire le marker
		this.map.removeMarker(marker);
		delete marker;
		
		// Le supprimer du tableau global
		this.markers[id] = null;
		delete this.markers[id];
			
		return true;
	},
	
	// Ajout ou modification d'un marqueur
	// id: identifiant unique de ce marqueur
	// params : paramètres du marqueur
	_useIcon: function(marker, icon)
	{
		var def = this.getIcon(icon);
		if (this._hasCap('shadow_icon'))
		{
			marker.setIcon(def.urlIconFile, [def.widthIcon, def.heightIcon], [def.anchorX, def.anchorY]);
			if (isObject(def.urlShadowFile))
				marker.setShadowIcon(def.urlShadowFile, [def.widthShadow, def.heightShadow]);
		}
		else if (isObject(def.urlCompleteFile))
			marker.setIcon(def.urlCompleteFile, [def.widthComplete, def.heightComplete], [def.anchorCompleteX, def.anchorCompleteY]);
		else
			marker.setIcon(def.urlIconFile, [def.widthIcon, def.heightIcon], [def.anchorX, def.anchorY]);
	},
	setMarker: function(id, params)
	{
		// Récupérer le marqueur (et son id si on a passé l'objet en paramètre)
		var marker = this.getMarkerObject(id);

		// Cas où le marqueur n'existe pas
		if (!isObject(marker))
		{
			// Créer le marqueur
			var point = new mxn.LatLonPoint(params.latitude, params.longitude);
			marker = new mxn.Marker(point);
			this.markers[id] = marker;
			marker.extraData = {
				id: id,
				params: clone(params)
			};
			
			// On utilise toujours une icone "custom" pour éviter le défaut du provider
			if (!isObject(params.icon))
				params.icon = "default";
			
			// Toujours ajouter les handlers d'évènements pour ce qui peut être remonté
			var objThis = this;
			if (this._hasCap('marker_click_handler'))
			{
				marker.click.addHandler(function(name, marker)
				{
					switch (marker.extraData.params.click)
					{
					// Tous les fournisseurs n'implémentent pas cet évènement, donc on ne
					// peut pas compter dessus pour afficher les bulles d'informations.
					case 'showInfoWindow':
						{
							var timer = setTimeout(function()
							{
								objThis.showInfoWindow(marker);
								timer = null;
							}, 200);
						}
						break;
					case 'custom':
						objThis.fireEvent("click-on-marker", marker.extraData.id);
						break;
					}
				});
			}
			marker.openInfoBubble.addHandler(function()
			{
				objThis.fireEvent("info-window-open");
			});
			// Il reste à implémenter "drop-marker" et "dblclick_on_marker" qui ne sont pas dans 
			// Mapstraction.
			// En attendant soit une implémentation manuelle, soit une évolution de Mapstraction,
			// tous les fournisseurs sont taggés "drag_markers = false" et "dblclick = false"
			//	objThis.fireEvent("drop-marker", marker.extraData.id, args.location.lat, args.location.lon);
		}
		
		// Cas où le marqueur existe
		else
		{
			// Retirer le marqueur 
			this.map.removeMarker(marker);

			// Recopier les champs
			for (prop in params)
				marker.extraData.params[prop] = params[prop];
			
			// Mettre à jour la position
			marker.location = new mxn.LatLonPoint(marker.extraData.params.latitude, marker.extraData.params.longitude);
		}
		
		// Mise à jour des données (sauf la position qui a déjà été donnée)
		if (isObject(params.draggable) && this._hasCap('draggable'))
			marker.setDraggable(params.draggable);
		if (isObject(params.html) && !this._hasCap('marker_click_handler')) // sinon on affiche l'info-bulle par showInfoWindow
			marker.setInfoBubble(params.html);
		if (isObject(params.title))
			marker.setLabel(params.title);
		marker.setHover(false); // pas d'ouverture de la bulle en survol...
			
		// Mise à jour des icones
		if (isObject(params.icon))
			this._useIcon(marker, params.icon);

		// Ajouter le marqueur sur la carte
		this.map.addMarker(marker, false);
		
		return true;
	},

	// Changer la position d'un marqueur
	setMarkerPosition: function(id, latitude, longitude)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		// Je n'ai pas trouvé d'API dans Mapstraction pour METTRE À JOUR un marqueur, donc
		// solution bulldozer : on détruit et on recréé
		this.map.removeMarker(marker);
		marker.extraData.params.latitude = latitude;
		marker.extraData.params.longitude = longitude;
		marker.location = new mxn.LatLonPoint(marker.extraData.params.latitude, marker.extraData.params.longitude);
		this.map.addMarker(marker);
		return true;
	},
	
	// Afficher la fenêtre sur un marqueur
	showInfoWindow: function(id)
	{
		if (!isObject(this.map))
			return false;
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		var htmlContents = marker.extraData.params.html;
		if ((typeof(htmlContents) === "string") && (htmlContents.length > 0))
		{
			if (isObject(this.infoWindow))
				this.closeInfoWindow();
			this.infoWindow = marker;
			this.infoWindow.setInfoBubble(htmlContents);
			this.infoWindow.openBubble();
			this.infoWindow.setInfoBubble('');
		}
		return true;
	},
	closeInfoWindow: function()
	{
		if (!isObject(this.map))
			return false;
		if (isObject(this.infoWindow))
		{
			this.infoWindow.closeBubble();
			this.infoWindow = null;
		}
		return true;
	},
	
	// Afficher ou cacher un marqueur
	isMarkerVisible: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		return marker.getAttribute("visible") ? true : false;
	},
	showMarker: function(id, bShow)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		if (bShow)
			marker.show();
		else
			marker.hide();
		return true;
	},
	toggleMarker: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		if (marker.getAttribute("visible"))
			marker.hide();
		else
			marker.show();
		return true;
	},

	// Afficher ou cacher tous les marqueurs 
	areMarkersVisible: function()
	{
		var marker;
		for (var id in this.markers)
		{
			marker = this.markers[id];
			if (!(marker instanceof mxn.Marker))
				continue;
			if (marker.getAttribute("visible"))
				return true;
		}
		return false;
	},
	showMarkers: function(bShow)
	{
		var marker;
		for (var id in this.markers)
		{
			marker = this.markers[id];
			if (!(marker instanceof mxn.Marker))
				continue;
			if (bShow)
				marker.show();
			else
				marker.hide();
		}
		return true;
	},
	toggleMarkers: function()
	{
		var bShow = this.areMarkersVisible();
		return this.showMarkers(!bShow);
	},
	
	
	//// FICHIERS KML
	
	// Récupérer un identifiant de marqueur non utilisé
	getNewLayerID: function()
	{
		return 0;
	},
	
	// Récupérer l'objet specifique du marqueur
	// Au cas où on voudrait faire des développements spécifique à une implémentation
	getLayerObject: function(id)
	{
		return null;
	},
	
	// Test si un marqueur existe
	existLayer: function(id)
	{
		return false;
	},
	
	// Création d'un tracé KML
	addLayerKML: function(id, url, show)
	{
		if (!isObject(this.map))
			return false;
		this.map.addOverlay(url, show);
		return true;
	},
	
	// Création d'une couche externe
	addLayerAuto: function(id, url, show)
	{
		return false;
	},
	
	// Supprimer une couche
	removeLayer: function(id)
	{
		return false;
	},
	
	// Afficher une couche
	showLayer: function(id, show)
	{
		return false;
	},
	toggleLayerVisibility: function(id)
	{
		return false;
	},
	
	// Centrer la carte
	gotoLayerViewport: function(id)
	{
		return false;
	}
	
};

// Fermeture de la page
jQuery(document).unload(function()
{
	MapWrapper.freeAllMaps();
});

