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
    initMap: function(dirPlug) {	//alert ("initMap '"+typeof(Geoportal)+"'"+OpenLayers.Layer.Vector.Locator);
        // IE demande une attente
        if (jQuery.browser["msie"]) {	// API chargee ?
            if (typeof (Geoportal) == 'undefined' ||
				typeof (Geoportal.Viewer) == 'undefined' ||
				typeof (Geoportal.Viewer.Standard) == 'undefined' ||
				typeof (document.namespaces) != 'object')
            //if (typeof(Geoportal) == "undefined") 
            {
                setTimeout("jQuery.geoportail.initMap()", 500); //500 millisecondes
                return;
            }
        }
        // Chargement des classes Geoportail (pour IE
        Geoportal.Util.loadJS(dirPlug + "js/Format/Ceoconcept_rip.js");
        Geoportal.Util.loadJS(dirPlug + "js/Layer/Locator.js");
        Geoportal.Util.loadJS(dirPlug + "js/Layer/GXT.js");

        //
        var i;
        for (i = 0; i < this.cartes.length; i++) {
            var carte = this.cartes[i];
            // Charger
            carte.geoportalLoadmap();

            // La carte geoportail
            var map = carte.map;
            var id = carte.id;
            // Ajouter une fonction d'export
            map.downloadData = geoportail_loadData;
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
            // Afficher les documents
            var k;
            for (k = 0; k < carte.doc.length; k++)
                this.addLayer(carte, carte.doc[k]['extension'], carte.doc[k]['id_document'], carte.doc[k]['titre'], carte.doc[k]['fichier'], carte.doc[k]['nozoom']);
            // Afficher des images
            if (carte.img.length) {	// Rajoute une couche pour les points
                var t = carte.img[0].titre;
                var l = new OpenLayers.Layer.Vector(t ? t : "IMG", { opacity: 1, visibility: 1, originators: jQuery.geoportail.originators });
                map.spip_img = l;
                map.getMap().addLayer(l);
                for (k = 0; k < carte.img.length; k++) {
                    var img = carte.img[k];
                    var feature = jQuery.geoportail.createFeature(Number(img.lon), Number(img.lat), img.logo, img.taille, null, img.align);
                    feature.attributes = img.attributes;
                    l.addFeatures(feature);
                }
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
            if (!div) return;
            ovDiv = document.createElement('div');
            ovDiv.id = OpenLayers.Util.createUniqueID('overviewMap');
            ovDiv.className = 'overviewMap';
            ovDiv.style.width = w + "px";
            ovDiv.style.margin = "auto";
            div.appendChild(ovDiv);
        }
        // photo ou carte ?
        var fond = photo ? 'ORTHOIMAGERY.ORTHOPHOTOS:WMSC' : 'GEOGRAPHICALGRIDSYSTEMS.MAPS:WMSC';
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
    lien: function(id_map) {
        var carte = this.getCarte(id_map);
        if (carte) {
            var map = carte.map.getMap();
            var pos = map.getCenter();
            var zoom = map.getZoom();
            var ortho, carto;
            for (i = 0; i < map.layers.length; i++) {
                var lyr = map.layers[i];
                if (!lyr.displayInLayerSwitcher) continue;
                if (lyr.name == 'geoportal.catalogue.maps.theme.name' || lyr.name == "GEOGRAPHICALGRIDSYSTEMS.MAPS") {
                    if (!lyr.visibility) carto = 0;
                    else carto = Math.round(lyr.opacity * 10) / 10;
                }
                if (lyr.name == 'geoportal.catalogue.orthophotos.theme.name' || lyr.name == 'ORTHOIMAGERY.ORTHOPHOTOS') {
                    if (!lyr.visibility) ortho = 0;
                    else ortho = Math.round(lyr.opacity * 10) / 10;
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
    addDoc: function(id_map, ext, id_document, titre, fic, nozoom) {
        ext = ext.toUpperCase();
        // Verifier OK
        if (ext != 'GPX' && ext != 'KML' && ext != 'GXT') return;
        // Ajouter
        var carte = this.getCarte(id_map);
        if (carte) {
            carte.doc[carte.doc.length] = { extension: ext, titre: titre, id_document: id_document, fichier: fic, nozoom: nozoom };
        }
    },

    // Ajouter une image
    addImg: function(id_map, id_document, titre, lon, lat, logo, align, taille, attributes) {
        var carte = this.getCarte(id_map);
        if (carte) {
            carte.img[carte.img.length] = { titre: titre, lon: lon, lat: lat, id_document: id_document, attributes: attributes, logo: logo, align: align, taille: Number(taille) };
        }
    },

    /** Ajouter un fichier GPX,KML,GXT
    Zoom sur l'extension du fichier
    */
    addLayer: function(carte, type, id_document, name, url, nozoom) {
        var map = carte.map;
        // Ajouter la couche
        var l;
        if (type == "GXT") {
            l = new OpenLayers.Layer.GXT(name, url);
            map.getMap().addLayer(l);
        }
        else l = map.getMap().addLayer(type, name, url, { opacity: 1, visibility: true, originators: jQuery.geoportail.originators });
        if (l) {	// Definition des styles
            l.styleMap.styles['default'].defaultStyle = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
            l.styleMap.styles['default'].defaultStyle.pointRadius = 1;
            l.styleMap.styles['default'].defaultStyle.externalGraphic = null;
            l.styleMap.styles['default'].defaultStyle.strokeWidth = 2;
            l.styleMap.styles['default'].defaultStyle.strokeColor = "#0000ff";
            l.styleMap.styles['default'].defaultStyle.graphicOpacity = 1;

            // Sauvegarder l'id du document
            l.id_document = id_document;

            // Faire quelque chose quand le calque est charge
            if (!nozoom && !carte.fixe) {	// Calcul et zoom sur l'extension (une fois charge)
                l.events.register("loadend", l, function(e) {
                    if (this.features.length) {
                        this.extent = new OpenLayers.Bounds();
                        // this.extent.extend (map.getMap().center);
                        for (var i = 0; i < this.features.length; i++) {
                            this.extent.extend(this.features[i].geometry.getBounds());
                            // Bug KML (en beta2)
                            if (this.features[i].style && !this.features[i].style.strokeColor) {
                                this.features[i].style = this.styleMap.styles['default'].defaultStyle;
                            }
                        }
                        map.getMap().zoomToExtent(this.extent);
                    }
                    // Fonction utilisateur onLoadSpipDoc#ID_DOCUMENT (layer)
                    var fdoc = 'onLoadSpipDoc' + this.id_document;
                    try { fdoc = eval(fdoc); } catch (error) { }
                    if (typeof (fdoc) == 'function') fdoc(this.id_document, this);
                    // Fonction utilisateur onLoadSpipDoc (id_document, layer)
                    else if (typeof (onLoadSpipDoc) == 'function') {
                        onLoadSpipDoc(this.id_document, this);
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
        }
    },

    // Creation d'une feature openlayer
    createFeature: function(lon, lat, logo, size, ol, align) {
        if (!ol) ol = OpenLayers;
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
            // Alignement
            align = align.split('-');
            for (i = 0; i < align.length; i++) switch (align[i]) {
                case 'right': feature.style.graphicXOffset = 0; break;
                case 'left': feature.style.graphicXOffset = -2 * size; break;
                case 'top': feature.style.graphicYOffset = 0; break;
                case 'bottom': feature.style.graphicYOffset = -2 * size; break;
                default: break;
            }
            feature.photo = true;
        }

        return feature;
    },

    // Fonction principale pour l'affichage de la carte
    showMap: function(map, zone, lon, lat, ech, layerctrl, toolbox, infobox, carto, ortho) {
        // Parametres de la vue
        switch (zone) {
            case "MTQ":
                {
                    if (!lon) lon = -61;
                    if (!lat) lat = 14.65;
                    if (!ech) ech = 9;
                    break;
                }
            case "REU":
                {
                    if (!lon) lon = 55.5;
                    if (!lat) lat = -21.1;
                    if (!ech) ech = 8;
                    break;
                }
            case "GUF":
                {
                    if (!lon) lon = -53;
                    if (!lat) lat = 4.8;
                    if (!ech) ech = 7;
                    break;
                }
            case "GLP":
                {
                    if (!lon) lon = -61.42;
                    if (!lat) lat = 16.17;
                    if (!ech) ech = 8;
                    break;
                }
            case "MYT":
                {
                    if (!lon) lon = 45.13;
                    if (!lat) lat = -12.82;
                    if (!ech) ech = 9;
                    break;
                }
            case "FXX":
                {
                    if (!lon) lon = 1.7;
                    if (!lat) lat = 46.95;
                    if (!ech) ech = 5;
                    break;
                }
            default:
                break;
        }

        // Rechercher les couches a afficher
        if (map.getMap().allowedGeoportalLayers) {
            var i;
            // BUG IExplorer (fort ralentissement)
            if (false)//!jQuery.browser["msie"])
            {
                var olayersId = map.getMap().catalogue._orderLayersStack(map.getMap().allowedGeoportalLayers);
                // Ne pas afficher tous les layers
                for (i = 0; i < olayersId.length; i++) {
                    switch (olayersId[i]) {	// Ne pas afficher
                        case 'SEAREGIONS.LEVEL0:WMSC':
                        case 'ELEVATION.SLOPS:WMSC':
                        case 'CADASTRALPARCELS.PARCELS:WMSC':
                        case 'HYDROGRAPHY.HYDROGRAPHY:WMSC':
                        case 'TRANSPORTNETWORKS.ROADS:WMSC':
                        case 'TRANSPORTNETWORKS.RAILWAYS:WMSC':
                        case 'TRANSPORTNETWORKS.RUNWAYS:WMSC':
                        case 'BUILDINGS.BUILDINGS:WMSC':
                        case 'UTILITYANDGOVERNMENTALSERVICES.ALL:WMSC':
                        case 'ADMINISTRATIVEUNITS.BOUNDARIES:WMSC':
                            break;
                        // Afficher  
                        default:
                            map.addGeoportalLayer(olayersId[i]);
                            break;
                    }
                }
            }
            else
                map.addGeoportalLayers();
            // centrage
            if (lon) map.getMap().setCenterAtLonLat(lon, lat, ech);

            // Gestion des controles
            switch (layerctrl) {
                case 0:
                case 'false': map.setLayersPanelVisibility(false); break;
                case 'true': map.setLayersPanelVisibility(true); break;
                default: map.openLayersPanel(false); break;
            }
            switch (toolbox) {
                case 0:
                case 'false': map.setToolsPanelVisibility(false); break;
                case 'mini': map.openToolsPanel(false); break;
                default: map.setToolsPanelVisibility(true); break;
            }
            switch (infobox) {
                case 0:
                case 'false': map.setInformationPanelVisibility(false); break;
                default: map.setInformationPanelVisibility(true); break;
            }

            // Forcer l'affichage ortho/carto
            if (this.getParam("ortho")) ortho = this.getParam("ortho");
            if (this.getParam("carto")) carto = this.getParam("carto");

            // Affichage des des couches ORTHO et CARTO
            for (i = 0; i < map.getMap().layers.length; i++) {
                var lyr = map.getMap().layers[i];
                if (lyr.name == 'geoportal.catalogue.maps.theme.name' || lyr.name == "GEOGRAPHICALGRIDSYSTEMS.MAPS") {
                    switch (carto) {
                        case '0': lyr.setVisibility(false); break;
                        case '': break;
                        default: lyr.setOpacity(carto); break;
                    }
                }
                if (lyr.name == 'geoportal.catalogue.orthophotos.theme.name' || lyr.name == 'ORTHOIMAGERY.ORTHOPHOTOS') {
                    switch (ortho) {
                        case '0': lyr.setVisibility(false); break;
                        case '': break;
                        default: lyr.setOpacity(ortho); break;
                    }
                }
            }

            // Style d'affichage par defaut
            OpenLayers.Feature.Vector.style['default'].fillOpacity = 1;
            OpenLayers.Feature.Vector.style['select'].fillOpacity = 1;
            /*
            OpenLayers.Feature.Vector.style['default'].pointRadius = 20;
            OpenLayers.Feature.Vector.style['default'].graphicYOffset = -35;
            OpenLayers.Feature.Vector.style['default'].graphicXOffset = -6;
            OpenLayers.Feature.Vector.style['select'].pointRadius = 20;
            */
        }
    },

    // Ajout des outils de mesure
    addMeasureTools: function(map) {
        var tbx = map.getMap().getControlsByClass('Geoportal.Control.ToolBox')[0];
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
        {   var l = f.layer.map.getLayersByName("ADDRESSES.CROSSINGS:OPENLS");
            if (l.length > 0) l[0].setVisibility(false);
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
			        //					setZoom: Geoportal.Control.LocationUtilityService.GeoNames.setZoomForBDNyme,
			        onSelectAddress: this.selectAdresse,
			        layerOptions: {
			            name: 'TOPONYMS.ALL:OPENLS',
			            formatOptions: {
			                version: '1.0'
			            }
			        }
			    },
			    geocodeOptions: {
			        onSelectAddress: this.selectAdresse,
			        layerOptions: {
			            name: 'ADDRESSES.CROSSINGS:OPENLS',
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

    // Fonction d'affichage d'un popup sur une carte
    popupFeature: function(feature) {
        //    popup = new OpenLayers.Popup.FramedCloud("popup", 
        popup = new OpenLayers.Popup.AnchoredBubble("popup",
										feature.geometry.getBounds().getCenterLonLat(),
										new OpenLayers.Size(200, 100),
										feature.attributes.img +
										"<p class=titre><a href='" + feature.attributes.link + "'>" + feature.attributes.name + "</a></p>"
										+ feature.attributes.description
										,
										null, true,
										function(evt) {
										    if (this.feature) $.geoportail.unpopupFeature(this.feature);
										});

        popup.maxSize = new OpenLayers.Size(400, 200);
        popup.minSize = new OpenLayers.Size(220, 0);
        popup.feature = feature; //mimic Geoportal.Popup (See closeFeatureInfo)
        feature.popup = popup;
        popup.setBackgroundColor("#ffffcc");
        feature.layer.map.addPopup(popup);
    },

    unpopupFeature: function(feature) {
        Geoportal.Control.unselectFeature(feature);
        feature.layer.drawFeature(feature, 'default');
        feature.popup = null;
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
{	// Changement de format
	var str, format;
	switch (formatType)
	{	case 'kml':
			format = new OpenLayers.Format.KML( options );
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'osm' :
			format = new OpenLayers.Format.OSM( options );
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'gml' :
			format = new OpenLayers.Format.GML( options );
			if (!proj) proj = 'IGNF:RGF93G';
			if (!options) options={};
			if (!options.featureNS) options.featureNS='http://interop.ign.fr/exchange';
		break;
		case 'gpx':
			format = new Geoportal.Format.GPX( options );
			if (!proj) proj = 'IGNF:RGF93G';
		break;
		case 'gxt':
			//format = new Geoportal.Format.Geoconcept( options );
			format = new Geoportal.Format.Geoconcept.rip( options );
			if (!proj) proj = 'IGNF:LAMB93';
		break;
		default:
			return false;
		break;
	}
	// Changement de projection
	format.internalProjection = this.getMap().getProjection();
	format.externalProjection = new OpenLayers.Projection(proj);
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
			+"<input type=hidden value='"+fic+"' name='name' />"
			+"</form></div>").appendTo('body');
	}
	document.getElementById('geoportailExportFormat').value = formatType;
	document.getElementById('geoportailExportData').value = str;
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
