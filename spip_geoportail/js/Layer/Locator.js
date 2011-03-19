/*
 * Copyright 2008 Institut Geographique National France, released under the
 * BSD license.
 */
/*
 * @requires OpenLayers.Layer.Vector
 */
 
// Fonction de chargement
function GeoportailInitLayerLocator()
{
/**
 * Class: OpenLayers.Layer.Vector.Locator
 * Couche pour la localisation
 *
 * Inherits from:
 * - <OpenLayers.Layer.Vector>
 */

OpenLayers.Layer.Vector.Locator = OpenLayers.Class(OpenLayers.Layer.Vector, 
{	
	/**
		Fonction appelee apres deplacement callBack(lon,lat,name);
	*/
	callback:null,
	proj_code:'IGNF:RGF93G',
	locInit:false,
	unique:true,
	drag_feature:true,
		
    /**
     * Constructor: OpenLayers.Layer.Vector.Layer.Vector.Locator
     */
    initialize: function(name, options) 
    {   OpenLayers.Layer.Vector.prototype.initialize.apply(this, arguments);
		// Rempir les options
		if (options.callback) this.callback = options.callback;
		if (options.projection) this.proj_code = options.projection;
		if (options.unique) this.unique = options.unique;
		if (options.drag_feature) this.drag_feature = options.drag_feature;
		// Style du calque :
		var symbolizer = OpenLayers.Util.applyDefaults(
			{ pointRadius: 20, graphicXOffset: -6, graphicYOffset: -35}, 
			OpenLayers.Feature.Vector.style["default"]);
		if (this.drag_feature) symbolizer.cursor = 'move';
		this.styleMap = new OpenLayers.StyleMap({"default": symbolizer, "select": {pointRadius: 20}});
    },
    
    /**
     * Localiser un point
     */
    locate: function (x, y, name)
    {	if (this.map)
		{	if (!this.locInit)	
			{	if (this.drag_feature)
				{	// Controle pour le deplacement
					var options = {
							onDrag : function (f,p) { this.layer.ismove(f,p); },
							onComplete : function (f,p) { this.layer.ismove(f,p); }
						}
					var drag_feature = new OpenLayers.Control.DragFeature(this, options);
					this.map.addControl(drag_feature);
					drag_feature.activate();
				}
				// c'est bon !
				this.locInit=true;
			}
			
			// Supprimer l'ancien point
			if (this.unique) this.destroyFeatures();
			// Creer le nouveau point
			var pt;
			if (!x || !y)
			{	pt = new OpenLayers.Geometry.Point (this.map.center.lon, this.map.center.lat);
			}
			else 
			{	pt = new OpenLayers.Geometry.Point (x,y);
				pt.transform ( new OpenLayers.Projection(this.proj_code), this.map.getProjection());
				// Permettre une nouvelle transfo
				pt.transformed = false;
			}
			// Verfier qu'on est dans la zone
			if (this.map.maxExtent.left > pt.x || this.map.maxExtent.right < pt.x 
			|| this.map.maxExtent.bottom > pt.y || this.map.maxExtent.top < pt.y)
			{	return false;
			} 
			var geometry = new OpenLayers.Geometry.Point (pt.x,pt.y);
			// Ajouter le point
			var feature = new OpenLayers.Feature.Vector(pt);
			feature.state = 'Insert';
			if (name) feature.attributes['name'] = name;
			this.addFeatures(feature);
			// Mettre a jour
			this.ismove(feature);
			return true;
		}
		
	},

	// Deplacement d'un objet
	ismove: function (feature, pix)
	{	if (!feature || !feature.geometry  || !feature.geometry.x) return;
		var pt = new OpenLayers.Geometry.Point (feature.geometry.x, feature.geometry.y);
		pt.transform (this.map.getProjection(), new OpenLayers.Projection(this.proj_code));
		// Affichage dans l'element
		if (this.callback) this.callback(pt.x, pt.y, feature.attributes['name']);
	},

	 /**
     * Constant: CLASS_NAME
     * {String} *"OpenLayers.Layer.Vector.Layer.Vector.Locator"*
     */
    CLASS_NAME : "OpenLayers.Layer.Vector.Layer.Vector.Locator"
});

}