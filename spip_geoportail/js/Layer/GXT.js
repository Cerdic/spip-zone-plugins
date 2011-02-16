/* Copyright (c) 2006-2008 MetaCarta, Inc., published under the Clear BSD
 * license.  See http://svn.openlayers.org/trunk/openlayers/license.txt for the
 * full text of the license. */

/**
 * @requires OpenLayers/Layer/Vector.js
 * @requires OpenLayers/Format/Geoconcept.js
 * @requires OpenLayers/Request/XMLHttpRequest.js
 */

// Fonction de chargement
function GeoportailInitLayerGXT()
{
/**
 * Class: OpenLayers.Layer.GXT
 * Create a vector layer by parsing a GXT file. The GXT file is
 *     passed in as a parameter.
 *
 * Inherits from:
 *  - <OpenLayers.Layer.Vector>
 */
OpenLayers.Layer.GXT = OpenLayers.Class(OpenLayers.Layer.Vector, {
    
    /**
      * Property: loaded
      * {Boolean} Flag for whether the GXT data has been loaded yet.
      */
    loaded: false,

    /**
     * APIProperty: formatOptions
     * {Object} Hash of options which should be passed to the format when it is
     * created. Must be passed in the constructor.
     */
    formatOptions: null, 
    
    /**
     * Constructor: OpenLayers.Layer.GXT
     * Load and parse a single file on the web, according to the format
     * provided via the 'format' option, defaulting to GXT. 
     *
     * Parameters:
     * name - {String} 
     * url - {String} URL of a GXT file.
     * options - {Object} Hashtable of extra options to tag onto the layer.
     */
     initialize: function(name, url, options) {
        var newArguments = [];
        newArguments.push(name, options);
        OpenLayers.Layer.Vector.prototype.initialize.apply(this, newArguments);
        this.url = url;
    },

    /**
     * APIMethod: setVisibility
     * Set the visibility flag for the layer and hide/show&redraw accordingly. 
     * Fire event unless otherwise specified
     * GXT will be loaded if the layer is being made visible for the first
     * time.
     *  
     * Parameters:
     * visible - {Boolean} Whether or not to display the layer 
     *                          (if in range)
     * noEvent - {Boolean} 
     */
    setVisibility: function(visibility, noEvent) {
        OpenLayers.Layer.Vector.prototype.setVisibility.apply(this, arguments);
        if(this.visibility && !this.loaded){
            // Load the GXT
            this.loadGXT();
        }
    },

    /**
     * Method: moveTo
     * If layer is visible and GXT has not been loaded, load GXT, then load GXT
     * and call OpenLayers.Layer.Vector.moveTo() to redraw at the new location.
     * 
     * Parameters:
     * bounds - {Object} 
     * zoomChanged - {Object} 
     * minor - {Object} 
     */
    moveTo:function(bounds, zoomChanged, minor) {
        OpenLayers.Layer.Vector.prototype.moveTo.apply(this, arguments);
        // Wait until initialisation is complete before loading GXT
        // otherwise we can get a race condition where the root HTML DOM is
        // loaded after the GXT is paited.
        // See http://trac.openlayers.org/ticket/404
        if(this.visibility && !this.loaded){
            this.events.triggerEvent("loadstart");
            this.loadGXT();
        }
    },

    /**
     * Method: loadGXT
     */
    loadGXT: function() {
        if (!this.loaded) {
            OpenLayers.Request.GET({
                url: this.url,
                success: this.requestSuccess,
                failure: this.requestFailure,
                scope: this
            });
            this.loaded = true;
        }    
    },    
    
    /**
     * Method: setUrl
     * Change the URL and reload the GXT
     *
     * Parameters:
     * url - {String} URL of a GXT file.
     */
    setUrl:function(url) {
        this.url = url;
        this.destroyFeatures();
        this.loaded = false;
        this.events.triggerEvent("loadstart");
        this.loadGXT();
    },
    
    /**
     * Method: requestSuccess
     * Process GXT after it has been loaded.
     * Called by initialise() and loadUrl() after the GXT has been loaded.
     *
     * Parameters:
     * request - {String} 
     */
    requestSuccess:function(request) {
        var doc = request.responseText;
        var options = {};
        
        OpenLayers.Util.extend(options, this.formatOptions);
        if (this.map) options.internalProjection = this.map.getProjectionObject();
        
        var gxt = new Geoportal.Format.Geoconcept.rip(options);

        this.addFeatures(gxt.read(doc));
        this.events.triggerEvent("loadend");
    },
    
    /**
     * Method: requestFailure
     * Process a failed loading of GXT.
     * Called by initialise() and loadUrl() if there was a problem loading GXT.
     *
     * Parameters:
     * request - {String} 
     */
    requestFailure: function(request) {
        OpenLayers.Console.userError(OpenLayers.i18n("errorLoadingGXT", {'url':this.url}));
        this.events.triggerEvent("loadend");
    },

    CLASS_NAME: "OpenLayers.Layer.GXT"
});
}