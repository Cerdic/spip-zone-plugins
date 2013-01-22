/**
	Classe de calcul d'un profil
	
- div : la div du profil
- options :
	- zmin, zmax : Z min et max sur l'echelle
	- amplitude : amplitude du graphique (en Z)
	- graduation : pas des graduations
	- box : top,left,bottom,right
*/
jQuery.geoportail.elevation = function (div, options)
{	var self = this;

	// Rendu pour la carte
	if (OpenLayers && OpenLayers.Renderer)
	{	if (!OpenLayers.Renderer.scale) OpenLayers.Renderer.scale={};
		OpenLayers.Renderer.symbol.poi_ = [ 0,-300, -116,-275, -212,-212, -275,-116, -300,0, -275,116, -212,213, -164,244, 0,700, 3,1400, 0,700, 164,244, 213,213, 275,116, 300,0, 275,-116, 213,-212, 116,-275, 0,-300, 0,-200, 78,-184, 141,-141, 184,-78, 200,0, 184,78, 141,141, 78,184, 0,200, 0,100, 38,91, 72,72, 91,38, 100,0, 91,-37, 72,-72, 38,-91, 0,-100, -37,-91, -72,-72, -91,-37, -100,0, -91,38, -72,72, -37,91, 0,100, 0,200, -78,184, -141,141, -184,78, -200,0, -184,-78, -141,-141, -78,-184, 0,-200, 0,-300 ];
		OpenLayers.Renderer.scale.poi_ = 2;
		OpenLayers.Renderer.symbol.auto_ = [ 0,9, 1,9, 1,11, 2,12, 3,12, 4,11, 4,9, 11,9, 11,11, 12,12, 13,12, 14,11, 14,9, 15,9, 15,6, 14,6, 14,7, 12,7, 12,6, 15,6, 15,5, 14,4, 13,0, 2,0, 1,4, 2,4, 3,1, 12,1, 13,4, 1,4, 0,5, 0,6, 3,6, 3,7, 1,7, 1,6, 0,6, 0,9 ];
		OpenLayers.Renderer.scale.auto_ = 1;
		OpenLayers.Renderer.symbol.velo_ = [ -1,-14, 2,-14, 4,-13, 3,-12, 3,-10, 2,-9, 1,-9, 1,-8, 2,-7, 4,-4, 9,-2, 10,-1, 6,-1, 1,-3, -1,-2, 2,0, 3,1, 6,-1, 10,-1, 9,0, 7,1, 8,2, 11,2, 12,3, 8,3, 8,4, 8,5, 9,9, 10,9, 10,11, 8,11, 8,9, 7,5, 6,5, 4,8, 4,12, 7,15, 11,15, 14,12, 14,8, 11,5, 8,5, 8,4, 12,4, 15,7, 15,13, 12,16, 6,16, 3,13, 3,7, 5,4, 6,4, 6,2, 5,2, 0,11, 2,13, -1,14, -3,13, -3,11, -2,10, -4,10, -4,13, -7,16, -13,16, -16,13, -16,7, -13,4, -7,4, -6,3, -14,3, -12,2, -5,2, -3,2, -2,3, -3,3, -4,4, -5,4, -6,5, -4,7, -4,9, -5,9, -5,8, -8,5, -12,5, -15,8, -15,12, -12,15, -8,15, -5,12, -5,10, -9,10, -9,11, -11,11, -11,9, -10,9, -8,5, -7,6, -9,9, -3,9, -4,7, -4,6, -1,4, -3,2, -5,2, -6,1, -6,-3, -3,-8, -1,-9, -2,-10, -2,-13, -1,-14 ]
		OpenLayers.Renderer.scale.velo_ = 1.5;
		OpenLayers.Renderer.symbol.rando_ = [ 1,-14, 2,-15, 5,-15, 7,-14, 6,-13, 6,-11, 5,-10, 4,-10, 4,-9, 5,-8, 4,-5, 2,-4, 2,-2, 6,2, 7,8, 9,9, 6,10, 4,9, 4,3, 0,0, -1,4, -5,8, -4,10, -6,10, -8,8, -8,6, -3,3, -3,-2, -2,-4, -4,-5, -4,-9, -2,-11, 0,-10, 2,-10, 1,-11, 1,-14 ]
		OpenLayers.Renderer.scale.rando_ = 1.5;
		OpenLayers.Renderer.symbol.rando2_ = [ 0,-13, 1,-14, 4,-14, 6,-13, 5,-12, 5,-10, 4,-9, 3,-9, 3,-8, 3,-5, 7,-5, 8,-4, 7,-3, 10,11, 9,11, 6,-3, 2,-3, 2,-1, 4,3, 6,9, 8,10, 5,11, 3,10, 2,4, 0,1, -1,5, -5,9, -4,11, -6,11, -8,9, -8,7, -3,4, -3,-1, -2,-3, -4,-3, -5,-8, -3,-10, -1,-9, 1,-9, 0,-10, 0,-13 ]
		OpenLayers.Renderer.scale.rando2_ = 1.5;
	}

	// Options de dessin par defaut
	this.options = 
	{	// Pas des graduations
		amplitude : 1000,
		graduation : 250,
		// Echelles des Z (par defaut prendre le Z du fichier)
		zmin : null,
		zmax : null,
		// Associer a une carte
		id_geoportail : null,
		// Centrer la carte ? (mode suivi par defaut)
		centerMap : "S",
		// Symbol sur la carte
		symbol : "poi_",
		// Boite d'affichage
		box : { top:10, bottom:10, left:40, right:10 },
		// Couleur du dessin
		color : "#369"
	
	};
	jQuery.extend (this.options, options);
	if (!this.options.graduation) this.options.graduation = 250;
	if (!this.options.amplitude) this.options.amplitude = 1000;
	if (typeof(this.options.id_geoportail)=='number')
	{	this.geoportail = jQuery.geoportail.getCarte(this.options.id_geoportail);
	}
	this.box = this.options.box;
	this.div = div;
	
	// Carte pour l'affichage
	this.container = jQuery("#"+div+"_map");
	if (!this.container.length) 
	{	jQuery("#"+div).prepend ("<div id='"+div+"_map' class='profilMap'>");
		this.container = jQuery("#"+div+"_map");
	}
	
	this.map = new OpenLayers.Map(div+"_map", { controls: [] });
	// Enregistrer la position
	this.map.events.register("mousemove", this.map, function(e) { self.mouseMove (this.events.getMousePosition(e)); });
	// Style pour l'affichage
	var myStyles = new OpenLayers.StyleMap(
		{	"default": new OpenLayers.Style(
			{	label: "${label}", 
				labelAlign : 'rm',
				fontSize : 10,
				fillColor: "#ccc",
				strokeColor: "${color}",
				strokeDashstyle: "${dash}",
				pointRadius : 0,
				strokeWidth: 1
            },
            {	context:
				{	label: function (feature)
					{	if (feature.attributes.label == "0") return "O";
						if (feature.attributes.label) return feature.attributes.label;
						return "";
					},
					color: function (feature)
					{	return (feature.attributes.color ? feature.attributes.color : "Black");
					},
					dash: function (feature)
					{	return (feature.attributes.dash ? feature.attributes.dash : "solid");
					}
				}
            }),
            "select": new OpenLayers.Style({strokeWidth:2})
		});
	this.layer = new OpenLayers.Layer.Vector("Profil", { isBaseLayer: true, 'displayInLayerSwitcher': true, styleMap: myStyles });
	this.map.addLayers([this.layer]);
	this.map.setCenter(new OpenLayers.LonLat(0,0),8);
     
    /** Gestion de la souris sur le profil */
    this.mouseMove = function(pos)
    {	if (!this.trk) return;
		// Masquer le curseur
		if (this.mrk) 
		{	this.layer.removeFeatures([this.mrk]);
			this.mrk=null;
		}
		if (pos.x<this.box.left || pos.x>this.container.width()-this.box.right || pos.y<this.box.top || pos.y>this.container.height()-this.box.bottom) 
		{	jQuery("#"+this.div+" .info_profil").show();
			jQuery("#"+this.div+" .info_trace").hide();
			if (this.geopLayer) this.geopLayer.setVisibility(false);
		}
		else
		{	jQuery("#"+this.div+" .info_profil").hide();
			jQuery("#"+this.div+" .info_trace").show();
			var elev = this.trk.px[Math.round(pos.x - this.box.left)];
			if (elev)
			{	var d = "#"+this.div+" .info_trace ";
				jQuery(d+".dist").html(Math.round(elev.d/100)/10+" km");
				jQuery(d+".zmax").html(elev.z+" m");
				var t = this.interval(this.trk.t0, elev.t);
				if (t.m<10) t.m = "0"+t.m;
				jQuery(d+".temps").html(t?(t.h?t.h+"h":"")+t.m+"min":"-");
				// Afficher le curseur
				var l = [ { x:elev.d, y:this.zmin }, { x:elev.d, y:elev.z } ];
				this.mrk = this.drawLine (l,{color:"red"});
				// Affichage sur le geoportail
				if (this.geoportail && this.geoportail.map && this.geoportail.map.map)
				{	var map = this.geoportail.map.getMap();
					var f = jQuery.geoportail.createFeature (this.geoportail.map, elev.pt.x,elev.pt.y);
					// Centrer la carte 
					if (this.options.centerMap == "1") map.setCenterAtLonLat(elev.pt.x,elev.pt.y);
					// seulement si hors zone
					else if (this.options.centerMap == "S" && !map.getExtent().contains(f.geometry.x, f.geometry.y)) map.setCenterAtLonLat(elev.pt.x,elev.pt.y);
					// Afficher dans une couche
					if (!this.geopLayer)
					{	var styleMap = new OpenLayers.StyleMap(
							{	"default": new OpenLayers.Style({ pointRadius:(OpenLayers.Renderer.scale[this.options.symbol] ? 10*OpenLayers.Renderer.scale[this.options.symbol]:20), strokeWidth:0, fillColor:this.options.color, graphicName:this.options.symbol }),
								"select": new OpenLayers.Style({ pointRadius: 25, strokeWidth:0, fillColor:this.options.color, graphicName:this.options.symbol })
							});
						// Rajoute une couche pour les points
						this.geopLayer = new OpenLayers.Layer.Vector("profil", { styleMap: styleMap, opacity: 1, visibility: 1, displayInLayerSwitcher:false });
						map.addLayer(this.geopLayer);
						this.geoportail.map.selectionnable(this.geopLayer);
					}
					this.geopLayer.setVisibility(true);
					this.geopLayer.removeAllFeatures();
					this.geopLayer.addFeatures([f]);
				}
			}
		}
    };
    
	/** Affichage d'une ligne */
	this.drawLine = function(pts, style)
	{	var	i;
		var line = new Array();
		var scx = (this.p1.lon-this.p0.lon) / this.dmax;
		var scy = (this.p1.lat-this.p0.lat) / (this.zmax-this.zmin);
		for (i=0; i<pts.length; i++)
		{	line.push (new OpenLayers.Geometry.Point(
				this.p0.lon + scx * pts[i].x, 
				this.p0.lat + scy * (pts[i].y-this.zmin)
			));
		}
		var f = new OpenLayers.Feature.Vector( new OpenLayers.Geometry.LineString(line) );
		if (style) f.attributes.color = style.color;
		this.layer.addFeatures([f]);
		return f;
	};
	
	/** Affichage d'une ligne en coord pixel */
	this.drawLinePx =function(pts, style)
	{	line = new Array();
		for (var i=0; i<pts.length; i++)
		{	pt = this.map.getLonLatFromPixel( new OpenLayers.Pixel(pts[i].x, pts[i].y) );
			line.push(new OpenLayers.Geometry.Point(pt.lon,pt.lat));
		}
		f = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(line));
		if (style) f.attributes.dash = style.dash;
		this.layer.addFeatures([f]);
	};
	
	/** Affichage du repere */
	this.drawRepere = function(zmin, zmax, dmax)
	{	// Definition du repere
		this.p0 = new OpenLayers.Pixel(this.box.left, this.container.height()-this.box.bottom);
		this.p0 = this.map.getLonLatFromPixel(this.p0);
		this.p1 = new OpenLayers.Pixel(this.container.width()-this.box.right,this.box.top);
		this.p1 = this.map.getLonLatFromPixel(this.p1);
		this.dmax = dmax;
		// Calcul des bornes
		this.options.zmin = parseFloat(this.options.zmin);
		this.options.zmax = parseFloat(this.options.zmax);
		if (isFinite(this.options.zmin))
		{	this.zmin = this.options.zmin;
			if (isFinite(this.options.zmax)) this.zmax = this.options.zmax;
			else
			{	this.zmax = Math.max (this.zmin + this.options.amplitude, Math.ceil(zmax/this.options.graduation)*this.options.graduation);
			}
		}
		else if (isFinite(this.options.zmax))
		{	this.zmax = this.options.zmax;
			this.zmin = Math.max (0, this.zmax - this.options.amplitude);
			this.zmin = Math.min (this.zmin, Math.floor(zmin/this.options.graduation)*this.options.graduation);
		}
		else
		{	this.zmin = Math.floor(zmin/this.options.amplitude*2)*this.options.amplitude/2;
			this.zmax = Math.ceil(zmax/this.options.amplitude*2)*this.options.amplitude/2;
			if (this.zmax-this.zmin < this.options.amplitude) this.zmin = Math.max (0,this.zmax -this.options.amplitude);
			if (this.zmax-this.zmin < this.options.amplitude) this.zmax = this.zmin +this.options.amplitude;
		}
		
		/** Dessin du repere */
		this.layer.removeAllFeatures();
		var y, pt, f, line;
		for (var i=this.zmin; i<=this.zmax; i+=this.options.graduation)
		{	y = this.box.bottom + ((this.zmax-i)/(this.zmax-this.zmin)*(this.container.height()-2*this.box.bottom));
			pt = this.map.getLonLatFromPixel( new OpenLayers.Pixel(this.box.left-6, y) );
			f = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(pt.lon,pt.lat));
			f.attributes.label = String(i);
			this.layer.addFeatures([f]);
			this.drawLinePx ([ {x:this.box.left-2,y:y}, {x:this.container.width()-this.box.right, y:y} ], { dash:(i>this.zmin ? "dot":"solid") });
		}
		this.drawLinePx ([ {x:this.box.left, y:this.box.top-5}, {x:this.box.left, y:this.container.height()-this.box.bottom} ]);
	};
	
	/** Affichage d'une trace */
	this.drawTrk = function (trk)
	{	var i;
		this.drawRepere(trk.zmin, trk.zmax, trk.dmax);
		var l = new Array();
		for (i=0; i<trk.pts.length; i++) l.push( { x:trk.pts[i].d, y:trk.pts[i].z } );
		this.drawLine(l, { color:this.options.color });
	}

	/** Calcul de grand cercle (en m) */
	this.gdCercle = function (lon1,lat1,lon2,lat2)
	{	var R = 6371000; // Rayon de la terre
		var toRad = Math.PI/180;
		
		var dLat = (lat2-lat1) * toRad;
		var dLon = (lon2-lon1) * toRad;
		var lat1 = lat1 * toRad;
		var lat2 = lat2 * toRad;

		var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
				
		return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
	}
	
	/** Extraction d'une trace dans un GPX */
	this.extractTrk = function (format, segment, node)
	{	var points = format.getElementsByTagNameNS(segment, segment.namespaceURI, node);
        var point_features = [];
        var dmax = 0;
        var zmax=null, zmin=1000000000;
        var len = points.length;
        for (var i=0; i < len; i++) 
        {	var z = format.getElementsByTagNameNS(points[i], points[i].namespaceURI, 'ele');
			z = z[0] ? Number(format.getChildValue(z[0],null)):null;
			var t = format.getElementsByTagNameNS(points[i], points[i].namespaceURI, 'time');
			t = t[0] ? format.getChildValue(t[0],null):null;
			zmin = Math.min (zmin,z);
			zmax = Math.max (zmax,z);
			var lon = Number(points[i].getAttribute("lon"));
			var lat = Number(points[i].getAttribute("lat"));
			var pt = new OpenLayers.Geometry.Point(lon,lat);
            point_features.push({'pt':pt,'z':z, 't':t, 'lon':lon, 'lat':lat});
            if (i==0) point_features[i]['d']=0;
            else 
            {	dmax += this.gdCercle (lon,lat, point_features[i-1].lon, point_features[i-1].lat);
				point_features[i]['d'] = dmax;
            }
        }
        return { 'pts':point_features, 'zmin':zmin, 'zmax':zmax, 'dmax':dmax, 't0':point_features[0].t, 't1':point_features[point_features.length-1].t };
    };

	// Transfo temps GPX -> temps JS
	this.date = function(t)
	{	if (!t) return null;
		t = t.split("T")
		// date
		var d = t[0];
		d = d.split("-");
		// time
		t = t[1].split("Z");
		t = t[0].split (":");
		// date JS
		var date = new Date();
		date.setDate(Number(d[2]));
		date.setMonth(Number(d[1]));
		date.setFullYear(Number(d[0]));
		date.setHours(Number(t[0]));
		date.setMinutes(Number(t[1]));
		date.setSeconds(Number(t[2]));
		return date;
	};
	// Calcul un interval de temps 
	this.interval = function(et0,et1)
	{	var t0 = this.date(et0);
		var t1 = this.date(et1);
		if (t0 && t1)
		{	var t = (t1-t0)/1000/3600;
			var h = Math.floor(t);
			var m = Math.round((t-h)*60);
			return { 'h':h, 'm':m };
		}
		return false;
	};

	/** Affichage des info */
	this.info = function (dist,zmin,zmax,t0,t1)
	{	var d = "#"+this.div+' .info_profil';
		jQuery(d+' .dist').html(Math.round(dist/100)/10+" km");
		jQuery(d+' .zmin').html(Math.round(zmin)+"m");
		jQuery(d+' .zmax').html(Math.round(zmax)+"m");
		var t = this.interval(t0, t1);
		if (t.m<10) t.m = "0"+t.m;
		jQuery(d+' .temps').html(t?(t.h?t.h+"h":"")+t.m+"min":"-");
	};
	
	/** Lecture du GPX */
	this.getGPX = function (request)
	{	if (!OpenLayers) return;
		var format = new OpenLayers.Format.XML;

		var doc = format.read (request.responseText);
		var tracks = doc.getElementsByTagName("trk");
		
		var len=tracks.length;
		for (var i=0; i<len; i++) 
		{	var trks = format.getElementsByTagNameNS(tracks[i], tracks[i].namespaceURI, "trkseg");
	        var trk;
			for (var j=0, len = trks.length; j<len; j++) 
			{   trk = this.extractTrk(format, trks[j], "trkpt");
				if (trk.zmax) 
				{	this.trk = trk;
					// Affichage
					this.drawTrk(trk);
					if (this.container) 
					{	this.container.css(
						{	'opacity':this.save.opacity,
							'background-position':this.save.backpos,
							'background-repeat':this.save.backrep,
							'background-image':this.save.backimg
						});
					}
					this.info (trk.dmax, trk.zmin, trk.zmax, trk.t0, trk.t1);
					// Correspondance Pixel
					this.trk.px = new Array();
					var i, k, d;
					for (i=this.box.left; i<this.container.width()-this.box.right; i++)
					{	d = (i - this.box.left) / (this.container.width()-this.box.right-this.box.left) * this.dmax;
						for (k=0; k<this.trk.pts.length-1; k++)
						{	if (this.trk.pts[k].d <= d && this.trk.pts[k+1].d > d) break;
						}
						this.trk.px.push(this.trk.pts[k]);
					}
					this.trk.px[this.container.width()-this.box.right-this.box.left-1] = this.trk.pts[this.trk.pts.length-1];
					return;
				}
			}
		}
	};
	
	/**	Chargement du fichier en structure segment 
	*/
	this.loadGPX = function(fichier)
	{	if (this.container) 
		{	this.save = 
			{	'opacity':this.container.css('opacity'), 
				'backimg':this.container.css('background-image'),
				'backrep':this.container.css('background-repeat'),
				'backpos':this.container.css('background-position') 
			};
			if (!this.save.backpos) 
			{	var bpx = this.container.css('background-position-x');
				var bpy = this.container.css('background-position-y');
				this.save.backpos = (bpx?bpx:"0px")+" "+(bpy?bpy:"0px");
			}
			this.container.css(
			{	'opacity':0.5,
				'background-position':(this.container.width()-20)+"px 5px",
				'background-repeat':"no-repeat",
				'background-image':"url(#CHEMIN{images/searching.gif})"
			});
		}
		/* Passe par un timeout pour explorer 
		(laisser un peu de temps pour afficher les modifs sur le container)
		*/
		var obj = this;
		setTimeout( function()
			{	OpenLayers.Request.GET({
						url: fichier,
						success: obj.getGPX,
						failure: function(request) {},
						scope:obj
				});
			},50);
	};

};
