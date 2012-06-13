/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Objet carte pour Google Maps API v2
 *
 */
	
//// D�finition d'un objet pour contenir les donn�es d'une carte

// D�finition d'un objet qui recevra des param�tres sp�cifiques au site
var SiteInfo = new Object();

// Tableau des cartes
MapWrapper.maps = new Array();

// D�finition de la classe Map
function MapWrapper(name)
{
	this.name = name;			// nom de la carte
	
	this.curParams = {};
	this.map = null;			// Objet Google Maps
	this.manager = null;		// manager des markers
	this.geocoder = null;		// Objet geocoder
	this.div = null;			// Objet jQuery autour de la DIV qui contient la carte
	
	this.ctrlNavigation = null;	// Contr�le de navigation
	this.ctrlMapTypes = null;	// Contr�le des types de carte
	
	this.icons = new Array();	// liste des icones de marqueur
	this.markers = new Array();	// liste des marqueurs
	this.nextMarkerID = 1;		// ID du prochain marqueur
	this.layers = new Array();	// liste des fichiers de couche (KML)
	this.nextLayerID = 1;		// ID de la prochaine couche KML
	this.icons = new Array();	// liste des ic�nes d�finies
	
	// M�canisme de listeners
	this.listeners = new Array();
	this.lastlatlng = null;
};

// R�cup�rer ou cr�er une carte
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
	if (arrayCount(MapWrapper.maps) == 0)
		GUnload();
}

// Accesseurs rapides "� la" jQuery
gMap = function(name)
{
	if (!isObject(MapWrapper.maps))
		return null;
	return MapWrapper.getMap(name, false);
};


// D�finition des param�tres de la carte
// Ces defaut servent � la fois de template pour la cr�ation des structure de param�tres, et de valeurs par d�faut
MapWrapper.defaultParams = {
	viewLatitude: 0,
	viewLongitude: 0,
	viewZoom: 1,
	mapTypes: ["plan", "satellite", "mixte", "physic", "earth"],
	defaultMapType: "mixte",
	styleBackgroundCommand: "menu",
	styleNavigationCommand: "3D",
	enableDblClkZoom: true,
	enableContinuousZoom: true,
	enableWheelZoom: false,
	infoWidthPercent: 65,
	infoWidthAbsolute: 300,
	handleResize: true,
	mergeInfoWindows: false
};

// D�finition des param�tre d'une icone
MapWrapper.IconDef = function(params)
{
	if (isObject(params) && isObject(params.urlIconFile))
	{
		this.urlIconFile = params.urlIconFile;
		this.widthIcon = isObject(params.widthIcon) ? params.widthIcon : SiteInfo.iconDef.widthIcon;
		this.heightIcon = isObject(params.heightIcon) ? params.heightIcon : SiteInfo.iconDef.heightIcon;
		this.anchorX = isObject(params.anchorX) ? params.anchorX : this.widthIcon / 2;
		this.anchorY = isObject(params.anchorY) ? params.anchorY : this.heightIcon;
		this.popupOffsetX = isObject(params.popupOffsetX) ? params.popupOffsetX : this.widthIcon / 2;
		this.popupOffsetY = isObject(params.popupOffsetY) ? params.popupOffsetY : this.heightIcon / 4;
	}
	else
	{
		this.urlIconFile = SiteInfo.iconDef.urlIconFile;
		this.widthIcon = SiteInfo.iconDef.widthIcon;
		this.heightIcon = SiteInfo.iconDef.heightIcon;
		this.anchorX = SiteInfo.iconDef.anchorX;
		this.anchorY = SiteInfo.iconDef.anchorY;
		this.popupOffsetX = SiteInfo.iconDef.popupOffsetX;
		this.popupOffsetY = SiteInfo.iconDef.popupOffsetY;
	}
	
	if (isObject(params) && isObject(params.urlShadowFile))
	{
		this.urlShadowFile = params.urlShadowFile;
		this.widthShadow = isObject(params.widthShadow) ? params.widthShadow : SiteInfo.iconDef.widthShadow;
		this.heightShadow = isObject(params.heightShadow) ? params.heightShadow : SiteInfo.iconDef.heightShadow;
	}
	else
	{
		this.urlShadowFile = SiteInfo.iconDef.urlShadowFile;
		this.widthShadow = SiteInfo.iconDef.widthShadow;
		this.heightShadow = SiteInfo.iconDef.heightShadow;
	}
};
MapWrapper.IconDef.prototype =
{
	// Cr�ation d'une icone GMap
	createIcon: function()
	{
		var icon = new GIcon();
		this.updateIcon(icon);
		return icon;
	},
	
	// Mise � jour d'une icone
	updateIcon: function(icon)
	{
		icon.image = this.urlIconFile;
		icon.shadow = this.urlShadowFile;
		icon.iconSize = new GSize(this.widthIcon, this.heightIcon);
		icon.shadowSize = new GSize(this.widthShadow, this.heightShadow);	
		icon.iconAnchor = new GPoint(this.anchorX, this.anchorY);
		icon.infoWindowAnchor = new GPoint(this.popupOffsetX, this.popupOffsetY);
	}
};

