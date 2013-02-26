/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Insertion d'un formulaire (carte dans une iframe)
*
**/


/** Gestion de l'initialisation des Maps */
jQuery.geoportail =
{	// La liste des cartes
	cartes: new Array(),
	// Liste des fonctions d'initialisation (chargees par onload)
	cinit: new Array(),
	// Liste de profils
	profils: new Array(),
	// hash code
	hash: null,

	getParam: function(param) {
		var p = document.location.href.split("&" + param + "=");
		if (p[1]) p = p[1].split("&")[0];
		else return null;
		return p;
	},

	// Definition de l'originator (logo) pour l'affichage des couches SPIP 
	// ex : jQuery.geoportail.setOriginator ('[(#LOGO_SITE_SPIP||extraire_attribut{src})]', '#URL_SITE_SPIP');
	setOriginator: function(logo, url) {
		jQuery.geoportail.originators = Array({ logo: 'spip', pictureUrl: logo, url: url });
	},

	// Fonction d'initialisation des cartes
	initMap: function(dirPlug) 
	{	//alert ("initMap '"+typeof(Geoportal)+"'");
		if (typeof(OpenLayers)=='undefined' ||
			typeof (Geoportal) == 'undefined' ||
			typeof (Geoportal.Viewer) == 'undefined' ||
			typeof (Geoportal.Viewer.Standard) == 'undefined' ||
			(jQuery.browser["msie"] && typeof (document.namespaces) != 'object'))        
		{
            window.setTimeout('jQuery.geoportail.initMap();', 300);
            return;
        }
        // ready !

		// Chargement des classes Geoportail 
		if (!OpenLayers.Layer.Vector.Locator) GeoportailInitLayerLocator();			// Geoportal.Util.loadJS(dirPlug + "js/Layer/Locator.js");
		if (!OpenLayers.Layer.GXT) GeoportailInitLayerGXT();						// Geoportal.Util.loadJS(dirPlug + "js/Layer/GXT.js");
		if (!Geoportal.Format.Geoconcept.rip) GeoportailInitFormatCeoconcept();		// Geoportal.Util.loadJS(dirPlug + "js/Format/Ceoconcept_rip.js");
		if (!OpenLayers.Popup.SpipPopup) GeoportailInitSpipPopup();					

		//
		var i;
		for (i = 0; i < this.cartes.length; i++)
		{	var carte = this.cartes[i];
			
			var fview;
			var proj;
			// Type du viewer
			switch (carte.type)
			{	case 'Standard' : 
					fview = Geoportal.Viewer[carte.type];
					break;
				default : 
					fview = Geoportal.Viewer.Default;
					break;
			}
			// Mode d'affichage
			switch (carte.mode)
			{	case 'OSM','YHOO','GMAP','BING' : 
					proj = 'EPSG:3857';
					break;
				default : 
					proj=null;
					break;
			}
			// Declarer une nouvelle carte
			carte.map = new fview
			(	"GeoportalMapDiv"+carte.id,
				OpenLayers.Util.extend({
					mode:'normal',
                    territory: carte.zone,
                    projection: proj,
					nameInstance: "map"+carte.id
					},
					gGEOPORTALRIGHTSMANAGEMENT
				)
			);
			carte.map.type = carte.type;
		}
		//Attente du chargement des cartes...
		this.initMap2();
	},
		
	initMap2: function() 
	{	var i;
		this.count++;
		// Verifier que les cartes sont pretes
		for (i = 0; i < this.cartes.length; i++)
		{	if (this.cartes[i].map.ready.h<0) 
			{	window.setTimeout('jQuery.geoportail.initMap2();', 100);
				return;
			}
		}
		// OK !
		for (i = 0; i < this.cartes.length; i++)
		{	var carte = this.cartes[i];
			// Charger
			carte.geoportalLoadmap();

			// La carte geoportail
			var map = carte.map;
			var id = carte.id;
			// Ajouter une fonction d'export
			map.downloadData = geoportail_loadData;
			// Ajouter une fonction de selection de layer
			map.selectionnable = geoportail_selectionnable;
			// Recherche du placement dans l'adresse
			var lon = Number(this.getParam("lon"));
			var lat = Number(this.getParam("lat"));
			var zoom = Number(this.getParam("zoom"));
			if (lon && lat) {
				map.getMap().setCenterAtLonLat(lon, lat, zoom);
				carte.fixe = true;
			}
			else if (zoom) {
				map.getMap().zoomTo(zoom);
				carte.fixe = true;
			}
			// Afficher les documents associes
			var k;
			for (k = 0; k < carte.doc.length; k++)
				this.addLayer(carte, carte.doc[k]['extension'], carte.doc[k]['id_document'], carte.doc[k]['titre'], carte.doc[k]['fichier'], carte.doc[k]['nozoom'], { spipStyle:carte.doc[k]['options'] } );
			// Afficher des images
			if (carte.img.length) 
			{	// Style de la couche
				var symbolizer = OpenLayers.Util.applyDefaults(
						{ pointRadius: 10, graphicXOffset: -2, graphicYOffset: -17, cursor:'pointer' },
						OpenLayers.Feature.Vector.style["default"]);
				var styleMap = new OpenLayers.StyleMap({"default": symbolizer, "select": {pointRadius: 15, graphicXOffset: -4, graphicYOffset: -26}});
				// Rajoute une couche pour les points
				var t = carte.img[0].titre;
				var l = new OpenLayers.Layer.Vector(t ? t : "IMG", { styleMap: styleMap, opacity: 1, visibility: 1, originators: jQuery.geoportail.originators, view:{ zoomToExtent:1 } });
				map.spip_img = l;
				map.getMap().addLayer(l);
				// couche selectionnable
				map.selectionnable (l);
				// Ajouter les documents dans la couche
				for (k = 0; k < carte.img.length; k++) 
				{	var img = carte.img[k];
					var feature = jQuery.geoportail.createFeature(map, Number(img.lon), Number(img.lat), img.logo, img.taille, null, img.align);
					feature.attributes = img.attributes;
					l.addFeatures(feature);
				}
			}
			// Affichage des profils
			for (var p = 0; p < this.profils.length; p++) 
			{	var pf = new jQuery.geoportail.elevation(this.profils[p].div, this.profils[p].options);
				pf.loadGPX(this.profils[p].fichier);
			}
			// Fonctions d'initialisation
			for (var f = 0; f < this.cinit.length; f++) {
				if (id == this.cinit[f].id_map) this.cinit[f].init(map, id);
			}
			// Gestion d'un formulaire
			if (carte.formulaire && typeof (parent.initSpipMapFormulaire) == 'function') parent.initSpipMapFormulaire(map, id, OpenLayers);
			// Permettre a l'utilisateur d'initialiser sa carte
			else {
				var finit = 'initSpipMap' + id;
				try { finit = eval(finit); } catch (error) { }
				if (typeof (finit) == 'function') finit(map, id);
				else if (typeof (initSpipMap) == 'function') initSpipMap(map, id);
				else if (typeof (parent.initSpipMap) == 'function') parent.initSpipMap(map, id, OpenLayers);
			}
			// Masquer la patience
			jQuery("#GeoportalMapDiv" + i).css("background-image", "none");
		}
	},
	
	/** Ajouter une fonction d'initialisation
	permet d'empiler plusieurs fonctions a executer lorsque la carte est chargee
	*/
	onLoad: function(id_map, f) {
		if (typeof (f) == 'function') this.cinit[this.cinit.length] = { 'id_map': id_map, 'init': f };
	},

	/** Ajouter une carte de situation
	la fonction renvoie le control correspondant dans map.overview
	la carte s'affiche dans la div id=overviewMap ou a la suite de la div id=navigation
	*/
	setOverview: function(map, photo, zoom, divID) {	// Div de la vue globale
		var ovDiv;
		var w = h = 162;
		if (!divID) divID = "overviewMap";
		ovDiv = jQuery("#" + divID);
		if (ovDiv && ovDiv.length > 0) {
			w = ovDiv.width();
			if (!w) w = 160;
			h = ovDiv.height();
			if (!h) h = w;
			ovDiv = ovDiv[0];
		}
		// En creer une
		else {
			var div = OpenLayers.Util.getElement('navigation');
			// SPIP v3
			if (!div) div = jQuery('.aside')[0];
			if (!div) return;
			ovDiv = document.createElement('div');
			ovDiv.id = OpenLayers.Util.createUniqueID('overviewMap');
			ovDiv.className = 'overviewSpipMap';
			ovDiv.style.width = w + "px";
			ovDiv.style.margin = "auto";
			div.appendChild(ovDiv);
		}
		// photo ou carte ?
		var fond = photo ? 'ORTHOIMAGERY.ORTHOPHOTOS' : 'GEOGRAPHICALGRIDSYSTEMS.MAPS';
		// Recuperer la couche
		var worldMapPrms = map.getMap().catalogue.getLayerParameters('WLD', fond);
		worldMapPrms.options.territory = 'WLD';
		worldMapPrms.options.isBaseLayer = true;
		worldMapPrms.options.opacity = 1.0;
		worldMapPrms.options.afterAdd = function() {// add GeoRM :
			var k = map.getMap().catalogue.getLayerGeoRMKey(this.territory, this.name);
			if (k != null) {
				this.GeoRM = Geoportal.GeoRMHandler.addKey(
                    k,
                    map.getMap().catalogue[k].tokenServer.url,
                    map.getMap().catalogue[k].tokenServer.ttl,
                    this.map); //the overview map !
			}
		};
		// Nouvelle couche
		var worldMap = new worldMapPrms.classLayer(
            worldMapPrms.options.name,
            worldMapPrms.url,
            worldMapPrms.params,
            worldMapPrms.options);
		var options = { resolutions: worldMapPrms.options.nativeResolutions.slice(0, 5),  // get resolutions from worldMap
			numZoomLevels: 5,
			minZoomLevel: worldMapPrms.options.minZoomLevel,
			maxZoomLevel: worldMapPrms.options.maxZoomLevel,
			projection: worldMapPrms.options.projection.clone(),
			maxExtent: worldMapPrms.options.maxExtent,
			theme: null // prevent OL to insert style.css !
		};
		// 1 seul niveau de zoom (moyen)
		if (!zoom) {
			options.resolutions = worldMapPrms.options.nativeResolutions.slice(2, 5)
			options.numZoomLevels = options.minZoomLevel = options.maxZoomLevel = 1;
		}
		// Creer la carte
		var ovmap = new OpenLayers.Control.OverviewMap(
			{ div: ovDiv,
				layers: [worldMap],
				mapOptions: options,
				autoPan: false,
				size: new OpenLayers.Size(w - 2, h - 2)
			});
		// Ne pas deplacer la carte intepestivement
		if (!zoom) {
			ovmap.isSuitableOverview = function() {
				var mapExtent = this.map.getExtent();
				var maxExtent = this.map.maxExtent;
				var testExtent = new OpenLayers.Bounds(
										Math.max(mapExtent.left, maxExtent.left),
										Math.max(mapExtent.bottom, maxExtent.bottom),
										Math.min(mapExtent.right, maxExtent.right),
										Math.min(mapExtent.top, maxExtent.top));

				if (this.ovmap.getProjection() != this.map.getProjection()) {
					testExtent = testExtent.transform(
						this.map.getProjectionObject(),
						this.ovmap.getProjectionObject());
				}

				return ((this.ovmap.getExtent().containsBounds(testExtent)));
			};
		}
		// Ajouter
		map.getMap().addControl(ovmap);
		map.overview = ovmap;
	},

	// Ajouter une nouvelle carte
	addMap: function(carte) {
		carte.doc = new Array();
		carte.img = new Array();
		this.cartes[this.cartes.length] = carte;
	},

	// Recupere la carte 
	getCarte: function(id_map) {
		var i;
		for (i = 0; i < this.cartes.length; i++) {
			if (this.cartes[i].id == id_map)
				return this.cartes[i];
		}
		return null;
	},

	// Affichage du lien direct (codage de lon, lat et zoom dans l'adresse)
	lien: function(id_map) 
	{	var carte = this.getCarte(id_map);
		if (carte) 
		{	var map = carte.map.getMap();
			var pos = map.getCenter();
			var zoom = map.getZoom();
			var ortho=0, carto=0;
			for (i = 0; i < map.layers.length; i++) 
			{	var lyr = map.layers[i];
				if (!lyr.displayInLayerSwitcher || !lyr.visibility) continue;
				switch (lyr.name)
				{	case 'geoportal.catalogue.maps.theme.name':
					case "GEOGRAPHICALGRIDSYSTEMS.MAPS":
					case "OSM (MapQuest)":
					case "OSM (Tiles&#064;Home)":
					case "OSM (Mapnik)":
					{	carto = Number(lyr.opacity) ? Math.round(lyr.opacity * 10) / 10 : 1;
						break;
					}
					case "geoportal.catalogue.orthophotos.theme.name":
					case "ORTHOIMAGERY.ORTHOPHOTOS":
					{	ortho = Number(lyr.opacity) ? Math.round(lyr.opacity * 10) / 10 : 1;
						break;
					}
					case "Google Map":
					case "Yahoo Map":
					case "Bing Road":
					{	carto = 1;
						break;
					}
					case "Google Satellite":
					case "Yahoo Satelitte":
					case "Bing Aerial":
					{	ortho = 1;
						break;
					}
					case "Google Hybrid":
					case "Yahoo Hybrid":
					case "Bing Hybrid":
					{	ortho=1;
						carto=1;
						break;
					}
					break;
				}
			}
			var a = pos.transform(map.getProjection(), new OpenLayers.Projection('IGNF:RGF93G'));
			// Simplifier un peu...
			a.lon = Math.round(a.lon * 100000000) / 100000000;
			a.lat = Math.round(a.lat * 100000000) / 100000000;
			// Supprimer les anciennes valeurs
			var href = document.location.href.replace(/&lon=[0-9,.,-]+/g, "");
			href = href.replace(/&lat=[0-9,.,-]+/g, "");
			href = href.replace(/&zoom=[0-9,.,-]+/g, "");
			href = href.replace(/&ortho=[0-9,.,-]+/g, "");
			href = href.replace(/&carto=[0-9,.,-]+/g, "");
			href += "&lon=" + a.lon + "&lat=" + a.lat + "&zoom=" + zoom + "&ortho=" + ortho + "&carto=" + carto;

			// Formatage de l'affichage
			var format = 'geoportail_lien_format' + id_map;
			try { format = eval(format); } catch (error) { }
			// Afficher
			if (typeof (format) == 'function' && jQuery.jqDialog) {
				jQuery.jqDialog("",
				{ dialog: format(href),
					ok: null
				});
			}
			else prompt('', href + "&lon=" + a.lon + "&lat=" + a.lat + "&zoom=" + zoom);
		}
	},

	// Ajouter un nouveau document a afficher
	addDoc: function(id_map, ext, id_document, titre, fic, nozoom, opts) 
	{	ext = ext.toUpperCase();
		// Verifier OK
		if (ext != 'GPX' && ext != 'KML' && ext != 'GXT') return;
		// &amp; => & (sinon on les code 2 fois)
		fic = fic.replace(/&amp;/g,"&");
		// Ajouter
		var carte = this.getCarte(id_map);
		if (carte) {
			carte.doc[carte.doc.length] = { extension: ext, titre: titre, id_document: id_document, fichier: fic, nozoom: nozoom, options:opts };
		}
	},

	// Ajouter une image
	addImg: function(id_map, id_document, titre, lon, lat, logo, align, taille, attributes) 
	{	var carte = this.getCarte(id_map);
		if (carte) {
			carte.img[carte.img.length] = { titre: titre, lon: lon, lat: lat, id_document: id_document, attributes: attributes, logo: logo, align: align, taille: Number(taille) };
		}
	},
	
	// Ajouter un profil
	addProfil: function (fichier, div, options)
	{	if (!options) options = {};
		this.profils.push ({ fichier:fichier, div:div, options:options });
	},

	/** Supprimer une couche de la carte
	*/
	removeGeoservice: function (map, type, layers)
	{	if (type != "GeoPortail") return;
		var tab, couches, i, j;
		tab = new Array();
		couches = layers.split(/,/g);
		var i,j;
		for (i=map.getMap().layers.length-1; i>=0; i--)
		{	for(j=0; j<couches.length; j++)
			{	if (map.getMap().layers[i].name == couches[j]) 
				{	// map.getMap().removeLayer(map.getMap().layers[i]);
					tab[tab.length] = map.getMap().layers[i];
					// map.getMap().layers[i].displayInLayerSwitcher = false;
					map.getMap().layers[i].setVisibility (false);
					break;
				}
			}
		}
		// Layer poubelle
		if (tab.length>0)
		{	var l = new Geoportal.Layer.Aggregate ("#", tab, 
			{	visibility:false, 
				displayInLayerSwitcher:false 
			});
			map.getMap().addLayer ( l );
		}
	},
	
	/** Ajouter un geoservice a la carte
	*/
	addGeoservice: function (map, type, titre, desc, service, options)
	{	// Originator (logo ign par defaut)
		var orig = { logo: (options.logo ? options.logo:'ign') };
		if (options.picture) 
		{	if (!options.logo) orig.logo = 'logo'+service.id_geoservice;
			orig.pictureUrl = options.picture;
			orig.url = options.link;
		}
		// Creer le layer
		switch (type)
		{	case "GeoPortail":
				{	var tab, couches, i, j;
					tab = new Array();
					couches = service.layers.split(/,/g);
					for (i=0; i<map.getMap().layers.length; i++)
					{	if (map.getMap().layers[i].territory == map.territory) 
						{	for(j=0; j<couches.length; j++)
							{	if (map.getMap().layers[i].name == couches[j]) 
								{	tab[tab.length] = map.getMap().layers[i];
									map.getMap().layers[i].setOpacity(options.opacity);
									map.getMap().layers[i].setVisibility(false);
								}
							}
						}
					}
					if (tab.length>0)
					{	var l = new Geoportal.Layer.Aggregate (titre, tab, 
						{	opacity:options.opacity, 
							visibility:options.visibility, 
							description: desc,
							metadataURL: options.link,
							displayInLayerSwitcher:true 
						});
						map.getMap().addLayer ( l );
					}
				}
				break;
			case "WMS":
				map.getMap().addLayer(
					type, titre, service.url,
					{	//map: (service.map ? service.map : null),
						layers:	service.layers,
						format: service.format,
						transparent:'true'
					},
					{	singleTile:true,
						projection: 'EPSG:4326',
						srs:{'EPSG:4326':'EPSG:4326', 'EPSG:3785':'EPSG:3785'},//some supported SRS from capabilities
						units:'degrees',
						// maxExtent expressed in EPSG:4326 :
						maxExtent: options.maxextent,
						minZoomLevel: options.minzoom,
						maxZoomLevel: options.maxzoom,
						opacity: options.opacity,
						isBaseLayer: false,
						description: desc,
						metadataURL: options.link,
						visibility: options.visibility,
						originators: [ orig ]
					}
				);
				break;
			case "WMS-C":
			case "WFS":
			case "KML":
			default: break;
		}
	},
	
	/** Ajouter un fichier GPX,KML,GXT
	Zoom sur l'extension du fichier
	*/
	addLayer: function(carte, type, id_document, name, url, nozoom, opts) 
	{	// Recherche des styles dans le css
		function setStyle(style, id, opts) 
		{	var stl = jQuery('#'+id);
			if (!stl.length) stl = jQuery("<div id='"+id+"'></div>").appendTo("body").hide();
			style.defaultStyle = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
			style.defaultStyle.cursor = 'pointer';
			style.defaultStyle.pointRadius = stl.width();
			style.defaultStyle.externalGraphic = stl.css("background-image").replace(/url\("(.+)"\)/i, '$1');
			style.defaultStyle.strokeWidth = stl.css('borderBottomWidth').replace('px', '');
			style.defaultStyle.strokeColor = stl.css('borderBottomColor');
			style.defaultStyle.strokeDashstyle = stl.css('borderBottomStyle').replace('ted', '').replace('ed', '');
			style.defaultStyle.opacity = 1;
			if (opts)
			{	if (opts.color) style.defaultStyle.strokeColor = opts.color;
				if (opts.style) style.defaultStyle.strokeDashstyle = opts.style;
				if (Number(opts.width)) style.defaultStyle.strokeWidth = Number(opts.width);
			}
		}

		var map = carte.map;
		// Ajouter la couche
		var l;
		if (!opts) opts = {};
		opts = jQuery.extend ({opacity:1, visibility:true, originators:jQuery.geoportail.originators, view:{ zoomToExtent:1 }}, opts);
		if (type == "GXT") 
		{	l = new OpenLayers.Layer.GXT(name, url, opts);
			map.getMap().addLayer(l);
		}
		else 
		{	opts.preFeatureInsert = Geoportal.Popup.setPointerCursorForFeature;
			l = map.getMap().addLayer(type, name, url, opts, { formatOptions :{extractStyles : true}, preventDefaultBehavior : true });
		}
		if (l) 
		{	// Recherche des styles
			setStyle(l.styleMap.styles['default'], 'geoportailDefaultStyle', opts.spipStyle);
			setStyle(l.styleMap.styles['select'], 'geoportailSelectStyle');
			
			// Permettre la selection du layer
			map.selectionnable (l);
		
			// Sauvegarder l'id du document
			l.id_document = id_document;

			// Faire quelque chose quand le calque est charge
			if (!nozoom && !carte.fixe) {	// Calcul et zoom sur l'extension (une fois charge)
				l.events.register("loadend", l, function(e) 
				{	if (this.features.length) 
					{	this.extent = new OpenLayers.Bounds();
						// this.extent.extend (map.getMap().center);
						for (var i = 0; i < this.features.length; i++) this.extent.extend(this.features[i].geometry.getBounds());
						map.getMap().zoomToExtent(this.extent);
					}
					// Fonction utilisateur onLoadSpipDoc#ID_DOCUMENT (layer)
					var fdoc = 'onLoadSpipDoc' + this.id_document;
					try { fdoc = eval(fdoc); } catch (error) { }
					if (typeof (fdoc) == 'function') fdoc(this.id_document, this);
					// Fonction utilisateur onLoadSpipDoc (id_document, layer)
					else if (typeof (onLoadSpipDoc) == 'function') 
					{	onLoadSpipDoc(this.id_document, this);
					}
				});
			}
			// Fonction utilisateur onLoadSpipDoc (id_document, layer)
			else if (typeof (onLoadSpipDoc) == 'function') {
				l.events.register("loadend", l, function(e) {
					onLoadSpipDoc(this.id_document, this);
				});
			}

			// Forcer pour IE !
			// l.events.triggerEvent("loadend");
			
			return l;
		}
	},

	// Creation d'une feature openlayer a mettre dans une carte
	createFeature: function(map, lon, lat, logo, size, ol, align) 
	{	if (!ol) ol = OpenLayers;
		if (!align) align = 'center'
		if (!size) size = 15;
		var pt = new ol.Geometry.Point(lon, lat);
		pt.transform(new ol.Projection('IGNF:RGF93G'), map.getMap().getProjection());
		// Ajouter le point
		var feature = new ol.Feature.Vector(new ol.Geometry.Point(pt.x, pt.y));
		feature.state = 'Insert';

		if (logo && logo != "") {
			feature.style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
			feature.style.externalGraphic = logo;
			feature.style.pointRadius = size;
			feature.style.graphicYOffset = -size;
			// Par defaut : centre
			feature.style.graphicXOffset = -size;
			feature.style.graphicYOffset = -size;
			feature.style.cursor = 'pointer';
			// Alignement
			align = align.split('-');
			for (i = 0; i < align.length; i++) switch (align[i]) {
				case 'left': feature.style.graphicXOffset = 0; break;
				case 'right': feature.style.graphicXOffset = -2 * size; break;
				case 'top': feature.style.graphicYOffset = 0; break;
				case 'bottom': feature.style.graphicYOffset = -2 * size; break;
				default: break;
			}
			feature.photo = true;
		}

		return feature;
	},
	
	// Modification du mode de la carte (layer de reference)
	setBaseLayer: function (map, l)
	{	// Creer un base layer
		var g = l.clone();
		l.isBaseLayer = false;
		map.getMap().addLayer(g);
		var c = map.getMap().resolution;
		// Changer de layer de reference + mettre le bon niveau de zoom (pour éviter un basculement auto hors echelle)
		var e = g.getZoomForResolution(c, true);
		map.getMap().setBaseLayer(g, map.getMap().getCenter(), e);
		map.getMap().restrictedMaxZoomLevel = g.maxZoomLevel;
		// Ne pas afficher...
		g.displayInLayerSwitcher= false;
		g.setVisibility(false);
		g.setOpacity(0);
		//l.setOpacity(1);
		//l.setVisibility(true);
	},

	// Ajouter un layer OSM
	addOSMLayer: function (map, titre, server, info, options)
	{	if (!info) info={};
		if (!options) options={};
		var l = new OpenLayers.Layer.OSM(
			titre,
			[	"http://a."+server+"/${z}/${x}/${y}.png",
				"http://b."+server+"/${z}/${x}/${y}.png",
				"http://c."+server+"/${z}/${x}/${y}.png"
			],
    		{	projection: new OpenLayers.Projection("EPSG:900913"),
				units: "m",
				numZoomLevels: (options.numZoom ? options.numZoom : 19),
				minZoomLevel: (options.minZoom ? options.minZoom : 0),
				//maxZoomLevel: (options.maxZoom ? options.maxZoom : null),
				maxResolution: 156543.0339,
				maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508),
				visibility: (typeof(options.visibility)=='undefined' ? false : options.visibility),
				opacity: ((!options.opacity) ? 1 : options.opacity),
				description: info.descriptif,
				metadataURL: info.url,
				isBaseLayer: true,
				originators: [{ logo: "osm", pictureUrl: "http://wiki.openstreetmap.org/Wiki.png", url: "http://wiki.openstreetmap.org/wiki/WikiProject_France"}]
			});
		map.getMap().addLayer(l);
		l.isBaseLayer = false;
		l.visibility = (typeof(options.visibility)=='undefined' ? false : options.visibility);
		l.maxZoomLevel = l.minZoomLevel + l.numZoomLevels;
		return l;
	},

	// Ajouter un layer MapQuest
	addMQSTLayer: function (map, titre, server, info, options)
	{	if (!info) info={};
		if (!options) options={};
		var l = new OpenLayers.Layer.OSM(
			titre,
			[	"http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png",
				"http://otile2.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png",
				"http://otile3.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png",
				"http://otile4.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png"
			],
    		{	projection: new OpenLayers.Projection("EPSG:900913"),
				units: "m",
				numZoomLevels: (options.numZoom ? options.numZoom : 19),
				minZoomLevel: (options.minZoom ? options.minZoom : 0),
				//maxZoomLevel: (options.maxZoom ? options.maxZoom : null),
				maxResolution: 156543.0339,
				maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508),
				visibility: (typeof(options.visibility)=='undefined' ? false : options.visibility),
				opacity: ((!options.opacity) ? 1 : options.opacity),
				description: info.descriptif,
				metadataURL: info.url,
				isBaseLayer: true,
				originators: [{ logo: "osm", pictureUrl: "http://wiki.openstreetmap.org/Wiki.png", url: "http://wiki.openstreetmap.org/wiki/WikiProject_France"},
					{ logo: "mquest", pictureUrl: "http://www.mapquestapi.com/cdn/common/images/large-logo.png", url: "http://www.mapquest.com/"}]
			});
		map.getMap().addLayer(l);
		l.isBaseLayer = false;
		l.visibility = (typeof(options.visibility)=='undefined' ? false : options.visibility);
		l.maxZoomLevel = l.minZoomLevel + l.numZoomLevels;
		return l;
	},
		
	// Ajouter un layer Google Map
	addGMAPLayer: function (map, titre, type, info, options)
	{	if (!info) info={};
		if (!options) options={};
		var l = new OpenLayers.Layer.Google(
			titre,
			{	type: google.maps.MapTypeId[type],
    			projection: new OpenLayers.Projection("EPSG:900913"),
				units: "m",
				numZoomLevels: (options.numZoom ? options.numZoom : 20),
				maxResolution: 156543.0339,
				maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508),
				visibility: (typeof(options.visibility)=='undefined' ? false : options.visibility),
				// opacity: (typeof(options.opacity)=='undefined' ? 1 : options.opacity),
				opacity:'none',
				description: info.descriptif,
				metadataURL: info.url,
				isBaseLayer: false,
				sphericalMercator: true
		});
		map.getMap().addLayer(l);
		return l;
	},
	
	// Ajouter un layer Yahoo! Map
	addYHOOLayer: function (map, titre, type, info, options)
	{	if (!info) info={};
		if (!options) options={};
		var l = new OpenLayers.Layer.Yahoo(
			titre,
			{	type: type,
    			projection: new OpenLayers.Projection("EPSG:900913"),
				units: "m",
				numZoomLevels: (options.numZoom ? options.numZoom : 18),
				//minZoomLevel: (options.minZoom ? options.minZoom : 1),
				//maxZoomLevel: (options.maxZoom ? options.maxZoom : 18),
				maxResolution: 156543.0339,
				maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508),
				visibility: (typeof(options.visibility)=='undefined' ? false : options.visibility),
				// opacity: (typeof(options.opacity)=='undefined' ? 1 : options.opacity),
				description: info.descriptif,
				metadataURL: info.url,
				isBaseLayer: false,
				sphericalMercator: true
		});
		map.getMap().addLayer(l);
		return l;
	},
	
	// Ajouter un layer Bing Map
	addBINGLayer: function (map, titre, type, info, options)
	{	if (!info) info={};
		if (!options) options={};
		var l ;
		l = new OpenLayers.Layer.Bing(
			{	name:titre,
				type: type,
				key: this.bingKey,
				isBaseLayer: false,
				visibility: (typeof(options.visibility)=='undefined' ? false : options.visibility),
				opacity: (typeof(options.opacity)=='undefined' ? 1 : options.opacity),
				numZoomLevels: (options.numZoom ? options.numZoom : 19),
				minZoomLevel: (options.minZoom ? options.minZoom : 0),
				description: info.descriptif,
				metadataURL: info.url,
				originators:[{
					logo:'bing',
					pictureUrl:'http://dev.virtualearth.net/Branding/logo_powered_by.png',
					url:'http://www.microsoft.com/maps/product/terms.html'
				}]

		});
//		l.isBaseLayer = false;
		map.getMap().addLayer(l);
		return l;
	},
	
	// Fonction principale pour l'affichage de la carte
	showMap: function(map, mode, pos, box, visu) 
	{	var lon = pos.lon;
		var lat = pos.lat;
		var ech = pos.zoom;
		var geocentre =
		{	"FXX": { lon:1.7, lat:46.95, ech:5 },
			"GLP": { lon:-61.42, lat:16.17, ech:8 },
			"GUF": { lon:-53, lat:4.8, ech:7 },
			"MTQ": { lon:-61, lat:14.65, ech:9 },
			"MYT": { lon:45.13, lat:-12.82, ech:9 },
			"NCL": { lon:165.9, lat:-21.2, ech:6 },
			"PYF": { lon:-149.5, lat:-17.66, ech:8 },
			"REU": { lon:55.5, lat:-21.1, ech:8 },
			"SPM": { lon:-56.3,	 lat:46.94,	 ech:9 },
			"WLF": { lon:-176.2, lat:-13.285,ech:11 },
			
			"ATF": { lon:140, lat:-66.7, ech:8 },
			"CRZ": { lon:Geoportal.Catalogue.TERRITORIES[pos.zone].geocenter[0], 
						lat:Geoportal.Catalogue.TERRITORIES[pos.zone].geocenter[1], 
						ech:9 },
			"KER": { lon:69.7, lat:-49.25,  ech:8 },
			"SMA": { lon:-63.08, lat:18.07,  ech:11 },
			"SBA": { lon:-62.85, lat:17.91, ech:11 },
			"ANF": { lon:-64.34, lat:15.78, ech:6 },
			"EUE": { lon:9.4, lat:48.94, ech:2 }
		};
		if (!lon) lon = geocentre[pos.zone].lon;
		if (!lat) lat = geocentre[pos.zone].lat;
		if (!ech) ech = geocentre[pos.zone].ech;

		// Rechercher les couches a afficher
		if (map.getMap().allowedGeoportalLayers) 
		{	// Forcer l'affichage ortho/carto
			var ortho = (this.getParam("ortho")) ? Number(this.getParam("ortho")) : (visu.ortho=='' ? 1 : Number(visu.ortho));
			var carto = (this.getParam("carto")) ? Number(this.getParam("carto")) : (visu.carto=='' ? (mode=='OSM' ? 1:0.3) : Number(visu.carto));
			var mixte = (ortho>0 && carto>0);

			switch (mode)
			{	// OpenStreetMap
				case 'OSM':
					var l;
					if (this.osm_mquest)
					l = this.addMQSTLayer ( map, "OSM (MapQuest)", 
						"",
						{	url: "http://developer.mapquest.com/web/products/open/map",
							descriptif: "<p style='margin:0 1em'>MapQuest OSM Open Tile.<br/>Tiles Courtesy of <a href='http://www.mapquest.com/' target='_blank'>MapQuest</a> <img src='http://developer.mapquest.com/content/osm/mq_logo.png'><br/>Map data (c) OpenStreetMap (and) contributors, CC-BY-SA</p>"
						},
						{	visibility : (this.osm_layer=='mquest' && carto>0),
							opacity: carto
						}
					);
					
					if (this.osm_tah)
					l = this.addOSMLayer ( map, "OSM (Tiles&#064;Home)", 
						"tah.openstreetmap.org/Tiles/tile",
						{	url: "http://tah.openstreetmap.org",
							descriptif: "<p style='margin:0 1em'>The Tiles@home server is the central hub of the Tiles@home distributed rendering system. Clients running all over the world talk to this server to get new rendering jobs, and upload their rendered results.<br/>Map data (c) OpenStreetMap (and) contributors, CC-BY-SA</p>"
						},
						{	numZoom:18,
							visibility : (this.osm_layer=='tah' && carto>0),
							opacity: carto
						}
					);
					
					l = this.addOSMLayer ( map, "OSM (Mapnik)", 
						"tile.openstreetmap.org",
						{	url: "http://wiki.openstreetmap.org/wiki/Mapnik",
							descriptif: "<p style='margin:0 1em'>Mapnik is the software we use to render the main Slippy Map layer for OSM, along with other layers such as the 'cycle map' layer and 'noname' layer. It is also the name given to the main layer, so things get a bit confusing sometimes.<br/>Map data (c) OpenStreetMap (and) contributors, CC-BY-SA</p>"
						},
						{	visibility : (this.osm_layer=='mapnik' && carto>0),
							opacity: carto
						}
					);
					
					/*
					l = this.addOSMLayer ( map, "OSM CycleMap", 
						"tile.opencyclemap.org/cycle",
						{	url: "http://wiki.openstreetmap.org/wiki/Cyclemap",
							descriptif: "<p style='margin:0 1em'><b>Just looking for bicycle maps and bike maps?</b><br/>An international cycling map created from OSM data is available, provided by Andy Allan. The map rendering is still being improved, the data is updated each week. It shows National Cycle Network cycle routes, other regional and local routes, and other cycling specific features.</p>"
						}
					);
					map.getMap().addLayer(l);
					*/
					jQuery.geoportail.setBaseLayer(map,l);
				break;
				// Google Map
				case 'GMAP':
					var l;
					l = this.addGMAPLayer ( map, "Google Satellite", 'SATELLITE', {}, { visibility : (!mixte && ortho>0) });
					l = this.addGMAPLayer ( map, "Google Map", 'ROADMAP', {}, { visibility : (!mixte && carto>0) });
					l = this.addGMAPLayer ( map, "Google Hybrid", 'HYBRID', {}, { visibility : mixte });
					jQuery.geoportail.setBaseLayer(map,l);
					// Pas d'info (masque le copyright)
					box.info = 0;
				break;
				// Bing Map
				case 'BING':
					var l;
					l = this.addBINGLayer ( map, "Bing Aerial", 'Aerial', {}, { visibility : (!mixte && ortho>0) });
					l = this.addBINGLayer ( map, "Bing Road", 'Road', {}, { visibility : (!mixte && carto>0) });
					l = this.addBINGLayer ( map, "Bing Hybrid", 'AerialWithLabels', {}, { visibility : mixte });
					/*
					l = this.addOSMLayer ( map, "OSM (Mapnik)", 
						"tile.openstreetmap.org",
						{	url: "http://wiki.openstreetmap.org/wiki/Mapnik",
							descriptif: "<p style='margin:0 1em'>Mapnik is the software we use to render the main Slippy Map layer for OSM, along with other layers such as the 'cycle map' layer and 'noname' layer. It is also the name given to the main layer, so things get a bit confusing sometimes.<br/>Map data (c) OpenStreetMap (and) contributors, CC-BY-SA</p>"
						},
						{	visibility : (this.osm_layer=='mapnik' && carto>0),
							opacity: carto
						}
					);
					*/
					jQuery.geoportail.setBaseLayer(map,l);
					// Pas d'info (masque le copyright)
					box.info = 0;
				break;
				// Yahoo! Map
				case 'YHOO':
					var l;
					l = this.addYHOOLayer ( map, "Yahoo Satelitte", YAHOO_MAP_SAT, {}, { visibility : (!mixte && ortho>0) });
					l = this.addYHOOLayer ( map, "Yahoo Map", YAHOO_MAP_REG, {}, { visibility : (!mixte && carto>0) });
					l = this.addYHOOLayer ( map, "Yahoo Hybrid", YAHOO_MAP_HYB, {}, { visibility : mixte });
					jQuery.geoportail.setBaseLayer(map,l);
					// Pas d'info (masque le copyright)
					box.info = 0;
				break;
				// Mini
				case 'mini': box.info = box.tools = box.layer = '0';				
				default: 
					map.addGeoportalLayers(); 
					// Affichage des des couches ORTHO et CARTO
					for (i = 0; i < map.getMap().layers.length; i++) 
					{	var lyr = map.getMap().layers[i];
						if (lyr.name == 'geoportal.catalogue.maps.theme.name' || lyr.name == "GEOGRAPHICALGRIDSYSTEMS.MAPS") 
						{	if (carto==0) lyr.setVisibility(false); 
							else lyr.setOpacity(carto); 
						}
						if (lyr.name == 'geoportal.catalogue.orthophotos.theme.name' || lyr.name == 'ORTHOIMAGERY.ORTHOPHOTOS') 
						{	if (ortho==0) lyr.setVisibility(false); 
							else lyr.setOpacity(ortho); 
						}
					}
			
				break;
			};

			// centrage
			if (lon) map.getMap().setCenterAtLonLat(lon, lat, ech);

			// Cas du type OpenLayers
			if (map.type == 'OpenLayers') 
			{	if (box.layer!='0' || box.layer=='false') 
					map.getMap().addControl(new OpenLayers.Control.PanZoom());
				if (box.layer!='0' && box.layer!='false') 
					map.getMap().addControl(new OpenLayers.Control.LayerSwitcher());
				box.tools = box.layer = '0';
			}
			// Gestion des controles
			switch (box.layer) 
			{	case '0':
				case 'false': map.setLayersPanelVisibility(false); break;
				case '1': 
				case 'true': map.setLayersPanelVisibility(true); break;
				default: map.openLayersPanel(false); break;
			}
			switch (box.tools) 
			{	case '0':
				case 'false': map.setToolsPanelVisibility(false); break;
				case 'mini': map.openToolsPanel(false); break;
				default: map.setToolsPanelVisibility(true); break;
			}
			switch (box.info) 
			{	case '0': case 0:
				case 'false': map.setInformationPanelVisibility(false); break;
				case '1':
				case 'true': map.setInformationPanelVisibility(true); break;
				case 'mini': 
				default : map.infoCntrl.minimizeControl();  break;
			}

			// Outils de recherche (un petit delais pour IE !)
			if (box.search=='' || box.search=='1') jQuery.geoportail.addSearchTools(map);
			// Outils d'impression
			if (box.toolprint=="1") 
			{	var nv= map.getMap().getControlsByClass('Geoportal.Control.NavToolbar')[0];
				nv.addControls([new Geoportal.Control.PrintMap()]);
			}
			// Outils de mesure
			if (box.measure=="1") jQuery.geoportail.addMeasureTools(map);
			// Afficher la vue globale
			if (box.overview=="1") jQuery.geoportail.setOverview(map);
			// Limiter le zoom
			if (visu.minZ) map.getMap().minZoomLevel = parseInt(visu.minZ);
			if (visu.maxZ) map.getMap().maxZoomLevel = parseInt(visu.maxZ);

			// Style d'affichage par defaut
			OpenLayers.Feature.Vector.style['default'].fillOpacity = 1;
			OpenLayers.Feature.Vector.style['select'].fillOpacity = 1;
		}
	},

	// Ajout des outils de mesure
	addMeasureTools: function(map) 
	{	var tbx = map.getMap().getControlsByClass('Geoportal.Control.ToolBox')[0];
		var measurebar = new Geoportal.Control.MeasureToolbar(
		{ div: OpenLayers.Util.getElement(tbx.id + '_measure'),
			displaySystem:
				(map.getMap().getProjection().proj.projName == 'longlat' ?
					'geographic'
				: 'metric'),
			targetElement: OpenLayers.Util.getElement(tbx.id + '_meares')
		}
		);
		map.getMap().addControl(measurebar);
	},

	// On a selectionne une adresse avec l'outil
	selectAdresse: function(f) 
	{	// Ne pas afficher le calque (risque d'interception d'evenements)
		if (f.layer) 
		{	var l = f.layer.map.getLayersByName("ADDRESSES.CROSSINGS:OPENLS");
			if (l.length > 0) l[0].setVisibility(false);
		}
		else if (this.layer) 
		{	this.layer.setVisibility(false);
		}
		// pour les autres cartes
		if (typeof (selectAdresse) == 'function') selectAdresse(f);
		else if (typeof (parent.selectFormulaireAdresse) == 'function') parent.selectFormulaireAdresse(f);
	},

	// Ajout des outils de recherche
	addSearchTools: function(map) {	// add "Search Toolbar" :
		var tbx = map.getMap().getControlsByClass('Geoportal.Control.ToolBox')[0];
		var searchbar = new Geoportal.Control.SearchToolbar(
			{
				div: OpenLayers.Util.getElement(tbx.id + '_search'),
				geonamesOptions: {
					// setZoom: Geoportal.Control.LocationUtilityService.GeoNames.setZoomForBDNyme,
					onSelectLocation: this.selectAdresse,
					layerOptions: {
						name: 'TOPONYMS.ALL:OPENLS',
						requestOptions:
						{	method:'GET'
						},
						formatOptions: {
							version: '1.0'
						}
					}
				},
				geocodeOptions: {
					onSelectLocation: this.selectAdresse,
					layerOptions: {
						name: 'ADDRESSES.CROSSINGS:OPENLS',
						requestOptions:
						{	method:'GET'
						},
						formatOptions: {
							version: '1.0'
						}
					},
					matchTypes: [
						{ re: /city/i, src: Geoportal.Util.getImagesLocation() + 'OLScity.gif' },
						{ re: /street$/i, src: Geoportal.Util.getImagesLocation() + 'OLSstreet.gif' },
						{ re: /number/i, src: Geoportal.Util.getImagesLocation() + 'OLSstreetnumber.gif' },
						{ re: /enhanced/i, src: Geoportal.Util.getImagesLocation() + 'OLSstreetenhanced.gif' },
						{ re: null, src: Geoportal.Util.getImagesLocation() + 'OLSstreet.gif' }
					]
				}
				/* TO BE REMOVED (1): NOT YET IN OPERATION */
				//,reverseGeocodeOptions: {
				//    layerOptions: {
				//        name: 'ADDRESSES.CROSSINGS:OPENLS',
				//        formatOptions: {
				//            version:'1.0'
				//        }
				//    },
				//    matchTypes: [
				//        {re:/city/i,    src:Geoportal.Util.getImagesLocation()+'OLScity.gif'},
				//        {re:/street$/i, src:Geoportal.Util.getImagesLocation()+'OLSstreet.gif'},
				//        {re:/number/i,  src:Geoportal.Util.getImagesLocation()+'OLSstreetnumber.gif'},
				//        {re:/enhanced/i,src:Geoportal.Util.getImagesLocation()+'OLSstreetenhanced.gif'},
				//        {re:null,       src:Geoportal.Util.getImagesLocation()+'OLSstreet.gif'}
				//    ]
				//}
				/* (1) */
			}
		);
		map.getMap().addControl(searchbar);
	},

	// Lancer une recherche par adresse
	formulaireAdresse: function(map, test) {
		var controls = map.getMap().controls;
		for (i = 0; i < controls.length; i++) {
			if (controls[i].CLASS_NAME == "Geoportal.Control.LocationUtilityService.Geocode") {
				if (!test) controls[i].activate();
				return true;
			}
		}
		return false;
	},

	/** Deselectionne tous les objets de la carte */
	unselectAll : function(id)
	{	if (typeof(id)=='undefined')
		{	var i;
			for (i=0; i<this.cartes.length; i++)
				if (this.cartes[i].map.hasOwnProperty('selectControl')) this.cartes[i].map.selectControl.unselectAll();
		}
		else
		{	var c = this.getCarte(id);
			if (c && c.map.hasOwnProperty('selectControl')) c.map.selectControl.unselectAll(); 
		}
	},
	
	/** Affichage d'une image dans une fenetre */
	afficheImage : function (src, titre)
	{	jQuery.geoportail.unselectAll();
		jQuery.jqDialog.wait();
		// Precharger l'image
		var imgPreloader = new Image();
		imgPreloader.onload = function()
		{	// Afficher l'image dans un jqDialog
			imgPreloader.onload = null;
			jQuery.jqDialog("",
				{	dialog: "<img src='"+this.src+"' width='100%'/><div class='legende'>"+this.title+"</div>",
					classe:'viewer',
					width: this.width,
					height: this.height,
					clickout:true,
					clickin:true,
					undo: false,
					speed:'fast',
					ok: false
				});
		}
		imgPreloader.title = titre;
		imgPreloader.src = src;
	},
	
	/** Fonction d'affichage d'un popup sur une carte
	* - feature : l'objet
	* - pos : position du curseur
	* - hover : survol
	*/
	popupFeature: function(feature, pos, hover) 
	{	var att = feature.attributes;
		// Fonction popup perso
		if (typeof (att.popup) == 'function')
		{	att.popup(feature, pos, hover);
			return;
		}
		// Style des Popups
		var spip_popup;
		var savePath = OpenLayers.ImgPath;
		if (hover) spip_popup = OpenLayers.Class(OpenLayers.Popup.Anchored, { 'autoSize': true, 'displayClass':"Anchored Anchored_hover" });
		else 
		{	/*
			if (jQuery.geoportail.spip_popup == "spip")
			{	OpenLayers.ImgPath = jQuery.geoportail.imgPath;		// Path du repertoire spip (image : cloud-popup-relative.png)
				spip_popup = OpenLayers.Class(OpenLayers.Popup['FramedCloud'], { 'autoSize': true, 'displayClass':jQuery.geoportail.spip_popup });
			}
			else 
			*/
			if (jQuery.geoportail.spip_popup == "spip") jQuery.geoportail.spip_popup = 'SpipPopup';
			spip_popup = OpenLayers.Class(OpenLayers.Popup[jQuery.geoportail.spip_popup], { 'autoSize': true, 'displayClass':jQuery.geoportail.spip_popup });
		}
		var html = "";
		var lien = null;
		if (att.url) 
		{	// Affichage des images
			if (att.extension in {'jpg':'','gif':'','png':''}) lien = "<a href=\"javascript:jQuery.geoportail.afficheImage('" + att.url + "','"+att.name.replace(/"/g,'\\\'').replace(/&/g,'&#38;')+"')\">";
			else lien = "<a href='" + att.url + "'>";
		}
		if (att.logo) html += (lien ? lien : "") + att.logo + (lien ? "</a>" : "");
		else if (att.img) html += (lien ? lien : "") + "<img src='"+att.img+"' class='spip_logos' />" + (lien ? "</a>" : "");
		if (att.name) html += "<p class=titre>"+ (lien ? lien : "") + att.name + (lien ? "</a>" : "") + "</p>";
		if (!hover)
		{	if (att.description) html += att.description;
			else 
			{	var t = "";
				for (var a in att) 
				{	if (att[a].value) t += "<tr><td>"+a+"</td><td>"+att[a].value+"</td></tr>"; // attributs KML
					else t += "<tr><td>"+a+"</td><td>"+att[a]+"</td></tr>";
				}
				if (t) html += "<table class='spip_popup'>"+t+"</table>";
			}
		}
		if (!html) return;
		html = "<div class='"+(att.classe ? att.classe : "")+"'>"+html+"</div>";
		var popup = new spip_popup("popup",
							pos ? pos:feature.geometry.getBounds().getCenterLonLat(),
							new OpenLayers.Size(200,200),
							html,
							pos ? { size:new OpenLayers.Size(0,5), offset: new OpenLayers.Pixel(0,-5) } : null,
							(hover?false:true),
							function(evt) { if (this.feature) jQuery.geoportail.unpopupFeature(this.feature); }
						);
		// Hauteur max 
		var h = 200;
		if (hover && att.logo)
		{	// Pour le survol : hauteur calculee sur le logo
			h = Number(att.logo.replace (/(.*height=.)([0-9]+)(.*)/,"$2"));
			h = (h?h+10:40);
		}
		popup.maxSize = new OpenLayers.Size(400, h);
		popup.minSize = new OpenLayers.Size(220, 0);
		popup.feature = feature; //mimic Geoportal.Popup (See closeFeatureInfo)
		popup.hover = hover;
		feature.popup = popup;
		feature.layer.map.addPopup(popup);
		// Remise de l'ancien path
		OpenLayers.ImgPath = savePath;
	},

	unpopupFeature: function(feature) 
	{	Geoportal.Control.unselectFeature(feature);
		feature.layer.drawFeature(feature, 'default');
		feature.popup = null;
	},
	
	/** Synchronisation de cartes */
	synchro: function (id_map)
	{	if (id_map.constructor.toString().indexOf("Array") == -1) return;
		var c = new Array;
		var i;
		for (i=0; i<id_map.length; i++) 
		{	var ci = this.getCarte(id_map[i]);
			if (ci)
			{	// Attendre le chargement de toutes les cartes...
				if (!ci.map) 
				{	var ids = "";
					for (k=0; k<id_map.length; k++) ids += (k!=0 ? ',':'')+id_map[k];
					window.setTimeout('jQuery.geoportail.synchro(['+ids+']);', 300);
					return;
				}
				c[i] = ci;
			}
		}
		for (i=0; i<c.length; i++) 
		{	if (!c[i].synchro) c[i].synchro = {}
			c[i].map.getMap().events.register('moveend', c[i], geoportail_moveSynchro);
			for (j=0; j<c.length; j++)
				if (c[i].id != id_map[j]) c[i].synchro[id_map[j]] = c[j];
		}
//		registerMouseMove();
	},
	
	/** Gestion de l'affichage plein ecran / adapte la carte a la dimention de la fenetre
		@param map : la carte a mettre en plein ecran
		@param divHeight : tableau des division a soustraire en hauteur (ex : ["#entete","#pied"])
		@param divWidth : tableau des division a soustraire en largeur (panneaux latteraux)
		@param callback : function callback(map,w,h) : a appeler en fin de redimentionnement (si d'autres choses a faire)
		
		Attention : 
		- il faut s'assurer que les div qui contiennent la map sont correctements parametrees (width et height = auto).
		- il faut s'assurer que les hauteurs / largeur des div pour le calcul (argument 2 et 3) sont correctement 
		  parametree dans le css (hauteur et largeur fixes de preference)
		
		Exemple d'utilisation :
		1- jQuery.geoportail.fullscreen (map0, ["#entete","#pied"]);	// Initialisation de la carte a afficher
		2- jQuery.geoportail.fullscreen ();			// Actualiser la dimention de la carte
		3- jQuery.geoportail.fullscreen (true);		// Masque les divHeigth et divWidth
		4- jQuery.geoportail.fullscreen (false);	// Affiche les divHeight et divWidth
		5- jQuery.geoportail.fullscreen ("toggle");	// Affiche/masque les divHeight et divWidth
		6- jQuery.geoportail.fullscreen ("no");		// Desactive l'affichage plein ecran (a appeler avant tout appel)
	*/
	fullscreen: function(map, divHeight, divWidth, callback)
	{	// Demande de reactualisation
		if (jQuery.geoportail.isFullscreen) 
		{	var i;
			if (map===true) 
			{	for (i=0; i<jQuery.geoportail.fsHeight.length; i++) jQuery(jQuery.geoportail.fsHeight[i]).slideUp(400);
				for (i=0; i<jQuery.geoportail.fsWidth.length; i++) jQuery(jQuery.geoportail.fsWidth[i]).hide();
				setTimeout (function(){ jQuery(window).resize() },500);
			}
			else if (map===false)
			{	for (i=0; i<jQuery.geoportail.fsHeight.length; i++) jQuery(jQuery.geoportail.fsHeight[i]).slideDown(400);
				for (i=0; i<jQuery.geoportail.fsWidth.length; i++) jQuery(jQuery.geoportail.fsWidth[i]).show();
				setTimeout (function(){ jQuery(window).resize() },500);
			}
			else if (map=="toggle") 
			{	if (jQuery.geoportail.isFullscreen && jQuery.geoportail.fsHeight.length > 0) 
					jQuery.geoportail.fullscreen(jQuery(jQuery.geoportail.fsHeight[0]).css('display')!='none');
			}
			else jQuery(window).resize();
			return;
		}
			
		if (!map || typeof(map)!="object" || !map.map) 
		{	// Demande de blocage
			if (map == "no") jQuery.geoportail.isFullscreen = true;
			return;
		}
		
		// Definition de la carte a mettre en plein ecran
		jQuery.geoportail.isFullscreen = true;
		jQuery.geoportail.fsCompteur = 0;
		jQuery.geoportail.fsHeight =(divHeight && divHeight.length) ? divHeight : new Array();
		jQuery.geoportail.fsWidth = (divWidth && divWidth.length) ? divWidth : new Array();
		jQuery.geoportail.fsMap = map;
		
		function delayResizeMap()
		{	jQuery.geoportail.fsCompteur--;
			if (jQuery.geoportail.fsCompteur > 0) return;
			jQuery.geoportail.fsCompteur = 0;
			if (!jQuery.geoportail.fsMap) return;
			var i;
			// Calcul hauteur et largeur
			var de = document.documentElement;
			var w = window.innerWidth || (de&&de.clientWidth) || document.body.clientWidth; // jQuery(window).width();
			var h = window.innerHeight || (de&&de.clientHeight) || document.body.clientHeight; // jQuery(window).height();
			var h2 = h;
			for (i=0; i<jQuery.geoportail.fsHeight.length; i++)
			{	h2 -= jQuery(jQuery.geoportail.fsHeight[i]).css('display')=='none' ? 0 : jQuery(jQuery.geoportail.fsHeight[i]).outerHeight(true);
			}
			var w2 = w;
			for (i=0; i<jQuery.geoportail.fsWidth.length; i++)
			{	w2 -= jQuery(jQuery.geoportail.fsWidth[i]).css('display')=='none' ? 0 : jQuery(jQuery.geoportail.fsWidth[i]).outerWidth(true);
			}
			
			jQuery.geoportail.fsMap.setSize(Math.round(w2),Math.round(h2));
			if (typeof (callback) == 'function') callback(map,h2,w2);
			// Forcer le refraichissement
			setTimeout("jQuery.geoportail.fsMap.getMap().setCenter(jQuery.geoportail.fsMap.getMap().getCenter())",500);
		}
		function resizeMap()
		{	jQuery.geoportail.fsCompteur++;
			setTimeout (delayResizeMap,200);
		}
		// Bug IE ??? <<
		/*
		for (i=0; i<jQuery.geoportail.fsHeight.length; i++)
			$(jQuery.geoportail.fsHeight[i]).css("margin",0).css("border","0px").css("padding",0);
		for (i=0; i<jQuery.geoportail.fsWidth.length; i++)
			$(jQuery.geoportail.fsWidth[i]).css("margin",0).css("border","0px").css("padding",0);
		*/
		// >>
		if (map) 
		{	// Au cas ou ce n'est pas fait dans le css...
			jQuery("body").css("width","100%").css("overflow","hidden");
			jQuery("#page").css("width","100%");
			jQuery("#"+map.div.id).parent().css("width","auto").css("height","auto");
			jQuery("#"+map.div.id).parent().parent().css("width","auto").css("height","auto");
			// Lancer la mise a jour
			resizeMap();
			if (map) jQuery(window).bind('resize', resizeMap);
		}
	}
}

