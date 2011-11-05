/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Objet carte pour Google Maps API v3
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
	this.map = null;			// Objet Google Maps
	this.ge = null;			 	// Objet Google Earth
	this.geocoder = null;		// Objet geocoder
	this.div = null;			// Objet jQuery autour de la DIV qui contient la carte
	this.fakeOverlay = null;	// Objet OverlayView utilisé pour faire des conversions de coordonnées
	
	this.markers = new Array();	// liste des marqueurs
	this.nextMarkerID = 1;		// ID du prochain marqueur
	this.layers = new Array();	// liste des fichiers de couche (KML)
	this.nextLayerID = 1;		// ID de la prochaine couche KML
	this.icons = new Array();	// liste des icônes définies
	this.infoWindow = null;		// fenêtre volante (une seule autorisée)
	
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

// Test de la présence de google earth
MapWrapper._isEarth = function(name)
{
	if (!google || !google.earth)
		return false;
	if (!google.earth.isSupported())
		return false;
	if (!google.earth.isInstalled())
		return false;
	return true;
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
	mapTypes: ["plan", "satellite", "mixte", "physic"], // par défaut pas de "earth" car il faut avoir la clef...
	defaultMapType: "mixte",
	styleBackgroundCommand: "menu",
	positionBackgroundCommand: "RT",
//	styleNavigationCommand: "auto",
//	positionNavigationCommand: "LT",
	styleZoomCommand: "auto",
	positionZoomCommand: "LT",
	stylePanCommand: "large",
	positionPanCommand: "LT",
	styleScaleControl: "none",
	positionScaleControl: "BL",
	styleStreetViewCommand: "none",
	positionStreetViewCommand: "BL",
	styleRotationCommand: "none",
	positionRotationCommand: "BL",
	styleOverviewControl: "none",
	enableDblClkZoom: true,
	enableMapDragging: true,
	enableWheelZoom: false,
	keyboardShortcuts: false,
	infoWidthPercent: 65,
	infoWidthAbsolute: 300,
	handleResize: true,
	mergeInfoWindows: false
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
		this.popupOffsetX = isObject(params.popupOffsetX) ? params.popupOffsetX -(this.widthIcon / 2) : 0; // position calculée à partir de top/center
		this.popupOffsetY = isObject(params.popupOffsetY) ? params.popupOffsetY : this.heightIcon / 4;
	}
};
MapWrapper.IconDef.prototype =
{
	// Création d'une icone GMap
	createIcon: function()
	{
		var icon = new Array();
		this.updateIcon(icon);
		return icon;
	},
	
	// Mise à jour d'une icone
	updateIcon: function(icon)
	{
		if (!isObject(icon))
			return false;
			
		if (!isObject(icon.image))
			icon.image = new google.maps.MarkerImage(this.urlIconFile);
		if (!isObject(icon.shadow) && isObject(this.urlShadowFile))
			icon.shadow = new google.maps.MarkerImage(this.urlShadowFile);

		if (isObject(icon.image))
		{
			icon.image.url = this.urlIconFile;
			icon.image.size = new google.maps.Size(this.widthIcon, this.heightIcon);
			icon.image.scaledSize = null;
			icon.image.anchor = new google.maps.Point(this.anchorX, this.anchorY);
		}

		if (isObject(icon.shadow))
		{
			icon.shadow.url = this.urlShadowFile;
			icon.shadow.size = new google.maps.Size(this.widthShadow, this.heightShadow);
			icon.shadow.scaledSize = null;
			icon.shadow.anchor = new google.maps.Point(this.anchorShadowX, this.anchorShadowY);
		}
		
		icon.infoWindowAnchor = new google.maps.Size(this.popupOffsetX, this.popupOffsetY);
		
		return true;
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

// Définition de l'objet carte
MapWrapper.prototype =
{
	// Calcul des options de la carte en fonction des paramètres externes
	_translateMapType: function(type)
	{
		switch (type)
		{
		case "plan": return google.maps.MapTypeId.ROADMAP;
		case "satellite": return google.maps.MapTypeId.SATELLITE;
		case "mixte": return google.maps.MapTypeId.HYBRID;
		case "physic": return google.maps.MapTypeId.TERRAIN;
		case "earth": return GoogleEarth.MAP_TYPE_ID; // cf. plugin GoogleEarth
		}
		return google.maps.HYBRID;
	},
	_translateControlPosition: function(position)
	{
		switch (position)
		{
		case "TL": return google.maps.ControlPosition.TOP_LEFT;
		case "TC": return google.maps.ControlPosition.TOP_CENTER;
		case "TR": return google.maps.ControlPosition.TOP_RIGHT;
		case "RT": return google.maps.ControlPosition.RIGHT_TOP;
		case "RC": return google.maps.ControlPosition.RIGHT_CENTER;
		case "RB": return google.maps.ControlPosition.RIGHT_BOTTOM;
		case "BR": return google.maps.ControlPosition.BOTTOM_RIGHT;
		case "BC": return google.maps.ControlPosition.BOTTOM_CENTER;
		case "BL": return google.maps.ControlPosition.BOTTOM_LEFT;
		case "LB": return google.maps.ControlPosition.LEFT_BOTTOM;
		case "LC": return google.maps.ControlPosition.LEFT_CENTER;
		case "LT": return google.maps.ControlPosition.LEFT_TOP;
		}
		return google.maps.ControlPosition.BR;
	},
	_getOptions: function(params)
	{
		var mapOptions = new Array();
		
		// Désactiver l'interface par défaut (de toute manière tout est redéfini)
		mapOptions.disableDefaultUI = true;
		
		// Viewport
		if (isObject(params.viewZoom))
			mapOptions.zoom = params.viewZoom;
		if (isObject(params.viewLatitude) && isObject(params.viewLongitude))
			mapOptions.center = new google.maps.LatLng(params.viewLatitude, params.viewLongitude);

		// Types de cartes et commandes du type de carte
		if (isObject(params.defaultMapType))
			mapOptions.mapTypeId = this._translateMapType(params.defaultMapType);
		else
			mapOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
		if (!isObject(params.styleBackgroundCommand) || (params.styleBackgroundCommand != 'none'))
		{
			mapOptions.mapTypeControl = true;
			if (!isObject(mapOptions.mapTypeControlOptions))
				mapOptions.mapTypeControlOptions = new Array();
			if (isObject(params.mapTypes))
			{
				mapOptions.mapTypeControlOptions.mapTypeIds = new Array();
				for (index in params.mapTypes)
				{
					if ((params.mapTypes[index] === "earth") && !MapWrapper._isEarth())
						continue;
					mapOptions.mapTypeControlOptions.mapTypeIds.push(this._translateMapType(params.mapTypes[index]));
				}
			}
			else
			{
				mapOptions.mapTypeControlOptions.mapTypeIds =
					[ google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE,
					  google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN ];
			}
			mapOptions.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DEFAULT;
			if (isObject(params.styleBackgroundCommand))
			{
				switch (params.styleBackgroundCommand)
				{
					case 'button' : mapOptions.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.HORIZONTAL_BAR; break;
					case 'menu' : mapOptions.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU; break;
				}
			}
			mapOptions.mapTypeControlOptions.position = this._translateControlPosition("TR");
			if (isObject(params.positionBackgroundCommand))
				mapOptions.mapTypeControlOptions.position = this._translateControlPosition(params.positionBackgroundCommand);
		}
		else
			mapOptions.mapTypeControl = false;
		
		// Commandes de navigation (deprecated)
		/*if (!isObject(params.styleNavigationCommand) || (params.styleNavigationCommand != 'none'))
		{
			mapOptions.navigationControl = true;
			if (!isObject(mapOptions.navigationControlOptions))
				mapOptions.navigationControlOptions = new Array();
			mapOptions.navigationControlOptions.style = google.maps.ZoomControlStyle.DEFAULT;
			if (isObject(params.styleNavigationCommand))
			{
				switch (params.styleNavigationCommand)
				{
					case 'auto' : mapOptions.navigationControlOptions.style = google.maps.NavigationControlStyle.DEFAULT; break;
					case 'small' : mapOptions.navigationControlOptions.style = google.maps.NavigationControlStyle.SMALL; break;
					case 'android' : mapOptions.navigationControlOptions.style = google.maps.NavigationControlStyle.ANDROID; break;
					case 'large' : mapOptions.navigationControlOptions.style = google.maps.NavigationControlStyle.ZOOM_PAN; break;
				}
			}
			mapOptions.navigationControlOptions.position = this._translateControlPosition("LT");
			if (isObject(params.positionNavigationCommand))
				mapOptions.navigationControlOptions.position = this._translateControlPosition(params.positionNavigationCommand);
		}*/
		// Commande de zoom
		if (isObject(params.styleZoomCommand))
		{
			mapOptions.zoomControl = (params.styleZoomCommand != 'none') ? true : false;
			if (mapOptions.zoomControl)
			{
				if (!isObject(mapOptions.zoomControlOptions))
					mapOptions.zoomControlOptions = new Array();
				mapOptions.zoomControlOptions.style = google.maps.ZoomControlStyle.DEFAULT;
				switch (params.styleZoomCommand)
				{
					case 'auto' : mapOptions.zoomControlOptions.style = google.maps.ZoomControlStyle.DEFAULT; break;
					case 'small' : mapOptions.zoomControlOptions.style = google.maps.ZoomControlStyle.SMALL; break;
					case 'large' : mapOptions.zoomControlOptions.style = google.maps.ZoomControlStyle.LARGE; break;
				}
				if (isObject(params.positionZoomCommand))
					mapOptions.zoomControlOptions.position = this._translateControlPosition(params.positionZoomCommand);
			}
		}
		// Commande de pan
		if (isObject(params.stylePanCommand))
		{
			mapOptions.panControl = (params.stylePanCommand != 'none') ? true : false;
			if (mapOptions.panControl)
			{
				if (!isObject(mapOptions.panControlOptions))
					mapOptions.panControlOptions = new Array();
				if (isObject(params.positionPanCommand))
					mapOptions.panControlOptions.position = this._translateControlPosition(params.positionPanCommand);
			}
		}

		// Affichage de l'échelle
		if (isObject(params.styleScaleControl))
		{
			mapOptions.scaleControl = (params.styleScaleControl != 'none') ? true : false;
			if (mapOptions.scaleControl)
			{
				if (!isObject(mapOptions.scaleControlOptions))
					mapOptions.scaleControlOptions = new Array();
				mapOptions.scaleControlOptions.style = google.maps.ScaleControlStyle.DEFAULT;
				if (isObject(params.positionScaleControl))
					mapOptions.scaleControlOptions.position = this._translateControlPosition(params.positionScaleControl);
			}
		}
		
		// Affichage du bonhomme de StreetView
		if (isObject(params.styleStreetViewCommand))
		{
			mapOptions.streetViewControl = (params.styleStreetViewCommand != 'none') ? true : false;
			if (mapOptions.streetViewControl && isObject(params.positionStreetViewCommand))
			{
				if (!isObject(mapOptions.streetViewControlOptions))
					mapOptions.streetViewControlOptions = new Array();
				mapOptions.streetViewControlOptions.position = this._translateControlPosition(params.positionStreetViewCommand);
			}
		}
		
		// Commande de rotation
		if (isObject(params.styleRotationCommand))
		{
			mapOptions.rotateControl = (params.styleRotationCommand != 'none') ? true : false;
			if (mapOptions.rotateControl && isObject(params.positionRotationCommand))
			{
				if (!isObject(mapOptions.rotateControlOptions))
					mapOptions.rotateControlOptions = new Array();
				mapOptions.rotateControlOptions.position = this._translateControlPosition(params.positionRotationCommand);
			}
		}
		
		// Commande d'overview
		if (isObject(params.styleOverviewControl))
		{
			mapOptions.overviewMapControl = (params.styleOverviewControl != 'none') ? true : false;
			if (mapOptions.overviewMapControl)
			{
				if (!isObject(mapOptions.overviewMapControlOptions))
					mapOptions.overviewMapControlOptions = new Array();
				mapOptions.overviewMapControlOptions.opened = (params.styleOverviewControl === 'open') ? true : false;
			}
		}
		
		// Options
		if (isObject(params.enableDblClkZoom))
			mapOptions.disableDoubleClickZoom = params.enableDblClkZoom ? false : true;
		if (isObject(params.enableMapDragging))
			mapOptions.draggable = params.enableMapDragging;
		if (isObject(params.enableWheelZoom))
			mapOptions.scrollwheel = params.enableWheelZoom;
		if (isObject(params.enableKeyboard))
			mapOptions.keyboardShortcuts = params.enableKeyboard;
		
		return mapOptions;
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
		var divElem = div.get(0);

		// Copier les paramètres dans curParams, en prenant les valeurs par défaut là où elles ne sont pas fournies
		function _param(name) { return (isObject(params) && (typeof params[name] != 'undefined')) ? params[name] : MapWrapper.defaultParams[name]; }
		for (var elem in MapWrapper.defaultParams)
			this.curParams[elem] = _param(elem);

		// Créer et initialiser la carte
		var mapOptions = this._getOptions(this.curParams);
		this.div = div;
		this.map = new google.maps.Map(divElem, mapOptions);
		if (arrayContains(params.mapTypes, "earth") && MapWrapper._isEarth())
			this.ge = new GoogleEarth(this.map);

		// Pour une raison que je ne comprends pas encore, les options ne sont pas bien prisent en compte
		// lors de la création initiale, donc les repasser...
		this.map.setOptions(mapOptions);
		
		// Création d'une icone par defaut
		this.setIcon("default");
		
		// Geocoder
		this.geocoder = new google.maps.Geocoder();

		// Overlay utilisé pour les projections
		FakeOverlay.prototype = new google.maps.OverlayView();
		FakeOverlay.prototype.onAdd = function() { }
		FakeOverlay.prototype.onRemove = function() { }
		FakeOverlay.prototype.draw = function() { }
		function FakeOverlay(map) { this.setMap(map); }
		this.fakeOverlay = new FakeOverlay(this.map);

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
	// clic-on-marker = clic sur un marqueur
	// drag-marker = déplacement d'un marqueur -> function(event, zoom)
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
					google.maps.event.addListener(this.map, "click", function(mouseevent)
					{
						// On gère seulement les clicks sur la carte
						var latlng = mouseevent.latLng;
						if (latlng != null)
							objThis.fireEvent("click-on-map", latlng.lat(), latlng.lng());
					});
				}
				break;
			// Changement du facteur de zoom
			case "zoom":
				{
					google.maps.event.addListener(this.map, "zoom_changed", function(oldlevel, newlevel)
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
	},
	
	// Effacement de la carte
	unload: function()
	{
		this.freeListeners();
		
		if (isObject(this.infoWindow))
			this.closeInfoWindow;
			
		if (isObject(this.fakeOverlay))
			delete this.fakeOverlay;
		this.fakeOverlay = null;
		
		if (isObject(this.geocoder))
			delete this.geocoder;
		this.geocoder = null;
		
		this.div.unbind("resize", MapWrapper.cbOnResize);
		this.div = null;

		if (isObject(this.curParams))
			delete this.curParams;
		this.curParams = null;
		
		if (isObject(this.ge))
			delete this.ge;
		this.ge = null;
		if (isObject(this.map))
		{
			google.maps.event.clearInstanceListeners(this.map);
			delete this.map;
		}
		this.map = null;
	},
	
	// Affectation du type de carte
	setMapType: function(type)
	{
		if (!isObject(this.map) || !isObject(type))
			return false;
		this.map.setMapTypeId(this._translateMapType(type));
		return true;
	},
	
	// Changement des paramètres
	update: function(params)
	{
		if (!isObject(this.map))
			return false;
			
		// Copier les paramètres
		for (var elem in MapWrapper.defaultParams)
			if (isObject(params[elem]))
				this.curParams[elem] = params[elem];

		// Modifier la carte
		var mapOptions = this._getOptions(this.curParams);
		this.map.setOptions(mapOptions);
		
		return true;
	},
	
	// Changement du centre
	_updateCurViewport: function()
	{
		this.curParams.viewZoom = this.map.getZoom();
		var center = this.map.getCenter();
		this.curParams.viewLatitude = center.lat();
		this.curParams.viewLongitude = center.lng();
	},
	setViewport: function(latitude, longitude, zoom)
	{
		if (!isObject(this.map))
			return false;
		this.map.setCenter(new google.maps.LatLng(latitude, longitude));
		this.map.setZoom(zoom);
		this._updateCurViewport();
		return true;
	},
	_setViewportBounds: function(bounds)
	{
		if (!isObject(this.map))
			return false;
		this.map.fitBounds(bounds);
		this._updateCurViewport();
		return true;
	},
	setViewportBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		return this._setViewportBounds(new google.maps.LatLngBounds(new google.maps.LatLng(minLatitude, minLongitude), new google.maps.LatLng(maxLatitude, maxLongitude)));
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
	setCenter: function(latitude, longitude)
	{
		if (!isObject(this.map))
			return false;
		this.map.setCenter(new google.maps.LatLng(latitude, longitude));
		this._updateCurViewport();
		return true;
	},
	panTo: function(latitude, longitude)
	{
		if (!isObject(this.map))
			return false;
		this.map.panTo(new google.maps.LatLng(latitude, longitude));
		this._updateCurViewport();
		return true;
	},
	panToBounds: function(minLatitude, minLongitude, maxLatitude, maxLongitude)
	{
		if (!isObject(this.map))
			return false;
		this.map.panToBounds(new google.maps.LatLngBounds(new google.maps.LatLng(minLatitude, minLongitude), new google.maps.LatLng(maxLatitude, maxLongitude)));
		this._updateCurViewport();
		return true;
	},
	setZoom: function(zoom)
	{
		if (!isObject(this.map))
			return false;
		this.map.setZoom(zoom);
		this._updateCurViewport();
		return true;
	},
	
	// Redimensionnement
	onResize: function()
	{
		if (!isObject(this.map))
			return;

		// Récupérer le positionnement
		var center = this.map.getCenter();

		// Mécanique interne
		google.maps.event.trigger(this.map, 'resize');

		// Rechargement
		this.map.setCenter(center);
	},
	
	// Fonctions de conversion entre lat/lng et pixel
	_fromLatLngToPixel: function(latlng)
	{
		var proj = isObject(this.fakeOverlay) ? this.fakeOverlay.getProjection() : null;
		if (isObject(proj))
			return proj.fromLatLngToDivPixel(latlng);
		else
		{
			proj = isObject(this.map) ? this.getProjection() : null;
			if (isObject(proj))
			{
				var mapBounds = this.map.getBounds();
				var mapTopRight = proj.fromLatLngToPoint(mapBounds.getNorthEast());
				var mapBottomLeft = proj.fromLatLngToPoint(mapBounds.getSouthWest());
				var scale = Math.pow(2, this.map.getZoom()); 
				var worldPoint = proj.fromLatLngToPoint(latlng);
				var divPoint = new google.maps.Point(
					(worldPoint.x - mapBottomLeft.x) * scale,
					(worldPoint.y - mapTopRight.y) * scale); 
				return divPoint;
			}
			else
				return new google.maps.Point(0,0);
		}
	},
	_fromPixelToLatLng: function(point)
	{
		var proj = isObject(this.fakeOverlay) ? this.fakeOverlay.getProjection() : null;
		if (isObject(proj))
			return proj.fromDivPixelToLatLng(point);
		else
		{
			proj = isObject(this.map) ? this.getProjection() : null;
			if (isObject(proj))
			{
				var mapBounds = this.map.getBounds();
				var mapTopRight = proj.fromLatLngToPoint(mapBounds.getNorthEast());
				var mapBottomLeft = proj.fromLatLngToPoint(mapBounds.getSouthWest());
				var scale = Math.pow(2, this.map.getZoom()); 
				var worldPoint = new google.maps.Point(
					(point.x / scale) + mapBottomLeft.x,
					(point.y / scale) + mapTopRight.y); 
				return proj.fromPointToLatLng(worldPoint);
			}
			else
				return point;
		}
	},
	
	
	//// GEOCODER
	
	// Recherche par le geocoder
	searchGeocoder: function(address, callback)
	{
		if (!isObject(this.geocoder))
			return false;
		this.geocoder.geocode({ address: address }, function(results, status)
		{
			if ((status == google.maps.GeocoderStatus.OK) && (results.length > 0))
			{
				var aLocation = results[0].geometry.location;
				callback.call(this, aLocation.lat(), aLocation.lng());
			}
			else if ((status == google.maps.GeocoderStatus.ZERO_RESULTS) || (results.length == 0))
				callback.call(this, null, null);
		});
		return true;
	},
	queryGeocoder: function(address, callback)
	{
		if (!isObject(this.geocoder))
			return false;
		this.geocoder.geocode({ address: address }, function(results, status)
		{
			if ((status == google.maps.GeocoderStatus.OK) && (results.length > 0))
			{
				var locations = new Array();
				for (var index in results)
				{
					var aLocation = new Array();
					aLocation.name = results[index].formatted_address;
					/*for (var indAC in results[index].address_components)
					{
						if (aLocation.name != "") aLocation.name += ", ";
						aLocation.name += results[index].address_components[indAC].long_name;
					}*/
					aLocation.latitude = results[index].geometry.location.lat();
					aLocation.longitude = results[index].geometry.location.lng();
					locations.push(aLocation);
				}
				callback.call(this, locations);
			}
			else if ((status == google.maps.GeocoderStatus.ZERO_RESULTS) || (results.length == 0))
				callback.call(this, null);
		});
		return true;
	},
	
	
	//// ICONS
	
	// Ajout ou modification d'une icone
	// name : nom de l'icone
	// params : paramètres (selon la définition de MapWrapper.IconDef
	setIcon: function(name, params)
	{
		var def = new MapWrapper.IconDef(params);
		if (!isObject(this.icons[name]))
			this.icons[name] = def.createIcon();
		else
			def.updateIcon(this.icons[name]);
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
		if (id instanceof google.maps.Marker)
			marker = id;
		else
			marker = this.markers[id];
		if (!isObject(marker) || !(marker instanceof google.maps.Marker))
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
		
		var center = marker.getPosition();
		params.latitude = center.lat();
		params.longitude = center.lng();
		
		return params;
	},
	
	// Suppression d'un marqueur
	removeMarker: function(id)
	{
		// Récupérer le marqueur (et son id si on a passé l'objet en paramètre)
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		id = marker.extraData.id;

		// Le supprimer du tableau global
		this.markers[id] = null;
		delete this.markers[id];
			
		// Détruire le marker
		marker.setMap(null);
		google.maps.event.clearInstanceListeners(marker);
		delete marker;
		
		return true;
	},
	
	// Traduction des options
	_getMarkerOptions: function(params, bUseDefaults)
	{
		// Paramètres du marqueur (google.maps.MarkerOptions)
		var markerOptions = new Object();
		
		// Position
		if (isObject(params.latitude) || isObject(params.longitude) || bUseDefaults)
		{
			var center = this.map.getCenter();
			markerOptions.position = new google.maps.LatLng(
								isObject(params.latitude) ? params.latitude : center.lat(),
								isObject(params.longitude) ? params.longitude : center.lng());
		}
		
		// Icone
		var icon = null;
		if (isObject(params.icon))
			icon = this.getIcon(params.icon);
		else if (bUseDefaults)
			icon = this.getIcon("default");
		if (icon)
		{
			markerOptions.icon = icon.image;
			markerOptions.flat = icon.shadow ? false : true;
			if (!markerOptions.flat)
				markerOptions.shadow = icon.shadow;
			markerOptions.infoWindowAnchor = icon.infoWindowAnchor;
			markerOptions.infoWindowAttractionSize = new google.maps.Size(icon.image.size.width*0.60, icon.image.size.height*0.60);
		}
		
		// Titre	
		if (isObject(params.title))
			markerOptions.title = params.title;
			
		// Marqueur cliquable ?
		if (isObject(params.click) || bUseDefaults)
			markerOptions.clickable = isObject(params.click) ? true : false;
			
		// Marqueur déplaçable ?
		if (isObject(params.draggable) || bUseDefaults)
			markerOptions.draggable = isObject(params.draggable) ? params.draggable : false;
			
		// Z-order
		if (isObject(params.zorder))
			markerOptions.zIndex = Number(params.zorder);

		return markerOptions;
	},
	
	// Mise à jour des handlers d'évènements
	_updateEventHandlers: function(marker, params)
	{
		var objThis = this;
		
		// Déplacements
		if (isObject(params.draggable))
		{
			if (isObject(marker.extraData.handlerDragEnd))
				google.maps.event.removeListener(marker.extraData.handlerDragEnd);
			if (params.draggable === true)
			{
				marker.extraData.handlerDragEnd = google.maps.event.addListener(marker, 'dragend', function(mouseEvent)
				{
					var center = marker.getPosition();
					objThis.fireEvent("drop-marker", marker.extraData.id, center.lat(), center.lng());
				});
			}
		}
		
		// Clic
		if (isObject(params.click))
		{
			if (isObject(marker.extraData.handlerClick))
				google.maps.event.removeListener(marker.extraData.handlerClick);
			if (params.click === "showInfoWindow")
			{
				marker.extraData.handlerClick = google.maps.event.addListener(marker, 'click', function(mouseEvent)
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
				marker.extraData.handlerClick = google.maps.event.addListener(marker, 'click', function(mouseEvent)
				{
					objThis.fireEvent("click-on-marker", marker.extraData.id);
				});
			}
		}
		
		// Doubleclick
		if (isObject(params.dblclick))
		{
			if (isObject(marker.extraData.handlerDblClick))
				google.maps.event.removeListener(marker.extraData.handlerDblClick);
			if (params.dblclk === "custom")
			{
				marker.extraData.handlerDblClick = google.maps.event.addListener(marker, 'dblclick', function(mouseEvent)
				{
					objThis.fireEvent("dblclick-on-marker", marker.extraData.id);
				});
			}
		}
	},
	
	// Ajout ou modification d'un marqueur
	// id: identifiant unique de ce marqueur
	// params : paramètres du marqueur
	setMarker: function(id, params)
	{
		// Récupérer le marqueur (et son id si on a passé l'objet en paramètre)
		var marker = this.getMarkerObject(id);
		
		// Cas où le marqueur n'existe pas
		if (!isObject(marker))
		{
			// Paramètres du marqueur (google.maps.MarkerOptions)
			var markerOptions = this._getMarkerOptions(params, true);
			
			// Créer le marqueur
			marker = new google.maps.Marker(markerOptions);
			this.markers[id] = marker;
			marker.extraData = {
				id: id,
				infoWindowAnchor: markerOptions.infoWindowAnchor,
				infoWindowAttractionSize: markerOptions.infoWindowAttractionSize,
				params: clone(params)
			};
			
			// Gestion des évènements
			this._updateEventHandlers(marker, params);
			
			// Ajouter le marqueur sur la carte
			marker.setMap(this.map);
		}
		
		// Cas où le marqueur existe
		else
		{
			// Récupérer les paramètres
			var markerParams = this.getMarkerDefinition(marker);
			id = marker.extraData.id;

			// Paramètres du marqueur (google.maps.MarkerOptions)
			var markerOptions = this._getMarkerOptions(params, false);
			
			// Mettre à jour
			marker.setOptions(markerOptions);
			
			// Recopier les champs
			if (isObject(params.icon))
			{
				marker.extraData.infoWindowAnchor = markerOptions.infoWindowAnchor;
				marker.extraData.infoWindowAttractionSize = markerOptions.infoWindowAttractionSize;
			}
			for (prop in params)
				marker.extraData.params[prop] = params[prop];
				
			// Gestion des évènements
			this._updateEventHandlers(marker, params);
		}
		
		return true;
	},

	// Changer la position d'un marqueur
	setMarkerPosition: function(id, latitude, longitude)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		marker.setPosition(new google.maps.LatLng(latitude, longitude));
		return true;
	},
	
	// Rechercher les marqueurs qui se trouvent entre deux coordonnées
	getMarkersInSquare: function(bounds)
	{
		var arResult = new Array();
		var sw = bounds.getSouthWest();
		var ne = bounds.getNorthEast();
		var latTop = ne.lat();
		var latBottom = sw.lat();
		var lngLeft = (sw.lng() > ne.lng()) ? ne.lng() : sw.lng();
		var lngRight = (sw.lng() > ne.lng()) ? sw.lng() : ne.lng();
		var mapMarkerPos;
		for (var id in this.markers)
		{
			var marker = this.markers[id];
			if (!(marker instanceof google.maps.Marker))
				continue;
			mapMarkerPos = marker.getPosition();
			if ((mapMarkerPos.lng() >= lngLeft) && (mapMarkerPos.lng() <= lngRight) &&
				(mapMarkerPos.lat() >= latBottom) && (mapMarkerPos.lat() <= latTop))
				arResult.push(marker);
		}
		return (arResult.length > 0) ? arResult : null;
	},
	
	// Afficher la fenêtre sur un marqueur
	_getInfoWindowMaxWidth: function()
	{
		var maxPercent = jQuery(this.map.getDiv()).width() * this.curParams.infoWidthPercent / 100;
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
		var mapPosition = marker.getPosition();
		var pixPosition = this._fromLatLngToPixel(mapPosition);
		var pixUpLeft = new google.maps.Point(
			pixPosition.x - marker.extraData.infoWindowAttractionSize.width,
			pixPosition.y - marker.extraData.infoWindowAttractionSize.height);
		var mapUpLeft = this._fromPixelToLatLng(pixUpLeft);
		var pixBottomRight = new google.maps.Point(
			pixPosition.x + marker.extraData.infoWindowAttractionSize.width,
			pixPosition.y + marker.extraData.infoWindowAttractionSize.height);
		var mapBottomRight = this._fromPixelToLatLng(pixBottomRight);
		return new google.maps.LatLngBounds(
			new google.maps.LatLng(mapBottomRight.lat(), mapUpLeft.lng()),
			new google.maps.LatLng(mapUpLeft.lat(), mapBottomRight.lng()));
	},
	_createSimpleInfoWindow: function(htmlContents, marker)
	{
		if (isObject(this.infoWindow))
			this.closeInfoWindow();
		var maxWidth = this._getInfoWindowMaxWidth();
		var params = {
				content: htmlContents,
				pixelOffset: marker.extraData.infoWindowAnchor };
		if (maxWidth > 0)
			params.maxWidth = maxWidth;
		this.infoWindow = new google.maps.InfoWindow(params);
		var objThis = this;
		google.maps.event.addListenerOnce(this.infoWindow, "domready", function()
		{
			objThis.fireEvent("info-window-open");
		});
		this.infoWindow.open(this.map, marker);
	},
	_createMergedInfoWindow: function(htmlContents, markers, current, bounds)
	{
		if (isObject(this.infoWindow))
			this.closeInfoWindow();
		var maxWidth = this._getInfoWindowMaxWidth();
		if (!current || (current < 1))
			current = 1;
		if (current > markers.length)
			current = markers.length;
		var marker = markers[current-1];
		var params = {
				content: htmlContents,
				pixelOffset: marker.extraData.infoWindowAnchor };
		if (maxWidth > 0)
			params.maxWidth = maxWidth;
		this.infoWindow = new google.maps.InfoWindow(params);
		var objThis = this;
		google.maps.event.addListenerOnce(this.infoWindow, "domready", function()
		{
			miw_init(function(index, html) {
					objThis._createMergedInfoWindow(html, markers, index, bounds);
				}, function() {
					if (bounds)
					{
						objThis.closeInfoWindow();
						objThis._setViewportBounds(bounds);
					}
				}, current);
			objThis.fireEvent("info-window-open");
		});
		this.infoWindow.open(this.map, marker);
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
		if (this.curParams.mergeInfoWindows === true)
		{
			bounds = this._getMarkerAttraction(marker);
			markers = this.getMarkersInSquare(bounds);
			if (markers && (typeof(markers) === "object") && (markers.length > 1))
			{
				var html = ''
				var navigator = '';
				for (var index = 0; index < markers.length; index++)
				{
					html += miw_formatContentPart(index+1, markers[index].extraData.params.html);
					navigator += miw_formatNavigatorPart(index+1, markers[index].getTitle());
				}
				htmlContents = miw_formatHtml(markers.length, 1, navigator, html);
			}
			else
				markers = null;
		}
		if ((typeof(htmlContents) === "string") && (htmlContents.length > 0))
		{
			if (!markers)
				this._createSimpleInfoWindow(htmlContents, marker);
			else
				this._createMergedInfoWindow(htmlContents, markers, 1, bounds);
		}
		return true;
	},
	closeInfoWindow: function()
	{
		if (!isObject(this.map))
			return false;
		if (isObject(this.infoWindow))
		{
			this.infoWindow.close();
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
		return marker.getVisible() ? true : false;
	},
	showMarker: function(id, bShow)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		marker.setVisible(bShow);
		return true;
	},
	toggleMarker: function(id)
	{
		var marker = this.getMarkerObject(id);
		if (marker == null)
			return false;
		var bShow = !marker.getVisible();
		marker.setVisible(bShow);
		return true;
	},

	// Afficher ou cacher tous les marqueurs 
	areMarkersVisible: function()
	{
		var marker;
		for (var id in this.markers)
		{
			marker = this.markers[id];
			if (!(marker instanceof google.maps.Marker))
				continue;
			if (marker.getVisible())
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
			if (!(marker instanceof google.maps.Marker))
				continue;
			marker.setVisible(bShow);
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
		var id = this.nextLayerID;
		while (this.layers[id])
			id++;
		this.nextLayerID = id+1;
		return id;
	},
	
	// Récupérer l'objet specifique du marqueur
	// Au cas où on voudrait faire des développements spécifique à une implémentation
	getLayerObject: function(id)
	{
		var layer = null;
		if (id instanceof google.maps.KmlLayer)
			layer = id;
		else
			layer = this.layers[id];
		if (!isObject(layer) || !(layer instanceof google.maps.KmlLayer))
			return null;
		return layer;
	},
	
	// Test si un marqueur existe
	existLayer: function(id)
	{
		return (isObject(this.getLayerObject(id)));
	},
	
	// Création d'un tracé KML
	addLayerKML: function(id, url, show)
	{
		if (!isObject(this.map))
			return false;
			
		// Récupérer le layer (et son id si on a passé l'objet en paramètre)
		var layer = this.getLayerObject(id);
		
		// Si le layer existe déjà, le supprimer
		if (isObject(layer))
		{
			this.map.removeOverlay(layer);
			id = layer.extraData.id;
		}
			
		// Recréer
		layer = new google.maps.KmlLayer(url);
		if (isObject(layer))
		{
			this.layers[id] = layer;
			layer.extraData = {
				id: id,
				url: url
			};
			if (show === true)
				layer.setMap(this.map);
		}
		
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
		// Récupérer le layer (et son id si on a passé l'objet en paramètre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		id = layer.extraData.id;
		
		// Le supprimer
		layer.setMap(null);
		this.layers[id] = null;
		delete layer;
		delete this.layers[id];
		
		return true;
	},
	
	// Afficher une couche
	showLayer: function(id, show)
	{
		// Récupérer le layer (et son id si on a passé l'objet en paramètre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type (pour l'instant ils ont tous les fonction show et hide)
		if (layer instanceof google.maps.KmlLayer)
		{
			if (Boolean(show))
				layer.setMap(this.map);
			else
				layer.setMap(null);
		}
		else 
			return false;
		
		return true;
	},
	toggleLayerVisibility: function(id)
	{
		// Récupérer le layer (et son id si on a passé l'objet en paramètre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type
		if (layer instanceof google.maps.KmlLayer)
		{
			if (layer.getMap() != this.map)
				layer.setMap(this.map);
			else
				layer.setMap(null);
		}
		else 
			return false;
		
		return true;
	},
	
	// Centrer la carte
	gotoLayerViewport: function(id)
	{
		// Récupérer le layer (et son id si on a passé l'objet en paramètre)
		var layer = this.getLayerObject(id);
		if (!isObject(layer))
			return false;
		
		// Afficher/cacher selon le type (pour l'instant ils ont tous les fonction show et hide)
		if (layer instanceof google.maps.KmlLayer)
		{
			var bounds = layer.getDefaultViewport();
			if (isObject(bounds))
			{
				this.map.panToBounds(bounds);
				this._updateCurViewport();
			}
		}
		else 
			return false;
		
		return true;
	}
	
};

// Fermeture de la page
jQuery(document).unload(function()
{
	MapWrapper.freeAllMaps();
});

