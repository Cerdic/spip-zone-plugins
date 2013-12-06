//---------------------------------------------------------------
//	Surcharge de leaflet
//---------------------------------------------------------------
/*
L.Control.ControlPerso = L.Control.extend({
    options: {
        position: 'bottomleft'
    },
    initialize: function(options) {
        this._button = {};
        this.setButton(options);  //method of this class, not inherited
    },

    onAdd: function(map) {
        this._map = map;
        var container = L.DomUtil.create('div', 'leaflet-control-button');

        this._container = container;

        this._update();
        return this._container;
    }

    //etc, etc

});​*/

// bouton générique
L.Control.Button = L.Control.extend({
    options: {
        position: 'topright',
	    title: null,
	    url: null,
		onclick: null
    },
    initialize: function(options) {
		L.Util.setOptions(this, options);
        this._button = {};
        this.setButton(options);  //method of this class, not inherited
    },

    onAdd: function(map) {
		this._map = map;
		var container = L.DomUtil.create('div', 'leaflet-control-add-cam');
		container.innerHtml = "bt";
		this._container = container;

		var link = this._layersLink = L.DomUtil.create('a', '-toggle', container);
		if (this.options.title != null) link.setAttribute('title', this.options.title );
		container.appendChild(link);
		
		L.DomEvent.addListener(container, 'click', this.onclick, this);
		
		this._update();
		return this._container;
    },

	setButton: function(options){
		
	},
	
	onclick: function(e){
		if (this.options.onclick != null) this.options.onclick(e);
		if (this.options.url != null) window.location = this.options.url;
	},
	
	_update: function () {
		if (!this._container) {
			return;
		}
	}
	
});

// bouton d'ajout de cam
L.Control.AjoutCam = L.Control.Button.extend({
	options: {
		collapsed: true,
		position: 'topright'
	}
});

// options avancées pour la carte
L.Control.Carto = L.Control.Layers.extend({
	//options: {
	//	collapsed: true,
	//	position: 'topright'
	//},
    //
	//initialize: function (baseLayers, overlays, options) {
	//	L.Util.setOptions(this, options);
    //
	//	this._layers = {};
    //
	//	for (var i in baseLayers) {
	//		if (baseLayers.hasOwnProperty(i)) {
	//			this._addLayer(baseLayers[i], i);
	//		}
	//	}
    //
	//	for (i in overlays) {
	//		if (overlays.hasOwnProperty(i)) {
	//			this._addLayer(overlays[i], i, true);
	//		}
	//	}
	//},
    //
	//onAdd: function (map) {
	//	this._initLayout();
	//	this._update();
    //
	//	return this._container;
	//},
    //
	//addBaseLayer: function (layer, name) {
	//	this._addLayer(layer, name);
	//	this._update();
	//	return this;
	//},
    //
	//addOverlay: function (layer, name) {
	//	this._addLayer(layer, name, true);
	//	this._update();
	//	return this;
	//},
    //
	//removeLayer: function (layer) {
	//	var id = L.Util.stamp(layer);
	//	delete this._layers[id];
	//	this._update();
	//	return this;
	//},
    //
	_initLayout: function () {
		var className = 'leaflet-control-layers',
		    container = this._container = L.DomUtil.create('div', className);
    
		if (!L.Browser.touch) {
			L.DomEvent.disableClickPropagation(container);
		} else {
			L.DomEvent.addListener(container, 'click', L.DomEvent.stopPropagation);
		}
    
		var form = this._form = L.DomUtil.create('form', className + '-list');
    
		if (this.options.collapsed) {
			L.DomEvent
				.addListener(container, 'mouseover', this._expand, this)
				.addListener(container, 'mouseout', this._collapse, this);
    
			var link = this._layersLink = L.DomUtil.create('a', className + '-toggle', container);
			link.href = '#';
			link.title = 'Layers';
    
			L.DomEvent.addListener(link, L.Browser.touch ? 'click' : 'focus', this._expand, this);
    
			this._map.on('movestart', this._collapse, this);
			// TODO keyboard accessibility
		} else {
			this._expand();
		}
    
		this._baseLayersList = L.DomUtil.create('div', className + '-base', form);
		this._separator = L.DomUtil.create('div', className + '-separator', form);
		this._overlaysList = L.DomUtil.create('div', className + '-overlays', form);
    
		container.appendChild(form);
	},
    //
	//_addLayer: function (layer, name, overlay) {
	//	var id = L.Util.stamp(layer);
	//	this._layers[id] = {
	//		layer: layer,
	//		name: name,
	//		overlay: overlay
	//	};
	//},
    //
	_update: function () {
		if (!this._container) {
			return;
		}
    
		this._baseLayersList.innerHTML = '';//'Couches de Base :';
		this._overlaysList.innerHTML = '';//'Couches optionelles : ';
    
		var baseLayersPresent = false,
			overlaysPresent = false;
    
		for (var i in this._layers) {
			if (this._layers.hasOwnProperty(i)) {
				var obj = this._layers[i];
				this._addItem(obj);
				overlaysPresent = overlaysPresent || obj.overlay;
				baseLayersPresent = baseLayersPresent || !obj.overlay;
			}
		}
    
		this._separator.style.display = (overlaysPresent && baseLayersPresent ? '' : 'none');
	},
    //
	//_addItem: function (obj, onclick) {
	//	var label = document.createElement('label');
    //
	//	var input = document.createElement('input');
	//	if (!obj.overlay) {
	//		input.name = 'leaflet-base-layers';
	//	}
	//	input.type = obj.overlay ? 'checkbox' : 'radio';
	//	input.checked = this._map.hasLayer(obj.layer);
	//	input.layerId = L.Util.stamp(obj.layer);
    //
	//	L.DomEvent.addListener(input, 'click', this._onInputClick, this);
    //
	//	var name = document.createTextNode(' ' + obj.name);
    //
	//	label.appendChild(input);
	//	label.appendChild(name);
    //
	//	var container = obj.overlay ? this._overlaysList : this._baseLayersList;
	//	container.appendChild(label);
	//},
    //
	//_onInputClick: function () {
	//	var i, input, obj,
	//		inputs = this._form.getElementsByTagName('input'),
	//		inputsLen = inputs.length;
    //
	//	for (i = 0; i < inputsLen; i++) {
	//		input = inputs[i];
	//		obj = this._layers[input.layerId];
    //
	//		if (input.checked) {
	//			this._map.addLayer(obj.layer, !obj.overlay);
	//		} else {
	//			this._map.removeLayer(obj.layer);
	//		}
	//	}
	//},
    //
	//_expand: function () {
	//	L.DomUtil.addClass(this._container, 'leaflet-control-layers-expanded');
	//},
    //
	//_collapse: function () {
	//	this._container.className = this._container.className.replace(' leaflet-control-layers-expanded', '');
	//}
	_rien: function(){}
});



