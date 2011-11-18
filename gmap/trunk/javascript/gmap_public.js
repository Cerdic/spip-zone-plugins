/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Scripts additionnels, ind�pendants de l'impl�mentation utilis�s dans la partie publique
 *
 */
 

 
/***
 * �v�nements de chargement de la carte
 */
 
// Utilitaire pour envoyer le gmapReady
jQuery.fn.triggerGmapReady = function(mapId)
{
	this.attr("mapId", mapId);
	this.triggerHandler("gmapReady");
};

// Raccourci pour ajouter des �v�nements
jQuery.fn.gmapReady = function(handler)
{
	var mapId = this.attr("mapId");
	var map = gMap(mapId);
	if (isObject(map) && map.isLoaded())
		handler.call();
	else
		this.bind("gmapReady", handler);
};


 
/***
 * Objet d�di� � l'analyse XML
 */

// D�finition de la classe XMLParser
function XMLParser(navigationHandler, openTagHandler, closeTagHandler)
{
	this.xmlDoc = null;
	this.navigationHandler = navigationHandler;
	this.openTagHandler = openTagHandler;
	this.closeTagHandler = closeTagHandler;
};

// Valeurs par d�faut des commande de navigation
XMLParser.defaultNavCommands = {
	bUseAttributes: 0,
	bUseChilds: 0,
	bUseContents: 0,
	arAttrsFilter: []
};

// M�thodes de l'objet
XMLParser.prototype = {

	// R�cup�rer ou cr�er une carte
	parse: function(xmlDoc)
	{
		this.xmlDoc = xmlDoc;
		this.doParse(this.xmlDoc);
	},

	// Fonction principale du parser
	doParse: function(domElement)
	{
		// Tester l'information n�cessaire sur l'�l�ment
		var name = domElement.nodeName;
		var navCommands = XMLParser.defaultNavCommands;
		if (this.navigationHandler)
			navCommands = this.navigationHandler(name);
		
		// R�cup�rer les attributs
		var attrs = null;
		if (navCommands.bUseAttributes)
		{
			src = domElement.attributes;
			if (isObject(src) && (src.length > 0))
			{
				attrs = new Array();
				for (indAttr = 0; indAttr < src.length; indAttr++)
					attrs[src[indAttr].nodeName] = src[indAttr].nodeValue;
			}
		}
		
		// R�cup�rer le contenu
		var contents = null;
		if (navCommands.bUseContents)
		{
			var nbChilds = domElement.childNodes.length;
			var indChild;
			for (indChild = 0; indChild < nbChilds; indChild++)
			{
				childElement = domElement.childNodes[indChild];
				if (childElement.nodeType != 1)
				{
					if (!contents)
						contents = childElement.nodeValue;
					else
						contents += childElement.nodeValue;
				}
			}
		}
		
		// Si on a des attributs ou du contenu, notifier l'ouverture de tag
		var bOpenCalled = false;
		if (this.openTagHandler && (attrs || contents))
		{
			this.openTagHandler(name, attrs, contents);
			bOpenCalled = true;
		}
			
		// Si on a demand� les fils ou le contenu, parcourir le contenu
		if (navCommands.bUseChilds)
		{
			var nbChilds = domElement.childNodes.length;
			var indChild;
			for (indChild = 0; indChild < nbChilds; indChild++)
			{
				childElement = domElement.childNodes[indChild];
				if (childElement.nodeType == 1)
					this.doParse(childElement);
			}
		}
		
		// Fermer le tag
		if (this.closeTagHandler && bOpenCalled)
			this.closeTagHandler(name);
	}
};



/***
 * Gestion des fichiers de d�finition des marqueurs
 */
 
// Formatage du texte HTML des bulles
function _gmap_getHtmlContents(contents)
{
	if (typeof(contents) != "string")
		return contents;
	// Inutile : juste pour tester et avoir exactement le m�me contenu qu'en JSON
	// contents = contents.replace(/[\n\r\t]/g, "");
	var matches = contents.match(/<body([^>]*)>([\s\S]*)<\/body>/mi);
	if (!matches)
		return contents;
	else
		return matches[2];
}