// Outils de DEBUG
function _dumpParams(params, intro)
{
	var str = "";
	if ((typeof(intro) != "undefined") && (intro.length > 0))
		str += intro + "\n";
	for (name in params)
		str += name + " = " + params[name] + "\n";
	alert(str);
}

// Callback de resize
MapWrapper.cbOnResize = function(event)
{
	var map = event.data.map;
	if (isObject(map))
		map.onResize();
}

// D�finition de l'objet carte
MapWrapper.prototype =
{
	// Chargement de la carte
	load: function(divElementId, params)
	{
		return this.jload(jQuery("#"+divElementId), params);
	},
	jload: function(div, params)
	{
   		// V�rifier que le browser est compatible
   		if (!GBrowserIsCompatible())
       		return false;

		//_dumpParams(params);
    		
    	// Si la carte est d�j� cr��e, ne pas la refaire
    	if (isObject(this.map))
    		return this.update(params);

		// Tester le conteneur
		if (!isObject(div) || (div.length == 0))
			return false;
		var divElem = div.get(0);
		
		// Copier les param�tres
		function _param(name) { return (isObject(params) && (typeof params[name] != 'undefined')) ? params[name] : MapWrapper.defaultParams[name]; }
		for (var elem in MapWrapper.defaultParams)
			this.curParams[elem] = _param(elem);

		// Cr�er et initialiser la carte
		this.div = div;
		this.map = new GMap2(divElem);
		this.map.setCenter(new GLatLng(this.curParams.viewLatitude, this.curParams.viewLongitude), this.curParams.viewZoom);
		this.applyAllowedMapTypes(this.curParams.mapTypes);
		this.setMapType(this.curParams.defaultMapType);
		if (this.curParams.enableDblClkZoom)
			this.map.enableDoubleClickZoom();
		if (this.curParams.enableContinuousZoom)
			this.map.enableContinuousZoom();
		if (this.curParams.enableWheelZoom)
			this.map.enableScrollWheelZoom();
		
		// Commandes de navigation
		this.ctrlNavigation = null;
		if (this.curParams.styleNavigationCommand === "small")
			this.ctrlNavigation = new GSmallMapControl();
		else if (this.curParams.styleNavigationCommand === "large")
			this.ctrlNavigation = new GLargeMapControl();
		else if (this.curParams.styleNavigationCommand === "3D")
			this.ctrlNavigation = new GLargeMapControl3D();
		if (this.ctrlNavigation != null)
			this.map.addControl(this.ctrlNavigation);
			
		// Commandes de choix du fond de carte
		this.ctrlMapTypes = null;
		if (this.curParams.styleBackgroundCommand === "button")
			this.ctrlMapTypes = new GMapTypeControl();
		else if (this.curParams.styleBackgroundCommand === "menu")
			this.ctrlMapTypes = new GMenuMapTypeControl();
		if (this.ctrlMapTypes != null)
			this.map.addControl(this.ctrlMapTypes);

		// Cr�ation d'une icone par defaut
		this.setIcon("default");
		
		// Manager et geocoder
		this.manager = new MarkerManager(this.map, { trackMarkers: true });
		this.geocoder = new GClientGeocoder();

		// Gestion propre du resize
		if (this.curParams.handleResize === true)
			this.div.bind("resize", { map: this }, MapWrapper.cbOnResize);
		
		// La carte est pr�te
		this.div.triggerGmapReady(this.name);
		
		return true;
	},
	
	// Test si l'objet map est cr��
	isLoaded: function()
	{
    	return (isObject(this.map)) ? true : false;
	},
	
	// Ajout des listeners
	// click-on-map = clic souris sur la carte -> function(event, latlng)
	// clic-on-point = clic sur un marqueur
	// drag-point = d�placement d'un marqueur -> function(event, zoom)
	addListener: function(event, listener)
	{
		if (!isObject(this.map))
			return false;
		
		// Ajouter l'�v�nement
		if (!isObject(this.listeners[event]))
			this.listeners[event] = new Array();
		this.listeners[event].push(listener);
		
		// Gestion sp�cial sur les �v�nements r�cup�r� directement sur la carte : on les ajoute ici
		// Pour les �v�nement r�cup�r�s sur les marqueurs, on les ajoute quand ils sont cr��s
		if (this.listeners[event].length == 1)
		{
			var objThis = this;
			switch (event)
			{
			// Clic souris sur la carte
			case "click-on-map":
				{
					// HACK HACK HACK (comme ils disent chez Microsoft)
					// Il y a une erreur dans IE8, le latlng pass� ici n'est pas correct.
					// Pour contourner, on utilise celui qu'on a sauvegard� sur le dernier mouse move...
					GEvent.addListener(this.map, "mousemove", function(latlng)
					{
						objThis.lastlatlng = latlng;
					});
					GEvent.addListener(this.map, "click", function(overlay, latlng, overlaylatlng)
					{
						// On g�re seulement les clicks sur la carte
						// (Les clicks sur les couches peuvent �tre pris en compte au niveau de ces dernieres)
						if ((overlay == null) && (latlng != null))
							objThis.fireEvent("click-on-map", objThis.lastlatlng.lat(), objThis.lastlatlng.lng());
					});
				}
				break;
			// Changement du facteur de zoom
			case "zoom":
				{
					GEvent.addListener(this.map, "zoomend", function(oldlevel, newlevel)
					{
						objThis.fireEvent("zoom", newlevel);
					});
				}
				break;
			// Ouverture d'une fen�tre d'information
			case "info-window-open":
				{
					GEvent.addListener(this.map, "infowindowopen", function()
					{
						objThis.fireEvent("info-window-open");
					});
				}
				break;
			}
		}
		
		return true;
	},
	
	// Envoyer les �v�nements
	fireEvent: function(event)
	{
		for (index in this.listeners[event])
			this.listeners[event][index].apply(this, arguments);
	},
	
	// Lib�rer tous les listeners
	freeListeners: function()
	{
		delete this.listeners;
		this.listeners = new Array();
		this.lastlatlng = null;
	},
	
	// Effacement de la carte
	unload: function()
	{
		if (isObject(this.curParams))
			delete this.curParams;
		this.curParams = null;
		
		this.freeListeners();
		
		if (isObject(this.ctrlNavigation))
			delete this.ctrlNavigation;
		this.ctrlNavigation = null;
		if (isObject(this.ctrlMapTypes))
			delete this.ctrlMapTypes;
		this.ctrlMapTypes = null;
		
		if (isObject(this.manager))
			delete this.manager;
		this.manager = null;
		
		if (isObject(this.geocoder))
			delete this.geocoder;
		this.geocoder = null;
		
		this.div.unbind("resize", MapWrapper.cbOnResize);
		this.div = null;
		
		if (isObject(this.map))
			delete this.map;
		this.map = null;
	},
	
	// Test des types visibles dans une carte
	applyAllowedMapTypes: function(mapTypes)
	{
		if (!isObject(this.map) || !isObject(mapTypes))
			return false;
		
		// J'ai eu des soucis quand il n'y a aucun type de carte, pour les �viter
		// je remplis d'abord et vide ensuite...
		
		// S'il y a un contr�le, le supprimer
		if (isObject(this.ctrlMapTypes))
			this.map.removeControl(this.ctrlMapTypes);
		
		// D'abord ajouter les types d�finis, on est surs qu'il y en a
		var mapType = null;
		for (var imt in mapTypes)
		{
			mapType = mapTypes[imt];
			switch (mapType)
			{
			case "plan": this.map.addMapType(G_NORMAL_MAP); break;
			case "satellite": this.map.addMapType(G_SATELLITE_MAP); break;
			case "mixte": this.map.addMapType(G_HYBRID_MAP); break;
			case "physic": this.map.addMapType(G_PHYSICAL_MAP); break;
			case "earth": this.map.addMapType(G_SATELLITE_3D_MAP); break;
			}
		}
		
		// Puis supprimer les types non demand�s
		// (il faudrait passer par un parcours des types de la carte...)
		if (!arrayContains(mapTypes, "plan"))
			this.map.removeMapType(G_NORMAL_MAP);
		if (!arrayContains(mapTypes, "satellite"))
			this.map.removeMapType(G_SATELLITE_MAP);
		if (!arrayContains(mapTypes, "mixte"))
			this.map.removeMapType(G_HYBRID_MAP);
		if (!arrayContains(mapTypes, "physic"))
			this.map.removeMapType(G_PHYSICAL_MAP);
		if (!arrayContains(mapTypes, "earth"))
			this.map.removeMapType(G_SATELLITE_3D_MAP);

		// Remettre le contr�le
		if (isObject(this.ctrlMapTypes))
			this.map.addControl(this.ctrlMapTypes);
			
		return true;
	},
	
	// Affectation du type de carte
	_translateMapType: function(type)
	{
		switch (type)
		{
		case "plan": return G_NORMAL_MAP;
		case "satellite": return G_SATELLITE_MAP;
		case "mixte": return G_HYBRID_MAP;
		case "physic": return G_PHYSICAL_MAP;
		case "earth": return G_SATELLITE_3D_MAP;
		}
		return G_HYBRID_MAP;
	},
	setMapType: function(type)
	{
		this.map.setMapType(this._translateMapType(type));
		return true;
	},
	
	// Changement des param�tres
	update: function(params)
	{
		if (!isObject(this.map))
			return false;
			
		// Copier les param�tres
		var oldParams = new Array();
		function _param(name) { return (isObject(params) && (typeof params[name] != 'undefined')) ? params[name] : MapWrapper.defaultParams[name]; }
		for (var elem in MapWrapper.defaultParams)
		{
			oldParams[elem] = this.curParams[elem];
			this.curParams[elem] = _param(elem);
		}
			
		// Mise � jour des fonds de carte autoris�s et du type par d�faut
		this.applyAllowedMapTypes(this.curParams.mapTypes);
		this.setMapType(this.curParams.defaultMapType);
		
		// Commande de navigation
		if (oldParams.styleNavigationCommand != this.curParams.styleNavigationCommand)
		{
			if (isObject(this.ctrlNavigation))
				this.map.removeControl(this.ctrlNavigation);
			this.ctrlNavigation = null;
			if (this.curParams.styleNavigationCommand === "small")
				this.ctrlNavigation = new GSmallMapControl();
			else if (this.curParams.styleNavigationCommand === "large")
				this.ctrlNavigation = new GLargeMapControl();
			else if (this.curParams.styleNavigationCommand === "3D")
				this.ctrlNavigation = new GLargeMapControl3D();
			if (this.ctrlNavigation != null)
				this.map.addControl(this.ctrlNavigation);
		}
		
		// Commande de changement du fond de carte
		if (oldParams.styleBackgroundCommand != this.curParams.styleBackgroundCommand)
		{
			if (isObject(this.ctrlMapTypes))
				this.map.removeControl(this.ctrlMapTypes);
			this.ctrlMapTypes = null;
			if (this.curParams.styleBackgroundCommand === "button")
				this.ctrlMapTypes = new GMapTypeControl();
			else if (this.curParams.styleBackgroundCommand === "menu")
				this.ctrlMapTypes = new GMenuMapTypeControl();
			if (this.ctrlMapTypes != null)
				this.map.addControl(this.ctrlMapTypes);
		}
		
		// Flags
		if (this.curParams.enableDblClkZoom)
			this.map.enableDoubleClickZoom();
		else
			this.map.disableDoubleClickZoom();
		if (this.curParams.enableContinuousZoom)
			this.map.enableContinuousZoom();
		else
			this.map.disableContinuousZoom();
		if (this.curParams.enableWheelZoom)
			this.map.enableScrollWheelZoom();
		else
			this.map.disableScrollWheelZoom();
		
		return true;
	},
	
	// Changement du centre
	setViewport: function(latitude, longitude, zoom)
	{
		if (!isObject(this.map))
			return false;
		this.curParams.viewLatitude = latitude;
		this.curParams.viewLongitude = longitude;
		this.curParams.viewZoom = zoom;
		this.map.setCenter(new GLatLng(this.curParams.viewLatitude, this.curParams.viewLongitude), this.curParams.viewZoom);
		return true;
	},
	_setViewportBounds: function(bounds)
	{
		if (!isObject(this.map))
			return false;
		var center = bounds.getCenter();
		var zoom = this.map.getBoundsZoomLevel(bounds);
		return this.setViewport(center.lat(), center.lng(), zoom);
	},
	setViewportBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		return this._setViewportBounds(new GLatLngBounds(new GLatLng(minLatitude, minLongitude), new GLatLng(maxLatitude, maxLongitude)));
	},
	getViewport: function()
	{
		var vp = new Array();
		if (isObject(this.map))
		{
			var center = this.map.getCenter();
			vp['latitude'] = center.lat();
			vp['longitude'] = center.lng();
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
	getViewportBounds: function()
	{
		if (!isObject(this.map))
			return false;
		var mapBounds = this.map.getBounds();
		var mapTopRight = mapBounds.getNorthEast();
		var mapBottomLeft = mapBounds.getSouthWest();
		var bounds = new Object();
		bounds.max_lat = mapTopRight.lat();
		bounds.max_lng = mapTopRight.lng();
		bounds.min_lat = mapBottomLeft.lat();
		bounds.min_lng = mapBottomLeft.lng();
		return bounds;
	},
	setCenter: function(latitude, longitude)
	{
		if (!isObject(this.map))
			return false;
		this.curParams.viewLatitude = latitude;
		this.curParams.viewLongitude = longitude;
		this.map.setCenter(new GLatLng(this.curParams.viewLatitude, this.curParams.viewLongitude));
		return true;
	},
	panTo: function(latitude, longitude)
	{
		if (!isObject(this.map))
			return false;
		this.curParams.viewLatitude = latitude;
		this.curParams.viewLongitude = longitude;
		this.map.panTo(new GLatLng(this.curParams.viewLatitude, this.curParams.viewLongitude));
		return true;
	},
	panToBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		var latitude = (minLatitude + maxLatitude) / 2;
		var longitude = (minLongitude + maxLongitude) / 2;
		var bounds = new  GLatLngBounds(new GLatLng(minLatitude, minLongitude), new GLatLng(maxLatitude, maxLongitude));
		var zoom = this.map.getBoundsZoomLevel(bounds);
		this.panTo(latitude, longitude);
		var curZoom = this.curParams.viewZoom;
		if (isObject(this.map))
			curZoom = this.map.getZoom();
		if (zoom < curZoom)
			this.setZoom(zoom);
		return true;
	},
	setZoom: function(zoom)
	{
		if (!isObject(this.map))
			return false;
		this.curParams.viewZoom = zoom;
		this.map.setZoom(this.curParams.viewZoom);
		return true;
	},
	
	// Redimensionnement
	onResize: function()
	{
		if (!isObject(this.map))
			return;
			
		// R�cup�rer le positionnement
		var center = this.map.getCenter();

		// M�canique interne
		this.map.checkResize();

		// Rechargement
		this.map.setCenter(center);
	},

	// Fonctions de conversion entre lat/lng et pixel
	_fromLatLngToPixel: function(latlng)
	{
		var mapType = isObject(this.map) ? this.map.getCurrentMapType() : null;
		var proj = isObject(mapType) ? mapType.getProjection() : null;
		if (isObject(proj))
			return proj.fromLatLngToPixel(latlng, this.map.getZoom());
		else
			return new GPoint(0, 0);
	},
	_fromPixelToLatLng: function(point)
	{
		var mapType = isObject(this.map) ? this.map.getCurrentMapType() : null;
		var proj = isObject(mapType) ? mapType.getProjection() : null;
		if (isObject(proj))
			return proj.fromPixelToLatLng(point, this.map.getZoom());
		else
			return new GPoint(0, 0);
	},
	
	
	//// GEOCODER
	
	// Recherche par le geocoder
	searchGeocoder: function(address, callback)
	{
		if (!isObject(this.geocoder))
			return false;
		this.geocoder.getLatLng(address, function(point)
		{
			callback.call(this, point.lat(), point.lng());
		});
		return true;
	},
	queryGeocoder: function(address, callback)
	{
		if (!isObject(this.geocoder))
			return false;
		this.geocoder.getLocations(address, function(results)
		{
			if (results.Status.code == G_GEO_SUCCESS)
			{
				var locations = new Array();
				for (var index in results.Placemark)
				{
					var aLocation = new Array();
					var place = results.Placemark[index];
					aLocation.name = place.address;
					aLocation.latitude = place.Point.coordinates[1];
					aLocation.longitude = place.Point.coordinates[0];
					locations.push(aLocation);
				}
				callback.call(this, locations);
			}
		});
		return true;
	},
	
	
	//// ICONS
	
	// Ajout ou modification d'une icone
	// name : nom de l'icone
	// params : param�tres (selon la d�finition de MapWrapper.IconDef
	setIcon: function(name, params)
	{
		var def = new MapWrapper.IconDef(params);
		if (!isObject(this.icons[name]))
			this.icons[name] = def.createIcon();
		else
			def.updateIcon(this.icons[name]);
	},
	
	// R�cup�ration d'une icone (usage interne : l'objet renvoy� est sp�cifique � l'impl�mentation)
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
	
	// R�cup�rer un identifiant de marqueur non utilis�
	getNewMarkerID: function()
	{
		var id = this.nextMarkerID;
		while (this.markers[id])
			id++;
		this.nextMarkerID = id+1;
		return id;
	},
	
	// R�cup�rer l'objet specifique du marqueur
	// Au cas o� on voudrait faire des d�veloppements sp�cifique � une impl�mentation
	getMarkerObject: function(id)
	{
		var marker = null;
		if (id instanceof GMarker)
			marker = id;
		else
			marker = this.markers[id];
		if (!isObject(marker) || !(marker instanceof GMarker))
			return null;
		return marker;
	},
	
	// Test si un marqueur existe
	existMarker: function(id)
	{
		return (isObject(this.getMarkerObject(id)));
	},
	
	// Recalcul des propri�t�s d'un marqueur
	// id: identifiant unique du marqueur
	getMarkerDefinition: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return null;
			
		var params = new Object();
		for (prop in marker.extraData.params)
			params[prop] = marker.extraData.params[prop];
		
		var center = marker.getLatLng();
		params.latitude = center.lat();
		params.longitude = center.lng();
		
		return params;
	},
	
	// Suppression d'un marqueur
	removeMarker: function(id)
	{
		// R�cup�rer le marqueur (et son id si on a pass� l'objet en param�tre)
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		id = marker.extraData.id;

		// D�truire
		if (isObject(this.manager))
			this.manager.removeMarker(marker);
		else
			this.map.removeOverlay(marker);
		
		// Vider
		this.markers[id] = null;
		delete marker;
		delete this.markers[id];
			
		return true;
	},
	
	// Ajout ou modification d'un marqueur
	// id: identifiant unique de ce marqueur
	// params : param�tres du marqueur
	setMarker: function(id, params)
	{
		// R�cup�rer le marqueur (et son id si on a pass� l'objet en param�tre)
		var marker = this.getMarkerObject(id);
		
		// Cas o� le marqueur n'existe pas
		if (!isObject(marker))
		{
			// Position
			var center = this.map.getCenter();
			var position = new GLatLng(isObject(params.latitude) ? params.latitude : center.lat(),
									isObject(params.longitude) ? params.longitude : center.lng());
			
			// Param�tres
			var gpar = new Object();
			if (isObject(params.icon))
				gpar.icon = this.getIcon(params.icon);
			else
				gpar.icon = this.getIcon("default");
			if (isObject(params.title))
				gpar.title = params.title;
			gpar.draggable = false;
			if (isObject(params.draggable))
				gpar.draggable = params.draggable;
				
			// Gestion du z-order
			if (isObject(params.zorder) && (params.zorder > 0))
			{
				function handleMarkerZOrder(marker, b)
				{
					var zorder = 0;
					if (isObject(marker.extraData.params.zorder))
					{
						if ((marker.extraData.params.zorder == -1) || (marker.extraData.params.zorder > 1000000))
							zorder = 1000000;
						else 
							zorder = marker.extraData.params.zorder;
					}
					return GOverlay.getZIndex(-90) + zorder;
				}
				gpar.zIndexProcess = handleMarkerZOrder;
			}
			else
				gpar.zIndexProcess = null;
			
			// Cr�er le marqueur
			marker = new GMarker(position, gpar);
			this.markers[id] = marker;
			marker.extraData = {
				id: id,
				infoWindowAnchor: gpar.icon.infoWindowAnchor,
				infoWindowAttractionSize: new GSize(gpar.icon.iconSize.width*0.75, gpar.icon.iconSize.height*0.75),
				params: clone(params)
			};
			
			// Gestion des �v�nements
			var objThis = this;
			if (gpar.draggable === true)
			{
				marker.enableDragging();
	    		GEvent.addListener(marker, 'dragend', function()
				{
	    			var center = marker.getLatLng();
					objThis.fireEvent("drop-marker", marker.extraData.id, center.lat(), center.lng());
	    		});
			}
			if (params.click === "showInfoWindow")
			{
				GEvent.addListener(marker, 'click', function()
				{
					var timer = setTimeout(function()
					{
						objThis.showInfoWindow(marker);
						timer = null;
					}, 200);
				});
			}
			else if (params.click === "custom")
			{
				GEvent.addListener(marker, 'click', function()
				{
					objThis.fireEvent("click-on-marker", marker.extraData.id);
				});
			}
			if (params.dblclk === "custom")
			{
				GEvent.addListener(marker, 'dblclick', function()
				{
					objThis.fireEvent("dblclick-on-marker", marker.extraData.id);
				});
			}
			
			// Ajouter le marqueur sur la carte
			if (isObject(this.manager))
			{
				this.manager.addMarker(marker, 0);
				this.manager.refresh();
			}
			else
				this.map.addOverlay(marker);
		}
		
		// Cas o� le marqueur existe
		else
		{
			// R�cup�rer les param�tres
			var markerParams = this.getMarkerDefinition(marker);
			id = marker.extraData.id;
			
			// Test si on doit compl�tement recr�er le marqueur (modification de icones, des events ou du zorder)
			if (isObject(params.icon) ||
				isObject(params.click) || isObject(params.dblclk) ||
				isObject(params.zorder))
			{
				// Les �craser avec ce qui a chang�
				for (prop in params)
					markerParams[prop] = params[prop];
					
				// Supprimer le marqueur
				this.removeMarker(id);
				
				// Le recr�er
				if (!this.setMarker(id, markerParams))
					return false;
				marker = this.getMarkerObject(id);
			}
			
			// Sinon modifications plus l�g�res
			else
			{
				// Changement de position
				if ((params.latitude !== markerParams.latitude) || (params.longitude !== markerParams.longitude))
				{
					var position = new GLatLng(isObject(params.latitude) ? params.latitude : markerParams.latitude, isObject(params.longitude) ? params.longitude : markerParams.longitude);
					marker.setLatLng(position);
				}
				
				// Recopier les champs
				for (prop in params)
					marker.extraData.params[prop] = params[prop];
			}
			
			// R�afficher
			if (isObject(this.manager))
				this.manager.refresh();
		}
		
		return true;
	},

	// Changer la position d'un marqueur
	setMarkerPosition: function(id, latitude, longitude)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		marker.setLatLng(new GLatLng(latitude, longitude));
		return true;
	},
	
	// Rechercher les marqueurs qui se trouvent entre deux coordonn�es
	getMarkersInSquare: function(bounds, fnValidate)
	{
		var arResult = new Array();
		var sw = bounds.getSouthWest();
		var ne = bounds.getNorthEast();
		var latTop = ne.lat();
		var latBottom = sw.lat();
		var lngLeft = sw.lng();
		var lngRight = ne.lng();
		var fnCompareLng = null;
		if (lngRight < lngLeft)
			fnCompareLng = function(pt) { return ((pt.lng() > lngLeft) || (pt.lng() < lngRight)); };
		else
			fnCompareLng = function(pt) { return ((pt.lng() > lngLeft) && (pt.lng() < lngRight)); };
		var mapMarkerPos;
		for (var id in this.markers)
		{
			var marker = this.markers[id];
			if (!(marker instanceof GMarker))
				continue;
			mapMarkerPos = marker.getLatLng();
			if (fnCompareLng(mapMarkerPos) &&
				(mapMarkerPos.lat() >= latBottom) && (mapMarkerPos.lat() <= latTop))
			{
				if (!fnValidate || fnValidate(marker))
					arResult.push(marker);
			}
		}
		return (arResult.length > 0) ? arResult : null;
	},
	
	// Afficher la fen�tre sur un marqueur
	_getInfoWindowMaxWidth: function()
	{
		var maxPercent = this.map.getSize().width * this.curParams.infoWidthPercent / 100;
		var maxWidth = 0;
		if (this.curParams.infoWidthAbsolute > 0)
		{
			maxWidth = this.curParams.infoWidthAbsolute;
			if ((maxPercent > 0) && (maxWidth > maxPercent))
				maxWidth = maxPercent;
		}
		else if (maxPercent > 0)
		{
			maxWidth = maxPercent;
			if ((this.curParams.infoWidthAbsolute > 0) && (maxWidth > this.curParams.infoWidthAbsolute))
				maxWidth = this.curParams.infoWidthAbsolute;
		}
		return maxWidth;
	},
	_getMarkerAttraction: function(marker)
	{
		var mapPosition = marker.getLatLng();
		var pixPosition = this._fromLatLngToPixel(mapPosition);
		var pixUpLeft = new GPoint(
			pixPosition.x - marker.extraData.infoWindowAttractionSize.width,
			pixPosition.y - marker.extraData.infoWindowAttractionSize.height);
		var mapUpLeft = this._fromPixelToLatLng(pixUpLeft);
		var pixBottomRight = new GPoint(
			pixPosition.x + marker.extraData.infoWindowAttractionSize.width,
			pixPosition.y + marker.extraData.infoWindowAttractionSize.height);
		var mapBottomRight = this._fromPixelToLatLng(pixBottomRight);
		return new GLatLngBounds(
			new GLatLng(mapBottomRight.lat(), mapUpLeft.lng()),
			new GLatLng(mapUpLeft.lat(), mapBottomRight.lng()));
	},
	_createSimpleInfoWindow: function(htmlContents, marker)
	{
		this.closeInfoWindow();
		var maxWidth = this._getInfoWindowMaxWidth();
		var params = new Array();
		if (maxWidth > 0)
			params.maxWidth = maxWidth;
		marker.openInfoWindowHtml(htmlContents, params);
	},
	_createMergedInfoWindow: function(htmlContents, markers, current, bounds)
	{
		this.closeInfoWindow();
		var maxWidth = this._getInfoWindowMaxWidth();
		if (!current || (current < 1))
			current = 1;
		if (current > markers.length)
			current = markers.length;
		var marker = markers[current-1];
		var params = new Array();
		if (maxWidth > 0)
			params.maxWidth = maxWidth;
		var objThis = this;
		var listener = GEvent.addListener(this.map, "infowindowopen", function()
		{
			GEvent.removeListener(listener);
			miw_init(function(index, html) {
					objThis._createMergedInfoWindow(html, markers, index, bounds);
				}, function() {
					if (bounds)
					{
						objThis.closeInfoWindow();
						objThis._setViewportBounds(bounds);
					}
				}, current);
		});
		marker.openInfoWindowHtml(htmlContents, params);
	},
	showInfoWindow: function(id)
	{
		if (!isObject(this.map))
			return false;
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		var htmlContents = marker.extraData.params.html;
		var markers = null;
		var bounds = null;
		var current = -1;
		if (this.curParams.mergeInfoWindows === true)
		{
			bounds = this._getMarkerAttraction(marker);
			markers = this.getMarkersInSquare(bounds, function(marker) { return (marker.extraData.params.html && marker.extraData.params.html.length) ? true : false; });
			if (markers && (typeof(markers) === "object") && (markers.length > 1))
			{
				var html = ''
				var navigator = '';
				for (var index = 0; index < markers.length; index++)
				{
					if (markers[index].extraData.id == marker.extraData.id)
						current = index;
					html += miw_formatContentPart(index+1, markers[index].extraData.params.html, (current == index) ? true : false);
					navigator += miw_formatNavigatorPart(index+1, markers[index].getTitle());
				}
				htmlContents = miw_formatHtml(markers.length, current+1, navigator, html);
			}
			else
				markers = null;
		}
		if ((typeof(htmlContents) === "string") && (htmlContents.length > 0))
		{
			if (!markers)
				this._createSimpleInfoWindow(htmlContents, marker);
			else
				this._createMergedInfoWindow(htmlContents, markers, current+1, bounds);
		}
		return true;
	},
	closeInfoWindow: function()
	{
		if (!isObject(this.map))
			return false;
		this.map.closeInfoWindow();
		return true;
	},
	
	// Afficher ou cacher un marqueur
	isMarkerVisible: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		return marker.isHidden() ? false : true;
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
		if (marker.isHidden())
			marker.show();
		else
			marker.hide();
		return true;
	},

	// Afficher ou cacher tous les marqueurs du manager
	areMarkersVisible: function()
	{
		if (isObject(this.manager))
			return this.manager.isHidden() ? false : true;
		else
		{
			var marker;
			for (var id in this.markers)
			{
				marker = this.markers[id];
				if (!(marker instanceof GMarker))
					continue;
				if (!marker.isHidden())
					return true;
			}
			return false;
		}
	},
	showMarkers: function(bShow)
	{
		if (isObject(this.manager))
		{
			if (bShow)
				this.manager.show();
			else
				this.manager.hide();
		}
		else
		{
			var marker;
			for (var id in this.markers)
			{
				marker = this.markers[id];
				if (!(marker instanceof GMarker))
					continue;
				if (bShow)
					marker.show();
				else
					marker.hide();
			}
		}
		return true;
	},
	toggleMarkers: function()
	{
		var bShow = this.areMarkersVisible();
		return this.showMarkers(!bShow);
	},
	
	
	//// FICHIERS KML
	
	// R�cup�rer un identifiant de marqueur non utilis�
	getNewLayerID: function()
	{
		var id = this.nextLayerID;
		while (this.layers[id])
			id++;
		this.nextLayerID = id+1;
		return id;
	},
	
	// R�cup�rer l'objet specifique du marqueur
	// Au cas o� on voudrait faire des d�veloppements sp�cifique � une impl�mentation
	getLayerObject: function(id)
	{
		var layer = null;
		if ((id instanceof GGeoXml) || (id instanceof GLayer) || (id instanceof GTileLayerOverlay))
			layer = id;
		else
			layer = this.layers[id];
		if (!isObject(layer) || !((layer instanceof GGeoXml) || (layer instanceof GLayer) || (layer instanceof GTileLayerOverlay)))
			return null;
		return layer;
	},
	
	// Test si un marqueur existe
	existLayer: function(id)
	{
		return (isObject(this.getLayerObject(id)));
	},
	
	// Cr�ation d'un trac� KML
	addLayerKML: function(id, url, show)
	{
		if (!isObject(this.map))
			return false;
			
		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		
		// Si le layer existe d�j�, le supprimer
		if (isObject(layer))
		{
			this.map.removeOverlay(layer);
			id = layer.extraData.id;
		}
			
		// Recr�er
		layer = new GGeoXml(url);
		if (isObject(layer))
		{
			this.layers[id] = layer;
			layer.extraData = {
				id: id,
				url: url
			};
			this.map.addOverlay(layer);
			var listener = GEvent.addListener(layer, 'load', function()
			{
				GEvent.removeListener(listener);
				if (show == false)
					this.hide();
			});
		}
		
		return true;
	},
	
	// Cr�ation d'une couche externe
	addLayerAuto: function(id, url, show)
	{
		if (!isObject(this.map))
			return false;

		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		
		// Si le layer existe d�j�, le supprimer
		if (isObject(layer))
		{
			this.map.removeOverlay(layer);
			id = layer.extraData.id;
		}
			
		// Recr�er
		layer = new GLayer(url);
		if (isObject(layer))
		{
			this.layers[id] = layer;
			layer.extraData = {
				id: id,
				url: url
			};
			if (show == false)
				layer.hide();
			this.map.addOverlay(layer);
		}
		
		return true;
	},
	
	// Supprimer une couche
	removeLayer: function(id)
	{
		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		id = layer.extraData.id;
		
		// Le supprimer
		this.map.removeOverlay(layer);
		this.layers[id] = null;
		delete layer;
		delete this.layers[id];
		
		return true;
	},
	
	// Afficher une couche
	showLayer: function(id, show)
	{
		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type (pour l'instant ils ont tous les fonction show et hide)
		if ((layer instanceof GGeoXml) || (layer instanceof GLayer) || (layer instanceof GTileLayerOverlay))
		{
			if (Boolean(show))
				layer.show();
			else
				layer.hide();
		}
		else 
			return false;
		
		return true;
	},
	toggleLayerVisibility: function(id)
	{
		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type
		if ((layer instanceof GGeoXml) || (layer instanceof GTileLayerOverlay))
		{
			if (layer.isHidden())
				layer.show();
			else
				layer.hide();
		}
		else if (layer instanceof GLayer)
		{
			if (GLayer.isHidden(layer.extraData.url))
				layer.show();
			else
				layer.hide();
		}
		else 
			return false;
		
		return true;
	},
	
	// Centrer la carte
	gotoLayerViewport: function(id)
	{
		// R�cup�rer le layer (et son id si on a pass� l'objet en param�tre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type (pour l'instant ils ont tous les fonction show et hide)
		if (layer instanceof GGeoXml)
			layer.gotoDefaultViewport(this.map);
		else 
			return false;
		
		return true;
	}
	
};

// Fermeture de la page
jQuery(document).unload(function()
{
	GUnload();
});