// Pour ancien jQuery (SPIP 1.9.x), definition des fonctions
if (!$.fn.outerHeight)
{	jQuery.fn.extend ({
		outerHeight:function(b) 
		{	if (!this.length) return 0;
			var totalHeight = this.height();
			totalHeight += parseInt(this.css("padding-top")) + parseInt(this.css("padding-bottom"));			//Total Padding Height
			totalHeight += parseInt(this.css("borderTopWidth")) + parseInt(this.css("borderBottomWidth"));		//Total Border Height
			if (b) totalHeight += parseInt(this.css("margin-top")) + parseInt(this.css("margin-bottom"));		//Total Margin Height
			return totalHeight;
		},
		outerWidth:function(b) 
		{	if (!this.length) return 0;
			var totalWidth = this.width();
			totalWidth += parseInt(this.css("padding-left")) + parseInt(this.css("padding-right"));				//Total Padding Width
			totalWidth += parseInt(this.css("borderLeftWidth")) + parseInt(this.css("borderRightWidth"));		//Total Border Width
			if (b) totalWidth += parseInt(this.css("margin-left")) + parseInt(this.css("margin-right"));		//Total Margin Width
			return totalWidth;
		}
	});
}

/** Fonction de gestion de la synchronisation */
function geoportail_moveSynchro (e)
{	if (this.moving) return;
	var i;	// jQuery.geoportail.synchro([1,2,3])
	for (i in this.synchro)
	{	var c = this.synchro[i].map.getMap()
		this.synchro[i].moving = true;
			var pt = this.map.getMap().getCenter().clone();
			pt.transform(this.map.getMap().getProjectionObject(),c.getProjectionObject());
			c.setCenter(pt,this.map.getMap().getZoom());
		this.synchro[i].moving = false;
	}
}