// item : item XML contenant les infos au format KML+Gmap
function gmap_addKMLMarker(map, item)
{
	var markerParams = new Array();
	var iconName = "default";
	var icon = new Array();
	var iconSel = new Array();
	
	// Parsing du fichier XML
	var iconCurrent = null;
	var bShadow = false;
	var parser = new XMLParser(
		function navigationHandler(name) {
			var params = {
				bUseAttributes: 0,
				bUseChilds: 1,
				bUseContents: 0,
				arAttrsFilter: []
			};
			if ((name == "open") || (name == "atom:link") || (name == "styleUrl"))
				params.bUseChilds = 0;
			if ((name == "text") || (name == "visibility") || (name == "name") || (name == "SimpleData") || (name == "coordinates") || (name == "gmm:image"))
				params.bUseContents = 1;
			if ((name == "gmm:markerParams") || (name == "gmm:markers") || (name == "gmm:icon") || (name == "gmm:iconShort") || (name == "gmm:size") || (name == "gmm:anchor") || (name == "gmm:offset"))
				params.bUseAttributes = 1;
			return params;
		},
		function openTagHandler(name, attrs, contents) {
			if (name == "text")
			{
				markerParams['click'] = "showInfoWindow";
				markerParams['html'] = _gmap_getHtmlContents(contents);
			}
			else if (name == "coordinates")
			{
				var coords = contents.split(',');
				markerParams['longitude'] = parseFloat(coords[0]);
				markerParams['latitude'] = parseFloat(coords[1]);
			}
			else if (name == "name")
			{
				markerParams['title'] = contents;
			}
			else if (name == "gmm:markerParams")
			{
				for (name in attrs)
				{
					if ((name == 'objectId') || (name == 'zoom') || (name == 'priority'))
						markerParams[name] = parseInt(attrs[name]);
					else
						markerParams[name] = attrs[name]
				}
			}
			else if (name == "gmm:markers")
			{
				if (isObject(attrs['id']))
					iconName = attrs['id'];
				else if (isObject(attrs['ref']))
					iconName = attrs['ref'];
			}
			else if (name == "gmm:icon")
			{
				var type = attrs['type'];
				if (!isObject(type))
					type = "simple";
				bShadow = (type == "shadow") ? true : false;
				if ((type == "simple") || (type == "shadow"))
				{
					if (attrs['state'] == "selected")
						iconCurrent = iconSel
					else
						iconCurrent = icon;
				}
				else
					iconCurrent = null;
			}
			else if (name == "gmm:iconShort")
			{
				if (attrs['state'] == "selected")
					iconCurrent = iconSel;
				else
					iconCurrent = icon;
				if (attrs['type'] == "shadow")
				{
					iconCurrent['urlShadowFile'] = attrs['url'];
					iconCurrent['widthShadow'] = parseInt(attrs['cxSize']);
					iconCurrent['heightShadow'] = parseInt(attrs['cySize']);
					iconCurrent['anchorShadowX'] = parseInt(attrs['xAnchor']);
					iconCurrent['anchorShadowY'] = parseInt(attrs['yAnchor']);
				}
				else if (attrs['type'] == "complete")
				{
					iconCurrent['urlCompleteFile'] = attrs['url'];
					iconCurrent['widthComplete'] = parseInt(attrs['cxSize']);
					iconCurrent['heightComplete'] = parseInt(attrs['cySize']);
					iconCurrent['anchorCompleteX'] = parseInt(attrs['xAnchor']);
					iconCurrent['anchorCompleteY'] = parseInt(attrs['yAnchor']);
				}
				else
				{
					iconCurrent['urlIconFile'] = attrs['url'];
					iconCurrent['widthIcon'] = parseInt(attrs['cxSize']);
					iconCurrent['heightIcon'] = parseInt(attrs['cySize']);
					iconCurrent['anchorX'] = parseInt(attrs['xAnchor']);
					iconCurrent['anchorY'] = parseInt(attrs['yAnchor']);
				}
				if (attrs['xOffset'] && attrs['yOffset'])
				{
					iconCurrent['popupOffsetX'] = parseInt(attrs['xOffset']);
					iconCurrent['popupOffsetY'] = parseInt(attrs['yOffset']);
				}
				iconCurrent = null; // C'est le pointeur, on a rempli icon ou iconSel
			}
			else if (name == "gmm:image")
			{
				if (iconCurrent)
				{
					if (bShadow)
						iconCurrent['urlShadowFile'] = contents;
					else
						iconCurrent['urlIconFile'] = contents;
				}
			}
			else if (name == "gmm:size")
			{
				if (iconCurrent)
				{
					if (bShadow)
					{
						iconCurrent['widthShadow'] = parseInt(attrs['x']);
						iconCurrent['heightShadow'] = parseInt(attrs['y']);
					}
					else
					{
						iconCurrent['widthIcon'] = parseInt(attrs['x']);
						iconCurrent['heightIcon'] = parseInt(attrs['y']);
					}
				}
			}
			else if (name == "gmm:anchor")
			{
				if (iconCurrent)
				{
					if (bShadow)
					{
						iconCurrent['anchorShadowX'] = parseInt(attrs['x']);
						iconCurrent['anchorShadowY'] = parseInt(attrs['y']);
					}
					else
					{
						iconCurrent['anchorX'] = parseInt(attrs['x']);
						iconCurrent['anchorY'] = parseInt(attrs['y']);
					}
				}
			}
			else if (name == "gmm:offset")
			{
				if (iconCurrent && !bShadow)
				{
					iconCurrent['popupOffsetX'] = parseInt(attrs['x']);
					iconCurrent['popupOffsetY'] = parseInt(attrs['y']);
				}
			}
		},
		function closeTagHandler(name) {
			if (name == "gmm:icon")
				iconCurrent = null;
		});
	parser.parse(item);

	// Ajout du marqueur
	if (!map.existIcon(iconName))
		map.setIcon(iconName, icon);
	markerParams['icon'] = iconName;
	if (arrayCount(iconSel) > 0)
	{
		if (!map.existIcon(iconName+"_sel"))
			map.setIcon(iconName+"_sel", iconSel);
		markerParams['icon_sel'] = iconName+"_sel";
	}
	var id = map.getNewMarkerID();
	map.setMarker(id, markerParams);
}
// element : item XML KML
function gmap_addKMLMarkers(map, element)
{
	jQuery("Placemark", element).each(function(placeIndex, placeElement) {
		gmap_addKMLMarker(map, placeElement);
	});
}

