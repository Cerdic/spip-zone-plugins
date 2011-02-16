/*
 * Copyright 2008 Institut Geographique National France, released under the
 * BSD license.
 */
/*
 * @requires Geoportal.Format.Geoconcept.js
 */
 
// Fonction de chargement
function GeoportailInitFormatCeoconcept()
{
/**
 * Class: Geoportal.Format.Geoconcept.rip
 * Write support for Geoconcept export text files.
 *
 * Inherits from:
 * - <Geoportal.Format.Geoconcept>
 */

Geoportal.Format.Geoconcept.rip = OpenLayers.Class(Geoportal.Format.Geoconcept, 
{
    /**
     * APIProperty: attributs
     * {[String]} Tableau de noms des atributs a exporter
     *		par defaut, seul le nom (name) est exporte.
     */
	attributs:Array(),
	type:null,
	sstype:null,
    /**
     * APIProperty: extractAttributes
     * Bool On charge les attributs
     *		par defaut, seul id, type, sous-type et name sont importes.
     */
	extractAttributes:false,
	
    /**
     * Constructor: OpenLayers.Format.GeoConcept
     * Create a new parser for GeoConcept.
     *
     * Parameters:
     * options - {Object} An optional object whose properties will be set on
     *     this instance.
     */
	initialize: function(options) 
	{ OpenLayers.Format.prototype.initialize.apply(this, [options]);}, 
	

    /**
     * APIMethod: read
     * Return a list of features from a GXT doc
     * Parameters:
     * doc    - {String} data to read/parse.
     * nb     - {Int} Number of feature to read.
     *
     * Returns:
     * An Array of <OpenLayers.Feature.Vector>s
     */
    read: function(doc, nb) 
    {	var features= [];
		var lines = doc.split ("\n");
		// Decode les metadonnees
		var i, il, code, sep, str;
		var syscoord=null, unit=null, format="2", quoted=false, charset=null, angle=null, nc=2;
		il = lines.length;
		for (i=0; i<il; i++)
		{	if (lines[i].charAt(0)!="/") break;
			sep = lines[i].indexOf (" ");
			if (sep<0) code = lines[i].substring(sep).replace("\r","");
			else 
			{	code = lines[i].substring(0,sep);
				str = lines[i].substring(sep).replace("\r","");
			}
			switch (code)
			{	case "//$SYSCOORD": 
					syscoord = Number(str.substring (str.indexOf(":")+1,str.indexOf("}")));
					break;
				case "//$FORMAT" :
					format = str.substring (str.indexOf(":")+1).replace(/ /g,"");
					break;
				case "//$UNIT" :
					if (str.match("Distance")) unit = str.substring (str.indexOf(":")+1).replace(/ /g,"");
					else if (str.match("Angle")) angle = str.substring (str.indexOf(":")+1).replace(/ /g,"");
					break;
				case "//$QUOTED-TEXT" :
					quoted = (str.replace(/"/g,"")=="yes");
					break;
				case "//$CHARSET" :
					charset = str.replace(/ /g,"");
					break;
				case "//$3DOBJECT" :
					nc = 3;
					break;
				case "//$FIELD" :
					break;
				default: break;
			}
		}
		// Projection
		if (syscoord)
		{	il = this.SUPPORTED_CRSS.length;
			for (var i= 0; i<il; i++) 
			{	if ( this.SUPPORTED_CRSS[i].Type == syscoord )
				{	syscoord = this.SUPPORTED_CRSS[i].projCode[0];
					this.externalProjection = new OpenLayers.Projection(syscoord);
					break;
				}
			}
		}
		// Lecture des objets
		il = lines.length;
		for (var i= 0; i<il; i++) 
		{	if (lines[i].charAt(0) == "/") 
			{	// Objet 3D
				if (lines[i].substring(0,11) == "//$3DOBJECT") nc=3;
			}
			else
			{	var data = lines[i].split("\t");
				var feature = this.readFeature (data, nc);
				if (feature) 
				{   // add new features to existing feature list
					features.push(feature);
					if (nb && nb <= features.length) return features;
				} else {
					// TODO
				}
				// Remise a zero
				nv=2;
			}
		}
		return features;
    },


    /**
     * APIMethod: readFeature
     * Return a features from a GXT doc
     * Parameters:
     * data    - Array{String} data to read/parse.
     *
     * Returns:
     * <OpenLayers.Feature.Vector>
     */
	readFeature : function(data, nc)
	{	if (data.length<7) return null;
		var attributes = this.parseAttributes(data);
        var pos = Number(data[4])+5;
        // Geometrie
        var x,y,z=null;
        x = Number(data[pos++]);
        y = Number(data[pos++]);
        if (nc==3) z = Number(data[pos++]);
        // Ponctuel
        if (data.length < pos+1) 
        {   var geometry= new OpenLayers.Geometry.Point(x,y,z);
            if (this.externalProjection && this.internalProjection) 
            {   geometry.transform(this.externalProjection,
                                   this.internalProjection);
            }
            return new OpenLayers.Feature.Vector(geometry, attributes);
        }
        else 
        {	var isline=true;
			nb = Number(data[pos+2]);
			// Lineaire : il faut sauter le dernier point...
			if (nb*nc == data.length-pos-3) pos += 3;
			else
			{	nb = Number(data[pos++]);
				isline=false;
			}
			// Lecture des objets
			var points = [];
			points.push(new OpenLayers.Geometry.Point(x, y, z));
			points = this.readComponent (data, points, pos, nb, nc)
			var geometry;
			if (isline) geometry = new OpenLayers.Geometry.LineString(points);
			else 
			{	var components = [];
				var ring = new OpenLayers.Geometry.LinearRing(points);
				components.push(ring);
				// TODO : lire les autres composantes...
				geometry = new OpenLayers.Geometry.Polygon(components);
			}
			// Transform
            if (this.externalProjection && this.internalProjection) 
            {   geometry.transform(this.externalProjection,
                                   this.internalProjection);
            }
			// Feature
            return new OpenLayers.Feature.Vector(geometry, attributes);
        }
	},
	
    /**
     * Method: parseAttributes
     *
     * Parameters:
     * node - {<DOMElement>}
     *
     * Returns:
     * {Object} An attributes object.
     */
    parseAttributes: function(data) 
    {	var attributes = {'id':data[0], 'type':data[1], 'sous-type':data[2], 'name':data[3] };
        // alert (data[0]+' type '+data[1]);
		var i, il;
        var nb = Number(data[4]);
        il = Math.min(data.length, nb);
        // Les autres attributs
        if (this.extractAttributes)
		{	for (i=0; i<il; i++)
			{	if (this.attributs.lenght<i) attributes[this.attributs[i]] = data[5+i];
				else attributes['att'+i] = data[5+i];
			}
		}
		return attributes;
    },

    /**
     * APIMethod: readComponent
     * Return a features from a GXT doc
     * Parameters:
     * data    - Array{String} data to read/parse.
     * points   - Array of <OpenLayers.Geometry.Point>.
     * pos     - start.
     * nb	   - nb points.
     * nc	   - nb coord (2=X,Y - 3=X,Y,Z).
     *
     * Returns:
     * Array of <OpenLayers.Geometry.Point>
     */
	readComponent : function(data, points, pos, nb, nc)
	{	var x,y,z;
		var il = pos+nc*nb;
		if (il>data.length) return points;
		for (i=pos; i<il; i+=nc)
		{	x = Number(data[i]);
			y = Number(data[i+1]);
			if (nc==3) z = Number(data[i+2]);
			points.push(new OpenLayers.Geometry.Point(x, y, z));
		}
		return points;
	},

    /**
     * Method: writeFeature
     * Create a Geoconcept export string representing the given feature.
     *
     * Parameters:
     * feature - {<OpenLayers.Feature.Vector>}
     *
     * Returns:
     * {String}
     */
    writeFeature : function(feature) {
        var gxt= '';
        // identifiant
		gxt += '-1\t';
        // type (nom du layer ou de la classe)
        if (this.type || !feature.layer)
			gxt += this.type? this.type : feature.CLASS_NAME;
		else gxt += feature.layer.name? feature.layer.name : feature.CLASS_NAME;
        gxt += '\t';
        // sstype (ou nom de la classe geometrique)
        if (this.sstype) gxt += this.sstype + '\t';
        else gxt += feature.geometry.CLASS_NAME.substring(feature.geometry.CLASS_NAME.lastIndexOf(".")+1) + '\t';
        // nom (attriubt name)
		gxt += (feature.attributes['name'] ? unescape(feature.attributes['name']) : '');
        gxt += '\t';
		// les attributs (suivant la liste passee au constructeur)
        gxt += this.attributs.length +'\t';
        for(var i=0; i<this.attributs.length; i++) 
			gxt += (feature.attributes[this.attributs[i]] ? unescape(feature.attributes[this.attributs[i]]) : '') +'\t';
        // la geometrie
        gxt += this.writeGeometry(feature.geometry);
        // fin
        return gxt + '\n';
    },

    /**
     * Property: buildGeometry
     * Object containing methods to do the actual geometry building
     *     based on geometry type.
     */
    buildGeometry : {
        /**
         * Method: buildGeometry.point
         * Given an OpenLayers point geometry, create a Geoconcept point.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.Point>} A point geometry.
         *
         * Returns:
         * {String}
         */
        point : function(geometry) {
            return this.buildCoordinates(geometry);
        },

        /**
         * Method: buildGeometry.multipoint
         * Given an OpenLayers multipoint geometry, create a Geoconcept multipoint.
         * Not supported.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.MultiPoint>} A multipoint geometry.
         *
         * Returns:
         * {String}
         */
        multipoint : function(geometry) {
            return null;
        },

        /**
         * Method: buildGeometry.linestring
         * Given an OpenLayers linestring geometry, create a Geoconcept linestring.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.LineString>} A linestring geometry.
         *
         * Returns:
         * {String}
         */
        linestring : function(geometry) {
            return this.buildCoordinates(geometry);
        },

        /**
         * Method: buildGeometry.linearring
         * Given an OpenLayers linearring geometry, create a Geoconcept linearring.
         *      Not supported.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.LinearRing>} A linearring geometry.
         *
         * Returns:
         * {String}
         */
        linearring : function(geometry) {
            return this.buildCoordinates(geometry, true);
        },

        /**
         * Method: buildGeometry.multilinestring
         * Given an OpenLayers multilinestring geometry, create a Geoconcept
         *     multilinestring.
         *     Not supported.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.MultiLineString>} A multilinestring
         *     geometry.
         *
         * Returns:
         * {String}
         */
        multilinestring : function(geometry) {
            return null;
        },

        /**
         * Method: buildGeometry.polygon
         * Given an OpenLayers polygon geometry, create a Geoconcept polygon.
         *      Not supported.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.Polygon>} A polygon geometry.
         *
         * Returns:
         * {String}
         */
        polygon : function(geometry) {
            var gxt='';
            var rings= geometry.components;
            for(var i= 0, il= rings.length; i<il; ++i) {
                if (i>0) {
                    gxt += '\t';
                }
                gxt= this.buildCoordinates(rings[i], true);
                // inner rings
                if (i==0) {
                    gxt += '\t' + (il-1) + '\t';
                }
            }
            return gxt;
        },

        /**
         * Method: buildGeometry.multipolygon
         * Given an OpenLayers multipolygon geometry, create a Geoconcept multipolygon.
         *      Not supported.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry.MultiPolygon>} A multipolygon
         *     geometry.
         *
         * Returns:
         * {String}
         */
        multipolygon : function(geometry) {
            var gxt = '';
            for (var i= 0, il= geometry.components.length; i<il; i++) {
                if (i>0) {
                    gxt += '\t';
                }
                var className = geometry.componentTypes[i];
                var type = className.substring(className.lastIndexOf(".") + 1);
                var builder = this.buildGeometry[type.toLowerCase()];
                gxt += builder.apply(this, [geometry.components[i]]);
                if (i==0) {
                    gxt += '\t' + (il-1) + '\t';
                }
            }
            return gxt;
        }
    },
	
    /**
     * Method: buildCoordinates ! Surchage de la fonction buggee !
     *
     * Parameters:
     * geometry - {<OpenLayers.Geometry>}
     * isPolygon - {Boolean}
     *
     * Returns:
     * {String}
     */
    buildCoordinates : function(geometry, isPolygon) 
    {   var gxt;
        var points= geometry.components;
        // LineString or LinearRing
        if (points) 
        {   var nb = points.length;
			if (isPolygon) nb--;
            // 1st point
            gxt= points[0].x + '\t' + points[0].y + '\t';
            // last point
            if (!isPolygon) gxt += points[nb-1].x + '\t' + points[nb-1].y + '\t';
            // number of remaining points
            gxt += (nb-1);
            // other points
            for (var i= 1; i<nb; i++) 
                gxt += '\t' + points[i].x + '\t' + points[i].y;
        } 
        else 
        {   gxt= geometry.x + '\t' + geometry.y;
        }
        return gxt;
    },

	 /**
     * Constant: CLASS_NAME
     * {String} *"Geoportal.Format.Geoconcept.rip"*
     */
    CLASS_NAME : "Geoportal.Format.Geoconcept.rip"
});

}