// Icons avec angle - https://github.com/CloudMade/Leaflet/issues/386
// modifié pour ajouté la rotation de l'ombre (champ de vision)
L.Marker.Compass = L.Marker.extend({
    _reset: function() {
	
        var pos = this._map.latLngToLayerPoint(this._latlng).round();
        L.DomUtil.setPosition(this._icon, pos);

        if (this._shadow) L.DomUtil.setPosition(this._shadow, pos);

        if (this.options.iconAngle) {
            this._icon.style.WebkitTransform = this._icon.style.WebkitTransform + ' rotate(' + this.options.iconAngle + 'deg)';
            this._icon.style.MozTransform = 'rotate(' + this.options.iconAngle + 'deg)';
            this._icon.style.MsTransform = 'rotate(' + this.options.iconAngle + 'deg)';
            this._icon.style.OTransform = 'rotate(' + this.options.iconAngle + 'deg)';
            this._icon.style.Transform = 'rotate(' + this.options.iconAngle + 'deg)';
        }
		
		// echelle en fonction du zoom
		var scale = ( this._map.getZoom() <= 16 ) ? 1/2 : 1; //1/2;
		this.setIconScale(this._shadow, 'shadow', scale);
		this.setIconScale(this._icon, 'icon', scale);

        if (this.options.shadowAngle) {
            this._shadow.style.WebkitTransform = this._shadow.style.WebkitTransform + ' rotate(' + this.options.shadowAngle + 'deg)';
            this._shadow.style.MozTransform = 'rotate(' + this.options.shadowAngle + 'deg)';
            this._shadow.style.MsTransform = 'rotate(' + this.options.shadowAngle + 'deg)';
            this._shadow.style.OTransform = 'rotate(' + this.options.shadowAngle + 'deg)';
            this._icon.style.Transform = 'rotate(' + this.options.shadowAngle + 'deg)';
        }

        if (this.options.markerOpacity) {
			// TODO: appliquer au container supérieur pour transparence commune
			L.DomUtil.setOpacity(this._icon, this.options.markerOpacity);
			L.DomUtil.setOpacity(this._shadow, this.options.markerOpacity);
		}
		
        this._icon.style.zIndex = pos.y;
    },

    setIconAngle: function(iconAngle) {
        if (this._map) this._removeIcon();
        this.options.iconAngle = iconAngle;
        if (this._map) {
            this._initIcon();
            this._reset();
        }
    },

    setShadowAngle: function(shadowAngle) {
        if (this._map) this._removeIcon();
        this.options.shadowAngle = shadowAngle;
        if (this._map) {
            this._initIcon();
            this._reset();
        }
    },

	// cf: leaflet-src.js : L.Icon._setIconStyles
	setIconScale: function(img, name, scale){
		var options = this.options.icon.options;
			size = options[name + 'Size'].multiplyBy(scale,true), //icon ou shadow
			anchor = options.iconAnchor;
		
		if (!anchor && size) {
			anchor = size.divideBy(2, true);
		}

		if (name === 'shadow' && anchor && options.shadowOffset) {
			anchor._add(options.shadowOffset);
		}

		if (anchor) {
			img.style.marginLeft = (-anchor.x) + 'px';
			img.style.marginTop  = (-anchor.y) + 'px';
		}

		if (size) {
			img.style.width  = size.x + 'px';
			img.style.height = size.y + 'px';
		}
		
	},

	setMarkerOpacity: function(opacity) {
        if (this._map) this._removeIcon();
        this.options.markerOpacity = opacity;
		if (this._map) {
            this._initIcon();
            this._reset();
		}
	},
	
	redraw: function() {
		//console.log("---> REDRAW");
        if (this._map) this._removeIcon();
		if (this._map) {
			//console.log("---> REDRAW (this._map)");
            this._initIcon();
            this._reset();
		}
	}
});