// Prise en charge d'un flux XML pour la d�finition des marqueurs
function gmap_handleXMLMarkers(map, xmlDoc)
{
	// Ajout des balise kml (normalement une seule)
	jQuery("kml", xmlDoc).each(function(kmlIndex, kmlElement) {
		gmap_addKMLMarkers(map, kmlElement);
	});
	// On pourrait ajouter d'autres formats XML : GeoRSS...
}



/***
 * Gestion des fichier GeoJSON pour transmettre les donn�es
 */

// Ajout d'une Feature
function gmap_handleJSONMarker(map, feature)
{
	if (!feature || (feature.type != 'Feature'))
		return false;
	var geometry = feature.geometry;
	if (!geometry || (geometry.type != 'Point'))
		return false;
	var markerParams = {
			longitude: geometry.coordinates[0],
			latitude: geometry.coordinates[1]
		};
	var params = feature['properties'];
	if (params['name'])
		markerParams.title = params['name'];
	if (params['html'])
	{
		markerParams.click = "showInfoWindow";
		markerParams['html'] = params['html'];
	}
	if (params['type'])
		markerParams.type = params['type'];
	if (params['zoom'])
		markerParams.zoom = params['zoom'];
	if (params['objet'])
		markerParams.objectName = params['objet'];
	if (params['id_objet'])
		markerParams.objectId = params['id_objet'];
	if (params['visible'])
		markerParams.visible = params['visible'];
	if (params['priorite'])
		markerParams.priority = params['priorite'];
	if (params['icon'])
	{
		var icon = params['icon'];
		var images = icon.images;
		if (images)
		{
			var iconDef = new Array;
			var iconSelDef = new Array;
			for (var indImage = 0; indImage < images.length; indImage++)
			{
				var image = images[indImage];
				if (image.state === "selected")
					iconCurrent = iconSelDef;
				else
					iconCurrent = iconDef;
				if (image.type == "shadow")
				{
					iconCurrent['urlShadowFile'] = image['url'];
					iconCurrent['widthShadow'] = image['cxSize'];
					iconCurrent['heightShadow'] = image['cySize'];
					iconCurrent['anchorShadowX'] = image['xAnchor'];
					iconCurrent['anchorShadowY'] = image['yAnchor'];
				}
				else if (image.type == "complete")
				{
					iconCurrent['urlCompleteFile'] = image['url'];
					iconCurrent['widthComplete'] = image['cxSize'];
					iconCurrent['heightComplete'] = image['cySize'];
					iconCurrent['anchorCompleteX'] = image['xAnchor'];
					iconCurrent['anchorCompleteY'] = image['yAnchor'];
				}
				else
				{
					iconCurrent['urlIconFile'] = image['url'];
					iconCurrent['widthIcon'] = image['cxSize'];
					iconCurrent['heightIcon'] = image['cySize'];
					iconCurrent['anchorX'] = image['xAnchor'];
					iconCurrent['anchorY'] = image['yAnchor'];
				}
				if (image['xOffset'] && image['yOffset'])
				{
					iconCurrent['popupOffsetX'] = image['xOffset'];
					iconCurrent['popupOffsetY'] = image['yOffset'];
				}
			}
			if (arrayCount(iconDef))
			{
				if (!map.existIcon(icon.name))
					map.setIcon(icon.name, iconDef);
				if (arrayCount(iconSelDef) && !map.existIcon(icon.name+"_sel"))
					map.setIcon(icon.name+"_sel", iconSelDef);
			}
		}
		if (icon.name && map.existIcon(icon.name))
			markerParams.icon = icon.name;
		if (icon.name && map.existIcon(icon.name+"_sel"))
			markerParams.icon_sel = icon.name+"_sel";
	}
	var id = map.getNewMarkerID();
	map.setMarker(id, markerParams);
	return true;
}

