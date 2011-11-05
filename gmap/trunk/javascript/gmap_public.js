/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Scripts additionnels, indépendants de l'implémentation utilisés dans la partie publique
 *
 */
 

 
/***
 * Évènements de chargement de la carte
 */
 
// Utilitaire pour envoyer le gmapReady
jQuery.fn.triggerGmapReady = function(mapId)
{
	this.attr("mapId", mapId);
	this.triggerHandler("gmapReady");
};

// Raccourci pour ajouter des évènements
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
 * Objet dédié à l'analyse XML
 */

// Définition de la classe XMLParser
function XMLParser(navigationHandler, openTagHandler, closeTagHandler)
{
	this.xmlDoc = null;
	this.navigationHandler = navigationHandler;
	this.openTagHandler = openTagHandler;
	this.closeTagHandler = closeTagHandler;
};

// Valeurs par défaut des commande de navigation
XMLParser.defaultNavCommands = {
	bUseAttributes: 0,
	bUseChilds: 0,
	bUseContents: 0,
	arAttrsFilter: []
};

// Méthodes de l'objet
XMLParser.prototype = {

	// Récupérer ou créer une carte
	parse: function(xmlDoc)
	{
		this.xmlDoc = xmlDoc;
		this.doParse(this.xmlDoc);
	},

	// Fonction principale du parser
	doParse: function(domElement)
	{
		// Tester l'information nécessaire sur l'élément
		var name = domElement.nodeName;
		var navCommands = XMLParser.defaultNavCommands;
		if (this.navigationHandler)
			navCommands = this.navigationHandler(name);
		
		// Récupérer les attributs
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
		
		// Récupérer le contenu
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
			
		// Si on a demandé les fils ou le contenu, parcourir le contenu
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
 * Gestion des fichiers de définition des marqueurs
 */
 
// Formatage du texte HTML des bulles
function _gmap_getHtmlContents(contents)
{
	if (typeof(contents) != "string")
		return contents;
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
					markerParams[name] = attrs[name]
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
	var id = map.getNewMarkerID();
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

// Prise en charge d'un flux XML pour la définition des marqueurs
function gmap_handleXMLMarkers(map, xmlDoc)
{
	// Ajout des balise kml (normalement une seule)
	jQuery("kml", xmlDoc).each(function(kmlIndex, kmlElement) {
		gmap_addKMLMarkers(map, kmlElement);
	});
	// On pourrait ajouter d'autres formats : GeoRSS...
}



/***
 * Gestion de la carte depuis les pages
 */

// Récupérer l'objet carte à parti de son id (optionnel, 1 par défaut)
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
    // Récupérer la carte
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
		map.panTo(minLatitude, minLongitude);
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

// Mettre le marqueur en évidence
var _bouncedMarkers = new Array();
function gmap_unbounceAll(mapId)
{
	if (_bouncedMarkers.length == 0)
	    return;
		
    // Récupérer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
		
	// Parcourir la liste pour déselectionner les marqueurs
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
	
    // Récupérer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	var markerParams = map.getMarkerDefinition(oneId);
	if (!isObject(markerParams))
	    return;

	// Déplacer la carte jusqu'à ce marqueur
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
    // Récupérer la carte
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

// Se déplacer vers un marqueur, le sélectionner et afficher la bulle associée
function gmap_gotoMarkers(objectName, objectId, mapId)
{
    // Récupérer la carte
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;
	
	// Fermer le bulles précédentes
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
		// change, donc on le déporte sur un timer, ce qui, de plus, donne
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
    // Récupérer la carte
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

// Déplacement de la carte pour montrer un KML
function gmap_setViewportOnKMLLayer(id, mapId)
{
    // Récupérer la carte
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
	
    // Récupérer la carte
	if (!mapId)
		mapId = 1;
	var map = gmap_getMap(mapId);
	if (!isObject(map))
	    return;

	// Créer la couche si elle n'existe pas
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

// Mettre à jour l'interface par rapport aux données
jQuery.fn.updateMergedInfoWindows = function(onChangeHandler, bFireChange, onZoomHandler, current, count)
{
	// Récupérer le compteur s'il n'est pas là
	if (!count)
		count = this.attr("count");
	if (!count)
		return;
		
	// Vérifier la validité de la position
	if (!current || (current < 1))
		current = 1;
	if (current > count)
		current = count;

	var root = this;
		
	// Mettre à jour le contenu
	for (var index = 0; index <= count; index++)
	{
		if (index == current)
			jQuery('#miw'+index+'-content', this).show();
		else
			jQuery('#miw'+index+'-content', this).hide();
	}
	
	// Mettre à jour les accès directs
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
	
	// Mettre à jour les flèches
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
	
	// Mettre à jour le zoom
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

// Fonctions d'accès simplifiées
function miw_init(onChangeHandler, onZoomHandler, current)
{
	var toBeInited = jQuery("#merged-info-windows");
	if (isObject(toBeInited))
		toBeInited.updateMergedInfoWindows(onChangeHandler, false, onZoomHandler, current);
}