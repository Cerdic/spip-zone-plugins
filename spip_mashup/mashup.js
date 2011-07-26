/**
* Plugin SPIP-Mashup
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* SPIP-Mashup
* - Gestion de l'affichage de la carte en plein ecran
* - Chargement d'une rubrique en AJAX (load -> loadContenu -> loadDocument -> loadRubrique -> addFeatures )
* - Redirection des liens sur la page
*
**/
// Pour ancien jQuery (SPIP 1.9.x), definition des outerHeight et outerWidth
if (!$.fn.outerHeight)
{	jQuery.fn.extend ({
		outerHeight:function(b) 
		{	var totalHeight = this.height();
			totalHeight += parseInt(this.css("padding-top"), 0) + parseInt(this.css("padding-bottom"), 0);			//Total Padding Height
			totalHeight += parseInt(this.css("borderTopWidth"), 0) + parseInt(this.css("borderBottomWidth"), 0);	//Total Border Height
			if (b) totalHeight += parseInt(this.css("margin-top"), 0) + parseInt(this.css("margin-bottom"), 0);		//Total Margin Height
			return totalHeight;
		},
		outerWidth:function(b) 
		{	var totalWidth = this.width();
			totalWidth += parseInt(this.css("padding-left"), 0) + parseInt(this.css("padding-right"), 0);			//Total Padding Width
			totalWidth += parseInt(this.css("borderLeftWidth"), 0) + parseInt(this.css("borderRightWidth"), 0);		//Total Border Width
			if (b) totalWidth += parseInt(this.css("margin-left"), 0) + parseInt(this.css("margin-right"), 0);		//Total Margin Width
			return totalWidth;
		}
	});
}

/** Fonction SPIP-Geoportail : 
* fonction appelee apres le chargement d'un document (KML, GPX, etc.)
* transmet la description du layer aux objets 
*/
function onLoadSpipDoc(id, l)
{	var i;
	var lien = 'javascript:mashup.showObject("'+l.info.objet+'",'+l.info.id+')'; 
	for (i=0; i<l.features.length; i++)
	{	if (l.info.description) l.features[i].attributes.description = l.info.description;
		if (lien) l.features[i].attributes.url = lien;
		l.features[i].attributes.logo = l.info.logo;
		l.features[i].attributes.description += "<p class='savoirplus'><a class='lien' href='"+l.features[i].attributes.url+"'>"+mashup._T['plus']+"</a></p>";
		// Substitue les popup
		l.features[i].attributes.popup = mashup.popup;
		l.features[i].attributes.classe = l.info.objet;
		l.features[i].attributes.id = l.info.id;
	}
}