// D�codage d'un contenu JSON obtenu par Ajax
function gmap_handleJSONMarkers(map, content)
{
	var features;
	if (typeof(content) == "string")
		features = eval('(' + content + ')');
	else
		features = content;
	switch (features.type)
	{
	case 'FeatureCollection':
		{
			features = features.features;
			for (var index = 0; index < features.length; index++)
				gmap_handleJSONMarker(map, features[index])
		}
		break;
	case 'Feature':
		gmap_handleJSONMarker(map, features);
		break;
	}
	return true;
}



/***
 * Gestion de la carte depuis les pages
 */

// R�cup�rer l'objet carte � parti de son id (optionnel, 1 par d�faut)
function gmap_getMap(mapId)
{
	if (!mapId)
		mapId = 1;
	return gMap("gmap_map"+mapId);
}



/***
 * Gestion des marqueurs
 */
 
// Changer le zoom pour voir tous les marqueurs
function gmap_setViewportOnMarkers(mapId)
{
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
		return;

	// Parcourir les marqueurs
	var minLatitude = null;
	var minLongitude = null;
	var maxLatitude = null;
	var maxLongitude = null;
	var countMarkers = 0;
	for (var id in map.markers)
	{
		var params = map.getMarkerDefinition(id);
		if (!isObject(params))
			continue;
		if (!isObject(params['visible']) || (params['visible'] == 'oui'))
		{
			// Formater le rectangle englobant
			countMarkers++;
			if ((minLatitude == null) || (params['latitude'] < minLatitude)) minLatitude = params['latitude'];
			if ((maxLatitude == null) || (params['latitude'] > maxLatitude)) maxLatitude = params['latitude'];
			if ((minLongitude == null) || (params['longitude'] < minLongitude)) minLongitude = params['longitude'];
			if ((maxLongitude == null) || (params['longitude'] > maxLongitude)) maxLongitude = params['longitude'];
		}
	}

	// Si pas de marqueurs, ne rien faire
	if (countMarkers == 0)
		return;
	
	// Centrer la carte
	if (countMarkers == 1)
	{
		map.panTo(minLatitude, minLongitude);
		var vp = map.getViewport();
		if (vp['zoom'] < 4)
			map.setZoom(4);
	}
	else
		map.panToBounds(minLatitude, minLongitude, maxLatitude, maxLongitude);
}