/** Ajoute un layer selectionnable a une carte
@param l : le layer
*/
function geoportail_selectionnable (l, hover)
{	if (!(l instanceof Array)) l = [l];
	if (typeof(hover) == 'undefined') hover = jQuery.geoportail.hover;
	// Controle pour la selection
	options = {
			clickout: true, toggle: false,
			multiple: false, hover: (hover ? true : false),
			/*
			onSelect: jQuery.geoportail.popupFeature,
			*/
			onUnselect: jQuery.geoportail.unpopupFeature,
			// autoriser pan map quand on clique dans un objet
			handlersOptions:{feature:{stopDown:false}}, 
			callbacks:
			{	click: function (feature) 
				{	// Position du curseur
					var pos = this.layer.map.getLonLatFromPixel(this.handlers.feature.evt.xy);
					// Selectionner
					this.unselectAll(); 
					this.select(feature); 
					jQuery.geoportail.popupFeature(feature, pos, false); 
				},
				clickout: jQuery.geoportail.unpopupFeature,
				over:  hover ? function (feature, e) 
				{	// Position du curseur
					var pos = this.layer.map.getLonLatFromPixel(this.handlers.feature.evt.xy);
					// Selectionner
					this.unselectAll(); 
					this.select(feature);
					jQuery.geoportail.popupFeature(feature, pos, true); 
				}: null, 
				out:  hover ? function (feature) { if (feature.popup && feature.popup.hover) this.unselectAll(); } : null 
			}
		}
	var layers;
	if (this.selectControl) 
	{	this.selectControl.deactivate();
		layers = this.selectControl.layers;
		if (!layers) layers = [ this.selectControl.layer ];
		this.getMap().removeControl(this.selectControl);
	}
	else layers = new Array();
	layers = layers.concat(l);
	var selectControl = new OpenLayers.Control.SelectFeature(layers, options);
	this.getMap().addControl(selectControl);
	this.selectControl = selectControl;
	selectControl.activate();
	/* Patch pour permettre le changment d'opacite meme avec un control actif... 
		=> limite a 30% pour eviter les problemes d'objets fantomes.... 
	*/
	var i;
	for (i=0; i<l.length; i++)
	{	// Patch setOpatity to avoid selectControl to catch the event
		l[i].setOpacity = function (opacity) 
		{	var b = (selectControl && selectControl.active);
			if (b && opacity>0.3) selectControl.deactivate(); 
			OpenLayers.Layer.Vector.prototype.setOpacity.apply (this,[opacity]);
			if (b && opacity>0.3) selectControl.activate(); 
		};
	}
	
}