/** Mashup 
*/
var mashup =
{	// options (definies dans le cache SPIP)
	options:
	{	// Taille des vignettes
		largeur : 25,  
		largeur_mot : 10, 
		// Toujours afficher les images en fond
		backLayer:true,
		// Utilise le zoom du conteneur pour l'affichage des objets
		zoom_pere:true,
		// Utilise le zoom des images
		zoom_img:true,
		// Ne pas afficher de popup (acces direct)
		no_popup:true
	},
	
	// Texte (definies dans le cache SPIP)
	_T : {},
	// Les layers du mashup
	_layers : new Array(),
	// Layer de fond (pour affichage des images a toutes les echelles)
	_backLayer : null,
	// Les echelles du mashup
	_scales : new Array(),
	
	// Eviter redimentionnement multiple
	resizeCompteur:0,

	/** Fonction principale : Lecture du contenu de la rubrique en Ajax */
	load : function(id_rubrique, legende)
	{	// Mettre aux dimentions de la fenetre
		mashup.resizeMap(true);
		// Recherche du var_mode
		var mode = $.geoportail.getParam('var_mode');
		// Rechercher
		jQuery.ajax(
			{	type	: 'GET', 
				url		: 'spip.php', 
				data	: "page=mashup_json&id_rubrique="+id_rubrique+"&legende="+legende+(mode?"&var_mode="+mode:""), 
				success	: mashup.loadContenu,
				error	: function (hrequest, msg, obj)
						{	alert (msg);
						}
			}
		);
//		$.jqDialog.wait();
		$.jqDialog ("", { dialog:"Chargement en cours...", classe:"patience", icon:mashup.waitIcon, undo: false, ok: false });
	},
	

	/** Afficher la carte en plein ecran */
	delayResizeMap : function()
	{	if ((--mashup.resizeCompteur) > 0) return;
		mashup.resizeCompteur = 0;
		var w = $(window).width();
		var h = $(window).height();
		var h2 = h	- ($("#entete").css('display')=='none'?0:$("#entete").outerHeight(true)) 
					- ($("#pied").css('display')=='none'?0:$("#pied").outerHeight(true)) ;
		map0.setSize(w,Math.round(h2));
		$("#jqDialog_back").width(w).height(h);
	},
	resizeMap : function()
	{	mashup.resizeCompteur++;
		setTimeout ("mashup.delayResizeMap()",200);
	},

	pleinEcran : function(quoi)
	{	// Masquer le haut
		if (quoi == 'haut' || (!quoi && !$('.gpViewerUpperSeparatorTop').length))
		{	$('#entete').slideToggle('fast',mashup.delayResizeMap);
			var s = $('.gpViewerUpperSeparatorTop');
			if (s.length) s.removeClass('gpViewerUpperSeparatorTop');
			else $('.gpViewerUpperSeparator').addClass('gpViewerUpperSeparatorTop');
		}
		// Masquer le bas
		if (quoi == 'bas' || (!quoi && !$('.gpViewerLowerSeparatorBottom').length))
		{	$('#pied').slideToggle(0,mashup.delayResizeMap);
			var s = $('.gpViewerLowerSeparatorBottom');
			if (s.length) s.removeClass('gpViewerLowerSeparatorBottom');
			else $('.gpViewerLowerSeparator').addClass('gpViewerLowerSeparatorBottom');
		}
		// Masquer les palettes
		if (!quoi) 
		{	map0.openLayersPanel(false);
			map0.openToolsPanel(false);
		}
	},
	
	/** Centrer la carte + deselectionner */
	center: function(lon, lat, zoom)
	{	//map0.getMap().setCenterAtLonLat(lon, lat, zoom);
		var pt = new OpenLayers.LonLat(lon, lat);
		pt.transform(new OpenLayers.Projection('IGNF:RGF93G'), map0.getMap().getProjection());
		map0.getMap().zoomTo(zoom);
		map0.getMap().panTo(pt);
		map0.selectControl.unselectAll();
	},

	/** Affichage d'un objet dans un dialogue sur la page en Ajax
	*/
	showObject : function(obj, id, debut)
	{	// Mettre en attente sauf si lien de pagination (meme fenetre).
		if (!debut) $.jqDialog.wait();
		$.geoportail.unselectAll();

		// Recherche du var_mode
		var mode = $.geoportail.getParam('var_mode');
		var url;
		// forcer le recalcul du cache
		if (mode) url = "page=mashup_"+obj+"&id_"+obj+"="+id+"&id_mashup="+mashup.doc.id+(mode?"&var_mode="+mode:"")+(debut?"&debut_"+debut+"#pagination_a":"");
		// ou page standard (pour compteurs de visite, etc.)
		else url =  obj+id+"&ajax_mashup=1&id_mashup="+mashup.doc.id+(mode?"&var_mode="+mode:"")+(debut?"&debut_"+debut+"#pagination_a":"");
		
		// Charger
		jQuery.ajax(
			{	type	: 'GET', 
				url		: 'spip.php', 
				data	: url, 
				success	: function(msg, success)
						{	// Recuperer le titre du dialogue dans le commentaire
							var titre = msg.split(/.*\<!-- *titre *=/i);
							if (titre.length>1) 
							{	titre = titre[1].split("-->");
								titre = titre[0];
							}
							else titre="";
							// Afficher
							$.jqDialog (titre, 
								{	dialog:msg ,
									classe:'boiteInfo boiteInfo_'+obj,
									clickout:true,
									shadow:10,
									//bgopacity:0.1,
									//speed:'slow',
									undo: false,
									ok: false
								});
							// Rediriger les liens
							$("#jqDialog .jqDialogBlock a").click(mashup.redirect);
							// Gestion des liens pagination : stocker l'info pour l'appel
							$("#jqDialog .lien_pagination").each( function () 
								{	this.info = { obj:obj, id:id }; 
								});	
							// Stocker les images pour l'affichage
							mashup.img = new Array();
							$("#jqDialog .jqDialogBlock a").each( function () 
								{	if (this.type.match(/^image\.*/i)) mashup.img[mashup.img.length] = { href:this.href, title:this.title }; 
								});

						},
				error	: function (hrequest, msg, obj)
						{	$("#jqDialog_back").removeClass('jqDialog_wait');
							alert (msg);
						}
			}
		);
	},
	
	/** Affichage d'un diaporama */
	diaporama : function (action, src)
	{	// une seule image ?
		if (mashup.img.length<2) 
		{	$.geoportail.afficheImage (src, mashup.img[0].title);
			return;
		}
		var i;
		//
		switch (action)
		{	case "src":
				for (i=0; i<mashup.img.length; i++) 
					if (mashup.img[i].href == src) break;
					mashup.img['pos'] = i;
				break;
			case "next":
				mashup.img['pos']++;
				if (mashup.img['pos'] >= mashup.img.length) mashup.img['pos'] = 0;
				break;
			case "prev":
				mashup.img['pos']--;
				if (mashup.img['pos'] <0) mashup.img['pos'] = mashup.img.length-1;
				break;
			default: return;
		}
		src = mashup.img[mashup.img['pos']].href;
		var titre = mashup.img[mashup.img['pos']].title;
		// Lien suivant
		var prevnext = "<div class='prevnext'>"+(mashup.img['pos']+1)+"/"+mashup.img.length
			+ "<a class='prev' href='javascript:$.jqDialog.action(\"prev\")'> </a>"
			+ "<a class='next' href='javascript:$.jqDialog.action(\"next\")'> </a>"
			+ "</div>";
		//
		jQuery.geoportail.unselectAll();
		$.jqDialog.wait();
		// Precharger l'image
		var imgPreloader = new Image();
		imgPreloader.onload = function()
		{	// Afficher l'image dans un jqDialog
			imgPreloader.onload = null;
			jQuery.jqDialog("",
				{	dialog: "<a href='javascript:$.jqDialog.action(\"next\")'><img src='"+this.src+"' width='100%'/></a>"+prevnext+"<div class='legende'>"+this.title+"</div>",
					classe:'viewer',
					width: this.width,
					height: this.height,
					clickout:true,
					clickin:false,
					undo: false,
					speed:'fast',
					ok: false,
					callback: mashup.diaporama
				});
		}
		imgPreloader.title = titre;
		imgPreloader.src = src;
	},

	/** Rediriger les liens dans la fenetre */
	redirect : function(e)
	{	var obj, id, i;
		// Objet SPIP ?
		var l = ['rubrique', 'article', 'mot', 'auteur'];
		for (i=0; i<l.length; i++)
		{	var rex = new RegExp(".*\\?"+l[i]+"(\\d*).*");
			if (id = Number(this.search.replace(rex,"$1"))) 
			{	obj = l[i];
				break;
			}
		}
		if (id)
		{	mashup.showObject (obj, id);
			e.preventDefault();
		}
		// Lien de pagination
		else if (this.className == "lien_pagination")
		{	var rex = new RegExp(".*debut_(.*)=(\\d*).*");
			var debut = this.search.replace(rex,"$1=$2"); 
			mashup.showObject (this.info.obj, this.info.id, debut);
			e.preventDefault();
		}
		// Image ?
		// if ( this.href.match(/([^\/\\]+)\.(gif|jpg|png)$/i) )
		else if (typeof(TB_show) != 'function' && this.type.match(/^image\.*/i) )
		{	mashup.diaporama ("src", this.href);
			e.preventDefault();
		}
		// Pas de vrai lien
		else if (!this.href)
		{	// C'est le cas des notations... on ne fait rien
		}
		// Masquer la fenetre
		else $.jqDialog.action('undo');
	},

	/** Gestion des popup : on redirige les liens dans la fenetre */
	popup :function(feature, pos, hover)
	{	// Afficher directement sans passer par un popup 
		if (!hover && mashup.options.no_popup)
		{	mashup.showObject (feature.attributes.classe, feature.attributes.id);
			return;
		}
		// Relancer la fonction standard de SPIP-Geoportail
		var p = feature.attributes.popup;
		feature.attributes.popup = null;
		$.geoportail.popupFeature(feature, pos, hover);
		feature.attributes.popup = p;
		// Rediriger les liens du popup
		$("#popup a").click(mashup.redirect);
		return;
	},
	
	/** Ajouter des objets dans un layer */
	addFeatures : function(map, layer, t)
	{	var i;
		for (i=0; i<t.length; i++)
		{	var feature;
			if (t[i].mot) 
			{	feature = jQuery.geoportail.createFeature (map,t[i].lon,t[i].lat);
				feature.attributes.mot_cle = "mot"+t[i].mot;
			}
			else 
			{	feature = jQuery.geoportail.createFeature (map,t[i].lon,t[i].lat, t[i].img, mashup.options.largeur, null, t[i].align);
				feature.attributes.mot_cle = "";
			}
			//feature.attributes.img = t[i].img;
			feature.attributes.name = t[i].titre;
			feature.attributes.description = t[i].description;
			feature.attributes.classe = t[i].objet;
			feature.attributes.id = t[i].id;
			feature.attributes.url = t[i].url;
			switch (t[i].objet)
			{	// Gestion des documents
				case 'document':
				{	feature.attributes.extension = t[i].url.substr(t[i].url.length-3, t[i].url.length).toLowerCase();
					switch (feature.attributes.extension)
					{	case 'kml':
						case 'gpx':
							continue;
						break;
						default:
							feature.attributes.logo = t[i].logo;
							feature.attributes.img = t[i].img;
							feature.attributes.width = t[i].width;
							feature.attributes.height = t[i].height;
							/* Afficher l'image directement sans passer par un popup */
							feature.attributes.popup = function(feature, pos, hover)
							{	if (hover)
								{	mashup.popup (feature, pos, hover);
									return;
								}
								else
								{	// Plugin ThickBox2 ?
									if (typeof(TB_show) == 'function') 
									{	TB_show (feature.attributes.titre?feature.attributes.titre:feature.attributes.name,feature.attributes.url)
									}
									// Fonction standard du plugin Geoportail
									else 
									{	$.geoportail.afficheImage(feature.attributes.url, feature.attributes.titre?feature.attributes.titre:feature.attributes.name);
									}
									// Deselectionner
									$.geoportail.unselectAll();
								}
							}
						break;
					}
					break;
				}
				case 'rubrique':
				case 'article':
				{	feature.attributes.url = "javascript:mashup.showObject(\""+t[i].objet+"\","+t[i].id+");";
					feature.attributes.description += "<p class='savoirplus'>";
					feature.attributes.description += "<a class='centrer' href='javascript:mashup.center("+t[i].lon+","+t[i].lat+","+t[i].zoom+")'>"+mashup._T['voir']+"</a><br/>";
					feature.attributes.description += "<a class='lien' href='"+feature.attributes.url+"'>"+mashup._T['plus']+"</a></p>";
					feature.attributes.logo = t[i].logo;
					// Gestion des popup
					feature.attributes.popup = mashup.popup;
					break;
				}
				default:
				{	feature.attributes.logo = t[i].logo;
					// Gestion du popup
					feature.attributes.popup = mashup.popup;
					break;
				} 
			}
			// Ne pas afficher les rubriques sans description 
			if (!t[i].description && t[i].objet=='rubrique') continue;
			// Ajouter l'objet au calque
			if (!mashup.options.zoom_pere || (t[i].objet == 'document' && mashup.options.zoom_img)) 
			{	// Rechercher le layer correspondant
				var l = mashup.getLayer(map, t[i].zoom);
				l.addFeatures(feature);
			}
			// Ajouter au layer du pere (meme zoom)
			else layer.addFeatures(feature);
			// Ajouter les document en fond (toujours visibles)
			if (mashup.options.backLayer && t[i].objet == 'document')
			{	var l = mashup._backlayer
				var fback = jQuery.geoportail.createFeature (map,t[i].lon,t[i].lat);
				fback.attributes = feature.attributes;
				fback.style = OpenLayers.Util.extend({}, l.styleMap.styles['default'].defaultStyle);
				// Centrer
				var s = -1 * fback.style.pointRadius;
				fback.style.graphicXOffset = fback.style.graphicYOffset = (s?s:-5);
				// Alignement
				var align = t[i].align.split('-');
				for (k = 0; k < align.length; k++) switch (align[k]) 
				{	case 'left': fback.style.graphicXOffset = 0; break;
					case 'right': fback.style.graphicXOffset *= 2; break;
					case 'top': fback.style.graphicYOffset = 0; break;
					case 'bottom': fback.style.graphicYOffset *= 2; break;
					default: break;
				}
				l.addFeatures(fback);
			}
			// Ajouter les documents des articles
			if (t[i].objet == 'article') 
				mashup.addFeatures (map, layer, t[i]['documents']);		
		}
	},

	// Chargement des documents GPK, KML
	addDocument : function(map, t, zoom)
	{	var i;
		// Lien vers l'objet contenant le document
		var lien = t.url;
		//
		for(i=0; i<t.documents.length; i++) 
		{	var url = t.documents[i].url;
			var ext = url.substr(url.length-3, url.length).toUpperCase();
			if (ext == 'GPX' || ext == 'KML')
			{	var obj, id;
				// Lien personnalise
				if (t.documents[i].lien.objet) 
				{	obj = t.documents[i].lien.objet;
					id = t.documents[i].lien.id;
				}
				// ou renvoie sur le pere
				else
				{	obj = t.objet;
					id = t.id;
				}
				// Herite du zoom du pere ?
				if (!mashup.options.zoom_pere) zoom = t.documents[i].zoom;
				// Ajouter un layer
				var opts = { maxResolution:map.getMap().resolutions[zoom], minZoomLevel:zoom, info:{objet:obj, id:id, description:t.documents[i].description, logo:t.documents[i].logo, img:t.logo} };
				var l = $.geoportail.addLayer($.geoportail.getCarte(0), ext, 0, 0, url, true, opts);
				// [BUG] avec GMAP !
				if (!l.minResolution) 
				{	l.minResolution = map.getMap().resolutions[map.getMap().resolutions.length-1];
					l.redraw();
				}
				this._layers[this._layers.length] = l;
			}
			else this._scales[t.documents[i].zoom] = true;
		}
	},

	loadDocument : function(map, contenu, zoom)
	{	var i,j;
		// Documents de la rubrique
		this.addDocument (map, contenu, zoom);
		this._scales[zoom] = true;
		// Documents des articles
		for (j=0; j<contenu.articles.length; j++) 
		{	this._scales[contenu.articles[j].zoom] = true;
			this.addDocument (map, contenu.articles[j], zoom);
		}
		// Documents des sous-rubriques
		for (i=0; i<contenu.rubriques.length; i++) 
		{	this.loadDocument (map, contenu.rubriques[i], contenu.rubriques[i].zoom);
		}
	},

	/** Chercher un layer pour le mashup. */
	getLayer : function(map, zoom)
	{	var l = this._layers[zoom];
		if (!l) return this._layers[0];
		else return l;
	},
	
	/** Creer un layer pour le mashup */
	createLayer : function(map, styleMap, zoom)
	{	// Creer un nouveau layer
		var l = new OpenLayers.Layer.Vector
		(	"mashup_"+zoom,
			{	styleMap: styleMap, 
				opacity:1, 
				visibility:1, 
				maxResolution:map.getMap().resolutions[zoom],
				minZoomLevel:zoom,
				view:{ zoomToExtent:1 }
			}
		);
		map.getMap().addLayer(l);
		// [BUG] avec GMAP !
		if (!l.minResolution) 
		{	l.minResolution = map.getMap().resolutions[map.getMap().resolutions.length-1];
			l.redraw();
		}
		return l;
	},
	
	/** Masher une rubrique */
	loadRubrique : function(map, contenu, zoom)
	{	// Contenu de la rubrique
		var l = mashup.getLayer (map, zoom);
		this.addFeatures (map, l, contenu['articles']);
		this.addFeatures (map, l, contenu['rubriques']);
		this.addFeatures (map, l, contenu['documents']);
		
		// Contenu des sous-rubriques
		var i;
		for (i=0; i<contenu.rubriques.length; i++) 
		{	this.loadRubrique(map, contenu.rubriques[i], contenu.rubriques[i].zoom);
		}
	},
	
	/** Charger un contenu sur la carte */
	loadContenu : function(msg, status)
	{	var contenu;
		if (!msg) 
		{	alert ("Impossible de charger la page !\n"+status);
			return;
		}
		if (typeof(msg)=="object") contenu = msg;
		else eval ("contenu = " + msg);
		mashup.doc = contenu;
		// Logo pour les couches (celui de la rubrique)
		$.geoportail.setOriginator (mashup.doc.img, "javascript:mashup.showObject('rubrique',"+mashup.doc.id+")");
		
		// Patience
		$("#jqDialog .jqDialogInner").text("Chargement des documents...");
		setTimeout ("mashup.delayLoadDoc()",10);
	},
	
	// Chargement des documents GPX, KML (en fond de carte)
	delayLoadDoc : function()
	{	this._layers = new Array();
		this.loadDocument (map0, this.doc, 0);
		$("#jqDialog .jqDialogInner").text("Initialisation des calques...");
		setTimeout ("mashup.delayLoadLayers()",10);
	},
			
	// Declaration des couches en fonction des echelles
	delayLoadLayers : function()
	{	var nlayers = new Array();
		var i, j, l;
		var layers = this._layers;

		// Definition des styles pour l'affichage sous forme de punaises
		var symbolizer = OpenLayers.Util.applyDefaults(
				{ pointRadius: 10, graphicXOffset: -2, graphicYOffset: -17, cursor:'pointer' },
				OpenLayers.Feature.Vector.style["default"]);
		var styleMap = new OpenLayers.StyleMap({"default": symbolizer, "select": {pointRadius: 15, graphicXOffset: -4, graphicYOffset: -26}});
		
		// Definition d'une legende
		if (this.doc.legende.length)
		{	var lookup = new Array();
			var lookup2 = new Array();
			var legend ="";
			lookup[""] = styleMap.styles['default'];
			lookup2[""] = styleMap.styles['select'];
			var l = mashup.options.largeur_mot;
			for (i=0; i<this.doc.legende.length; i++) 
			{	lookup['mot'+this.doc.legende[i].id] = { pointRadius: l, externalGraphic:this.doc.legende[i].img, graphicXOffset: -l, graphicYOffset: -l };
				lookup2['mot'+this.doc.legende[i].id] = { pointRadius: l+3, externalGraphic:this.doc.legende[i].img, graphicXOffset: -l-3, graphicYOffset: -l-3 };
				legend += "<li><img src='"+mashup.doc.legende[i].img+"'>"+mashup.doc.legende[i].titre+"</li>"
			}
			styleMap.addUniqueValueRules("default", "mot_cle", lookup);
			styleMap.addUniqueValueRules("select", "mot_cle", lookup2);
		}
		
		// Layer de fond
		if (mashup.options.backLayer) 
		{	var sm = new OpenLayers.StyleMap(
				{	"default": { pointRadius:5, externalGraphic:mashup.graphic, graphicXOffset:0, graphicYOffset:0, cursor:'pointer' }, 
					"select": { pointRadius:7 }
				});
			mashup._backlayer = layers[layers.length] = mashup.createLayer (map0, sm, "back");
		}
		// Creation d'un layer par echelle
		mashup._layers = new Array();
		for (i=0; i<=mashup._scales.length; i++) if (mashup._scales[i]) mashup._layers[i] = layers[layers.length] = mashup.createLayer (map0, styleMap, i);
		map0.selectionnable (layers);
		
		// Inserer dans un layer unique
		if (legend) legend = "<ul class='legende'>"+legend+"</ul>"
		l = new Geoportal.Layer.Aggregate (mashup.doc.titre, layers, 
			{	opacity:1, 
				visibility:1, 
				displayInLayerSwitcher:true,
				description: mashup.doc.logo + mashup.doc.description.replace('&gt;','>').replace('&lt;','<') + legend,
				originators: $.geoportail.originators,
				view:{ zoomToExtent:1 }
			});
		map0.getMap().addLayer ( l );

		$("#jqDialog .jqDialogInner").text("Chargement des rubriques...");
		setTimeout ("mashup.delayLoadRubrique()",10);
	},
	
	// Chargement des rubriques et de leur contenu
	delayLoadRubrique : function()
	{	mashup.loadRubrique (map0, mashup.doc, 0);
		// Fin
		$.jqDialog.close();
	}
};

/** Gestion de l'affichage plein ecran */
jQuery(window).bind('resize', mashup.resizeMap);