// Affichage des marqueurs du manager
function gmap_showMarkers(bShow, mapId)
{
	var map = gmap_getMap(mapId);
	if (isObject(map))
	{
		map.showMarkers(bShow);
		if (bShow)
			gmap_setViewportOnMarkers(mapId);
	}
}

// Mettre le marqueur en �vidence
var _bouncedMarkers = new Array();
function gmap_unbounceAll(mapId)
{
	if (_bouncedMarkers.length == 0)
	    return;
		
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
		
	// Parcourir la liste pour d�selectionner les marqueurs
	var id;
	while (id = _bouncedMarkers.shift())
	{
		var params = map.getMarkerDefinition(id);
		if (!isObject(params))
			continue;
		if (map.existIcon(params['icon_normal']))
			map.setMarker(id, { icon: params['icon_normal'], zorder: 0 });
		else
			map.setMarker(id, { zorder: 0 });
	}
}
function gmap_bounceMarker(oneId, mapId)
{
    // Tout cacher
	gmap_unbounceAll();
	
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	var markerParams = map.getMarkerDefinition(oneId);
	if (!isObject(markerParams))
	    return;

	// D�placer la carte jusqu'� ce marqueur
	var timer = setTimeout(function()
	{
		map.panTo(markerParams['latitude'], markerParams['longitude']);
		timer = null;
	}, 100);

	// Changer l'image
	if (isObject(markerParams['icon_sel']) && map.existIcon(markerParams['icon_sel']))
		map.setMarker(oneId, { icon: markerParams['icon_sel'], icon_normal: markerParams['icon'], zorder: 100 });
	else
		map.setMarker(oneId, { zorder: 100 });
	_bouncedMarkers.push(oneId);
}
function gmap_bounceMarkers(objectName, objectId, mapId)
{
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
		
    // Tout cacher
	gmap_unbounceAll();
	
	// Parcourir les marqueurs pour extraire un rectangle et changer l'icone
	var minLatitude = null;
	var minLongitude = null;
	var maxLatitude = null;
	var maxLongitude = null;
	var countMarkers = 0;
	for (var id in map.markers)
	{
		var params = map.getMarkerDefinition(id);
		if (!isObject(params))
			continue;
		if (isObject(params['objectName']) && (params['objectName'] == objectName) &&
			isObject(params['objectId']) && (params['objectId'] == objectId) &&
			(!isObject(params['visible']) || (params['visible'] == 'oui')))
		{
			// Formater le rectangle englobant
			countMarkers++;
			if ((minLatitude == null) || (params['latitude'] < minLatitude)) minLatitude = params['latitude'];
			if ((maxLatitude == null) || (params['latitude'] > maxLatitude)) maxLatitude = params['latitude'];
			if ((minLongitude == null) || (params['longitude'] < minLongitude)) minLongitude = params['longitude'];
			if ((maxLongitude == null) || (params['longitude'] > maxLongitude)) maxLongitude = params['longitude'];
		
			// Changer l'icone
			if (isObject(params['icon_sel']) && map.existIcon(params['icon_sel']))
				map.setMarker(id, { icon: params['icon_sel'], icon_normal: params['icon'], zorder: 100 });
			else
				map.setMarker(id, { zorder: 100 });
			_bouncedMarkers.push(id);
		}
	}
	
	// Si pas de marqueurs, ne rien faire
	if (countMarkers == 0)
		return;
	
	// Centrer la carte
	if (countMarkers == 1)
		map.panTo(minLatitude, minLongitude);
	else
		map.panToBounds(minLatitude, minLongitude, maxLatitude, maxLongitude);
}