/** Fonction de telechargement
* Cette fonction permet d'exporter une couche dans un format reconnu par le GeoPortail
* Elle ecrit dans une div l'export et l'envoie (via download) a l'utilisateur.
@param formatType : le format d'export (kml ou gxt)
@param sel : le type de selection : all = tous les objets, extent =  l'extension de la carte, sinon les objets selectionnes
@param proj : la projection qu'on veut
@param options : { fichier:geoportail, type:'remarque', sstype:'point', attributs:["remarque","commune","departement"] }
*/
function geoportail_loadData (formatType, sel, proj, options)
{	var str, format, formatf;
	// Recherche le format
	switch (formatType)
	{	case 'kml':
			formatf = OpenLayers.Format.KML;
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'osm' :
			formatf = OpenLayers.Format.OSM;
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'gml' :
			formatf = OpenLayers.Format.GML;
			if (!proj) proj = 'IGNF:RGF93G';
			if (!options) options={};
			if (!options.featureNS) options.featureNS='http://interop.ign.fr/exchange';
		break;
		case 'gpx':
			formatf = Geoportal.Format.GPX;
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'gxt':
			//format = new Geoportal.Format.Geoconcept( options );
			formatf = Geoportal.Format.Geoconcept.rip;
			if (!proj) proj = 'IGNF:LAMB93';
		break;
		default:
			return false;
		break;
	}
	// Changement de projection
	if (!options) options={};
	options.internalProjection = this.getMap().getProjection();
	options.externalProjection = new OpenLayers.Projection(proj);
	format = new formatf(options);
	// Ecrire l'export
	if (sel=='all') str = format.write(this.rlayer.features);
	else if (sel=='extent')
	{	var ext = this.getMap().getExtent();
		var pts = [];
		pts.push (new OpenLayers.Geometry.Point(ext.left,ext.top));
		pts.push (new OpenLayers.Geometry.Point(ext.right,ext.top));
		pts.push (new OpenLayers.Geometry.Point(ext.right,ext.bottom));
		pts.push (new OpenLayers.Geometry.Point(ext.left,ext.bottom));
		pts.push (new OpenLayers.Geometry.Point(ext.left,ext.top));
		var l = new OpenLayers.Geometry.LinearRing(pts);
		var polygonFeature = new OpenLayers.Feature.Vector(l);
		str = format.write(polygonFeature);
	}
	else str = format.write(this.rlayer.selectedFeatures);
	
	// Ecriture et telechargement
	var fic = (options && options.fichier) ? options.fichier:"geoportail";
	if (jQuery("#geoportailExport").length==0)
	{	jQuery("<div><form id='geoportailExport' name='export' action='?page=download&hash="+jQuery.geoportail.hash+"' method='POST' style='display:none'>"
			+"<textarea id='geoportailExportData' name='data'></textarea>"
			+"<input type=hidden value='txt' name='format' id='geoportailExportFormat' />"
			+"<input type=hidden value='"+fic+"' id='name' name='name' />"
			+"</form></div>").appendTo('body');
	}
	document.getElementById('geoportailExportFormat').value = formatType;
	document.getElementById('geoportailExportData').value = str;
	document.getElementById('name').value = fic;
	document.getElementById('geoportailExport').submit();
	
	return true;
}

