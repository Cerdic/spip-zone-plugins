/*
	source : https://gist.github.com/1992824
*/

/*
 * Here the 'inspiration': http://goo.gl/OKL9A
 * Adapted from: http://psha.org.ru/leaflet/Google.js
 * Demo: http://psha.org.ru/leaflet/bel.html
 * This code works well with jquerymobile: 
 * the original code maintain a div.height of 0 for the internal google container
 * REMARKS: this
 * NOTE: jQuery required!
 */

L.Google = L.Class.extend({
    includes: L.Mixin.Events,

    options: {
        minZoom: 0,
        maxZoom: 18,
        tileSize: 256,
        subdomains: 'abc',
        errorTileUrl: '',
        attribution: '',
        opacity: 1,
        continuousWorld: false,
        noWrap: false
    },

    // Possible types: SATELLITE, ROADMAP, HYBRID
    initialize: function(type, options) {
        L.Util.setOptions(this, options);

        this._type = window.google.maps.MapTypeId[type || 'SATELLITE'];
    },

    onAdd: function(map, insertAtTheBottom) {
        this._map = map;
        this._insertAtTheBottom = insertAtTheBottom;

        // create a container div for tiles
        this._initContainer();
        this._initMapObject();

        // set up events
        map.on('viewreset', this._resetCallback, this);

        this._limitedUpdate = L.Util.limitExecByInterval(this._update, 150, this);
        map.on('move', this._update, this);
        //map.on('moveend', this._update, this);

        this._reset();
        this._update();
    },

    onRemove: function(map) {
        this._map._container.removeChild(this._container);
        //this._container = null;

        this._map.off('viewreset', this._resetCallback, this);

        this._map.off('move', this._update, this);
        //this._map.off('moveend', this._update, this);
    },

    getAttribution: function() {
        return this.options.attribution;
    },

    setOpacity: function(opacity) {
        this.options.opacity = opacity;
        if (opacity < 1) {
            L.DomUtil.setOpacity(this._container, opacity);
        }
    },

    _initContainer: function() {
        var tilePane = this._map._container;
        first = tilePane.firstChild;

        if (!this._container) {
            this._container = L.DomUtil.create('div', 'leaflet-google-layer leaflet-top leaflet-left');
            this._container.id = "_GMapContainer";
        }

        if (true) {
            tilePane.insertBefore(this._container, first);

            this.setOpacity(this.options.opacity);
            var size = this._map.getSize();
            var c = $(this._container);
            c.width(size.x);
            c.height(size.y);
        }
    },

    _initMapObject: function() {
        this._google_center = new window.google.maps.LatLng(0, 0);
        var map = new window.google.maps.Map(this._container, {
            center: this._google_center,
            zoom: 0,
            mapTypeId: this._type,
            disableDefaultUI: true,
            keyboardShortcuts: false,
            draggable: false,
            disableDoubleClickZoom: true,
            scrollwheel: false,
            streetViewControl: false
        });

        var _this = this;
        this._reposition = window.google.maps.event.addListenerOnce(map, "center_changed",
            function() { _this.onReposition(); });

        map.backgroundColor = '#ff0000';
        this._google = map;
    },

    _resetCallback: function(e) {
        this._reset(e.hard);
    },

    _reset: function(clearOldContainer) {
        this._initContainer();
    },

    _update: function() {
        this._resize();

        var bounds = this._map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var google_bounds = new window.google.maps.LatLngBounds(
            new window.google.maps.LatLng(sw.lat, sw.lng),
            new window.google.maps.LatLng(ne.lat, ne.lng)
        );
        var center = this._map.getCenter();
        var _center = new window.google.maps.LatLng(center.lat, center.lng);

        this._google.setCenter(_center);
        this._google.setZoom(this._map.getZoom());
        //this._google.fitBounds(google_bounds);
    },

    _resize: function() {
        var size = this._map.getSize();
        var b = false;
        var c = $(this._container);
        if (c.width() != size.x) {
            c.width(size.x);
            b = true;
        }
        if (c.height() != size.y) {
            c.height(size.y);
            b = true;
        }
        if (b) {
            window.google.maps.event.trigger(this._google, "resize");
        }
    },

    onReposition: function() {
        window.google.maps.event.trigger(this._google, "resize");
    }
});