// Se d�placer vers un marqueur, le s�lectionner et afficher la bulle associ�e
function gmap_gotoMarkers(objectName, objectId, mapId)
{
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	
	// Fermer le bulles pr�c�dentes
    map.closeInfoWindow();

	// Parcourir les marqueurs pour extraire un rectangle et un facteur de zoom
	var minLatitude = null;
	var minLongitude = null;
	var maxLatitude = null;
	var maxLongitude = null;
	var countMarkers = 0;
	var zoom = 0;
	var onePriority = 99;
	var oneId = null;
	for (var id in map.markers)
	{
		var params = map.getMarkerDefinition(id);
		if (!isObject(params))
			continue;
		if (isObject(params['objectName']) && (params['objectName'] == objectName) &&
			isObject(params['objectId']) && (params['objectId'] == objectId) &&
			(!isObject(params['visible']) || (params['visible'] == 'oui')))
		{
			countMarkers++;
			zoom = Number(params['zoom']);
			if ((minLatitude == null) || (params['latitude'] < minLatitude)) minLatitude = params['latitude'];
			if ((maxLatitude == null) || (params['latitude'] > maxLatitude)) maxLatitude = params['latitude'];
			if ((minLongitude == null) || (params['longitude'] < minLongitude)) minLongitude = params['longitude'];
			if ((maxLongitude == null) || (params['longitude'] > maxLongitude)) maxLongitude = params['longitude'];
			if ((oneId == null) || (params['priority'] < onePriority))
			{
				oneId = id;
				oneType = params['type'];
			}
		}
	}
	
	// Si pas de marqueurs, ne rien faire
	if (countMarkers == 0)
		return;
	
	// Centrer la carte
	if (countMarkers == 1)
		map.setViewport(minLatitude, minLongitude, zoom);
	else
		map.setViewportBounds(minLatitude, minLongitude, maxLatitude, maxLongitude);
	
	// Ouvrir la bulle
	if (oneId)
	{
		// Si on le fait directement, elle ne s'affiche pas quand le zoom
		// change, donc on le d�porte sur un timer, ce qui, de plus, donne
		// un joli effet
		var timer = setTimeout(function()
		{
			gmap_bounceMarker(mapId, oneId);
		    map.showInfoWindow(oneId);
			timer = null;
		}, 200);
	}
}



/***
 * Gestion des fichiers KML externes
 */
 
// Affichage d'une couche KML
function gmap_showKMLLayer(id, bShow, mapId)
{
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	if (!map.existLayer(id))
		return;

	// Montrer ou cacher la couche
	map.showLayer(id, bShow);
	if (bShow)
		map.gotoLayerViewport(id);
}

// D�placement de la carte pour montrer un KML
function gmap_setViewportOnKMLLayer(id, mapId)
{
    // R�cup�rer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	if (!map.existLayer(id))
		return;
	
	// Afficher le KML
	map.gotoLayerViewport(id);
}

// Affichage de la couche Wikipedia
function gmap_showWikipediaLayer(show, mapId)
{
    // Initialiser la static qui contient l'id
	if (typeof gmap_showWikipediaLayer.layer == 'undefined' )
		gmap_showWikipediaLayer.layer = new Array();
	
    // R�cup�rer la carte
	if (!mapId)
		mapId = 1;
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;

	// Cr�er la couche si elle n'existe pas
	if (!isObject(gmap_showWikipediaLayer.layer[mapId]))
	{
		gmap_showWikipediaLayer.layer[mapId] = map.getNewLayerID();
		map.addLayerAuto(gmap_showWikipediaLayer.layer[mapId], 'org.wikipedia.fr', show);
	}
		
	// Sinon, montrer ou cacher la couche
	else
		map.showLayer(gmap_showWikipediaLayer.layer[mapId], show);
}


/*
 * Gestion de la fusion des info-bulles
 */

