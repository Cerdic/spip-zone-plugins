function getNodeText(node){
	return node.text || node.firstChild ? node.firstChild.nodeValue : "";
}

//GL recoller a id da URL do artigo
//ENG get id from the article'URL
//FR r�cup�rer l'id de l'article dans l'URL
function extraerID(url){
	var posicion = url.indexOf("article");
	if (posicion != -1) {
		url = url.substring(posicion + 7);
		posicion = url.indexOf ("&");
		if (posicion != -1) {
			url = url.substring(0,posicion);
		}
	//se non e un artigo de spip que lle dean
	} else {
		url = url.substring(url.length - 4);
	}
	return url;
}
function agregarMarcador (xmlItem, markerLayer, map) {
	var xmlLat = $("geo_lat",xmlItem);
	var xmlLng = $("geo_long",xmlItem);
	var xmlSon = $("enclosure",xmlItem);
	var popup = null;
	if ((xmlLat.length == 0) || (xmlLng.length == 0)) return;
	else {
		var lat = parseFloat(xmlLat.text());
		var lng = parseFloat(xmlLng.text());
		var id = extraerID($("link",xmlItem).text());
		var html = $("description",xmlItem).text();
		var icon = $("geo_icon",xmlItem).text();
		var son;
		if (xmlSon.length != 0) son = xmlSon.attr("url");
		var point = new OpenLayers.LonLat(lng,lat);
    var size = new OpenLayers.Size(20,34);
		var calculateOffset = function(size) { return new OpenLayers.Pixel(-(size.w/2), -size.h); };
		var urlicon = (icon != "" ? icon : URLbase + "/gis/img_pack/correxir.png");
		var icon = new OpenLayers.Icon(
			urlicon,
			size,
			null,
			calculateOffset
		);
		var marcador = new OpenLayers.Marker(point.fromDataToDisplay(), icon);
		marcador.events.register("mousedown", marcador, function mousedown(evt) {
			if (popup == null) {
				popup = new OpenLayers.Popup.FramedCloud("popup"+id, point.fromDataToDisplay());
       			popup.panMapIfOutOfView = true;
       			popup.autoSize = true;
      			popup.closeBox = true;
      			popup.padding = 10;
       			popup.contentDiv.style.overflow = 'visible';
       			popup.setContentHTML(html);
				map.addPopup(popup);
			} else {
				popup.toggle();
			}
			OpenLayers.Event.stop(evt);
		});
		markerLayer.addMarker(marcador);
	}	
}

OpenLayers.LonLat = OpenLayers.Class(OpenLayers.LonLat, {

	dataProjection: new OpenLayers.Projection("EPSG:900913"),
			
	mapProjection: new OpenLayers.Projection("EPSG:4326"),
    	
	fromDisplayToData: function() {
		if(this.dataProjection != this.mapProjection) {
			return new OpenLayers.LonLat(this.lon, this.lat).transform(this.dataProjection, this.mapProjection);
		} else {
			return new OpenLayers.LonLat(this.lon, this.lat);
		}
	},
			
	fromDataToDisplay: function() {
		if(this.dataProjection != this.mapProjection) {
			return new OpenLayers.LonLat(this.lon, this.lat).transform(this.mapProjection, this.dataProjection);
		} else {
			return new OpenLayers.LonLat(this.lon, this.lat);
		}
	}
			
});

function geocode(address,map) {
	var lon = "";
	var lat = "";
	$.getJSON("http://ws.geonames.org/searchJSON?maxRows=1&q='" + address + "'", function(data){
		$.each(data.geonames, function(i,geoname){
			lon = geoname.lng;
			lat = geoname.lat;
		});
		var lonlat = new OpenLayers.LonLat(lon, lat);
		map.setCenter(lonlat.fromDataToDisplay());
	});
}