/** Fonction afficher / masquer une classe (class_show) 
	avec effet de slide (class_slide).
	Charger le contenu de class_slide (de type iframe).
	Afficher une patience : class_patience (a masquer une fois la classe chargee).
*/
function jqToogleClass (cl, href) 
{	jQuery(cl+'_show').toggle();
	var slider = jQuery(cl+'_slide');
	slider.animate
	(	{height:'toggle'}, 
		50, 
		// Sauvegarder l'etat dans un cookie
		function() 
		{	if (jQuery.cookie) jQuery.cookie( cl , (slider.css('display')=='none')? 'hidden' : null );
		}
	);
	// Charger le lien
	if ( href && slider.length>0 && !slider[0].isload )
	{	slider[0].src = href;
		slider[0].isload = true;
		jQuery(cl+'_patience').show();
		slider.load ( function() {jQuery(cl+'_patience').hide();} );
	}
}

// Masquer si cookie actif ?
function jqToogleShow(cl)
{	if (jQuery.cookie(cl)) 
	{	jQuery(cl+'_show').toggle();
		jQuery(cl+'_slide').toggle();
	}
}


/** Fonction de recherche de commune dans le RGC
*	
*	Requete du type :
*	- q=nom commune&zone=FXX
*	ou 
*	- lon=X&lat=Y&zone=FXX
*	
*	Renvoie un objet :
*	success ({name:"nom", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", lon:x, lat:y })
*	ou
*	success ({ name:"paris", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", d:distance })
*/
(function($) { // Pour jQuery.noConflict()

	$.jqGeoSearch = function(q, options)
	{	// Options par defaut
		$.jqGeoSearch.param = $.extend({}, $.jqGeoSearch.defaults, options);

		// Info sur la fenetre
		var de = document.documentElement;
		var wt = $("body").width() + parseInt( $("body").css("margin-right")) + parseInt( $("body").css("margin-left")) +15;
		var ht = $("body").height() + parseInt( $("body").css("margin-top")) + parseInt( $("body").css("margin-bottom")) +15;
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight
		var scrollX = (typeof( window.pageXOffset ) == 'number') ? window.pageXOffset : (document.body && document.body.scrollLef) ? document.body.scrollLef : (document.documentElement) ? document.documentElement.scrollLeft : 0;
		var scrollY = (typeof( window.pageYOffset ) == 'number') ? window.pageYOffset : (document.body && document.body.scrollTop) ? document.body.scrollTop : (document.documentElement) ? document.documentElement.scrollTop : 0;
		// Verifier qu'on a ce qu'il faut, sinon le creer :
		if (jQuery('#query_back').length == 0)
		{	var back = $("<div class=query_back id=query_back "
				+"style='position:absolute; background-color:black; z-index:2000; display:none;' >"
				+"</div>").width(wt).height(ht).css("left",0).css("top",0).css("opacity",0.4).appendTo("body");
		}
		if (jQuery('#query').length == 0)
		{	var query = $("<div class=query id=query "
				+"style='position:absolute; z-index:2001; display:none;' >"
				+"<div class='jqCloseButton' onclick='javascript:$.jqGeoSearch.cancel()'></div>"
				+"<p>"+$.jqGeoSearch.param.title
				+($.jqGeoSearch.param.info!=''?"<br/><small>"+$.jqGeoSearch.param.info+"</small>":"")
				+"</p><ul></ul>"
				+"</div>").appendTo("body");
		}
		// Centrer la fenetre pour le choix
		jQuery('#query').css('left',scrollX+(w-jQuery('#query').width())/2);
		jQuery('#query').css('top',scrollY+(h-jQuery('#query').height())/2);
		
		// Envoyer la requete Ajax
		jQuery('#query_back').show();
		jQuery.ajax(
			{	type	: 'GET', 
				url		: $.jqGeoSearch.param['path']+'spip.php', 
				data	: "action=geoportail_search&"+q, 
				success	: $.jqGeoSearch.select,
				error	: $.jqGeoSearch.error
			}
		);	
	}
	
	// Traiter la selection
	$.jqGeoSearch.select = function (msg, success, nb)
	{   var t = Array();
		// Recuperer le tableau
		if (isFinite(nb)) t.push($.jqGeoSearch.param.obj[nb]);
		else if (typeof(msg)=="object") t=msg;
		else eval ("t = " + msg);
		
		// Rien trouver
		if (t.length == 0) 
		{	$.jqGeoSearch.param.success(null);
			$.jqGeoSearch.cancel();
			return; 
		}
		// Une seule solution
		else if (t.length == 1) 
		{	$.jqGeoSearch.param.success(t[0]);
			$.jqGeoSearch.cancel();
			return;
		}
		// Demande a l'utilisateur de choisir
		else
		{	var html='';
			$.jqGeoSearch.param.obj = t;
			for (i=0; i<t.length; i++) if (t[i])
			{	html += '<li><a href="javascript:$.jqGeoSearch.select(null,true,'+i+')">'
				+t[i]["name"]+' ('+t[i]["nadm"]+')</a></li>';
			}
			jQuery('#query ul').html(html);
			jQuery('#query').show();
			return;
		}
	}
	
	// Zut rate !
	$.jqGeoSearch.error = function (hrequest, msg, obj)
	{	$.jqGeoSearch.param.error(hrequest, msg);
		$.jqGeoSearch.cancel();
	}
	
	// Fermer les fenetres
	$.jqGeoSearch.cancel = function ()
	{	if ($.jqGeoSearch.param.submit) return;
		jQuery('#query_back').hide();
		jQuery('#query').hide();
	}
	
	// Options
	$.jqGeoSearch.defaults = {
 		title	: 'S&eacute;lectionner une destination...',
 		info	: '',
 		submit	: false,
 		path	: '',
		success	: function (resp)		// Ca marche !
		{	alert (resp['name']+" ("+resp['nadm']+")");
		},
		error	: function (resp, e)		// Ooops
		{	alert("[searchError] "+e);
		}
	};
	
	// Parametres en cours
	$.jqGeoSearch.param = $.jqGeoSearch.defaults;
	

})(jQuery);