// Formatage du HTML
function miw_formatNavigatorPart(index, name)
{
	return '<option id="miw'+index+'-link" value="'+index+'">'+name+'</option>';
}
function miw_formatContentPart(index, html)
{
	return '<div id="miw'+index+'-content"'+((index == 1) ? ' style="display:block;"' : ' style="display:none;"')+'>' + html + '</div>';
}
function miw_wrapHtml(count, current, content)
{
	return '<div id="merged-info-windows" count="'+count+'" current="'+current+'">'+content+'</div>';
}
function miw_formatHtml(count, current, navigator, content)
{
	var htmlContents = '<table id="miw-navigator"><tr>'
		+ '<td id="miw-navigator-first" class="miw-arrow"></td>'
		+ '<td id="miw-navigator-prev" class="miw-arrow"></td>'
		+ '<td id="miw-navigator-zoom" class="miw-zoom"></td>'
		+ '<td class="miw-navigator-links"><select name="miw-navigator-choice" id="miw-navigator-choice" size="1">'+navigator+'</select></td>'
		+ '<td id="miw-navigator-next" class="miw-arrow miw-navigator-next"></td>'
		+ '<td id="miw-navigator-last" class="miw-arrow miw-navigator-last"></td>'
		+ '</tr></table>'
	htmlContents += '<div id="miw-content">' + content + '</div>';
	return miw_wrapHtml(count, current, htmlContents);
}

// Mettre � jour l'interface par rapport aux donn�es
jQuery.fn.updateMergedInfoWindows = function(onChangeHandler, bFireChange, onZoomHandler, current, count)
{
	// R�cup�rer le compteur s'il n'est pas l�
	if (!count)
		count = this.attr("count");
	if (!count)
		return;
		
	// V�rifier la validit� de la position
	if (!current || (current < 1))
		current = 1;
	if (current > count)
		current = count;

	var root = this;
		
	// Mettre � jour le contenu
	for (var index = 0; index <= count; index++)
	{
		if (index == current)
			jQuery('#miw'+index+'-content', this).show();
		else
			jQuery('#miw'+index+'-content', this).hide();
	}
	
	// Mettre � jour les acc�s directs
	var select = jQuery('#miw-navigator-choice', this);
	for (var index = 0; index <= count; index++)
	{
		if (index == current)
			jQuery('#miw'+index+'-link', select).attr('selected', 'selected');
		else
			jQuery('#miw'+index+'-link', select).removeAttr('selected');
	}
	select.change(function(event) {
			var option = jQuery("option:selected", select);
			if (option && (option.length == 1))
			{
				var index =  parseInt(option.attr("value"));
				root.updateMergedInfoWindows(onChangeHandler, true, onZoomHandler, index, count);
			}
		});
	
	// Mettre � jour les fl�ches
	var first = jQuery('#miw-navigator-first', this);
	var prev = jQuery('#miw-navigator-prev', this);
	var next = jQuery('#miw-navigator-next', this);
	var last = jQuery('#miw-navigator-last', this);
	first.removeClass('miw-navigator-first').unbind('click');
	prev.removeClass('miw-navigator-prev').unbind('click');
	last.removeClass('miw-navigator-last').unbind('click');
	next.removeClass('miw-navigator-next').unbind('click');
	if (current > 1)
	{
		first.addClass('miw-navigator-first').click(function(event) {
			root.updateMergedInfoWindows(onChangeHandler, true, onZoomHandler, 1, count);
		});
		prev.addClass('miw-navigator-prev').click(function(event) {
			root.updateMergedInfoWindows(onChangeHandler, true, onZoomHandler, current-1, count);
		});
	}
	if (current < count)
	{
		last.addClass('miw-navigator-last').click(function(event) {
			root.updateMergedInfoWindows(onChangeHandler, true, onZoomHandler, count, count);
		});
		next.addClass('miw-navigator-next').click(function(event) {
			root.updateMergedInfoWindows(onChangeHandler, true, onZoomHandler, current+1, count);
		});
	}
	
	// Mettre � jour le zoom
	var zoom = jQuery('#miw-navigator-zoom', this);
	zoom.unbind('click');
	zoom.click(function(event) {
		onZoomHandler();
	});
	
	// Stocker la position
	this.attr("count", count);
	this.attr("current", current);
	
	// Notifier le changement
	if (onChangeHandler && bFireChange)
		onChangeHandler(current, miw_wrapHtml(count, current, this.html()));
}

// Fonctions d'acc�s simplifi�es
function miw_init(onChangeHandler, onZoomHandler, current)
{
	var toBeInited = jQuery("#merged-info-windows");
	if (isObject(toBeInited))
		toBeInited.updateMergedInfoWindows(onChangeHandler, false, onZoomHandler, current);
}