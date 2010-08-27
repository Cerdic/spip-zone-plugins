//***********
// Gis Marker
//***********
function GisMarker() {
	this.id = false;
	this.html = false;
	this.enclosure = false;
	this.json = false;
	this.marker = false;
}
GisMarker.prototype.openWindow = function() {
	openWindow(this);
}
function openWindow(marcador) {
	//cando se abre a ventana do marcador executamos as seguintes intsruccions
	GEvent.addListener(marcador.marker,"infowindowopen", function() {
		if (marcador.son) {
			var fo = new FlashObject(URLbaseGis + "/img_pack/musicplayer.swf?autoplay=true&song_url=" + marcador.son, "player_x", "17", "17", "6", "#FFFFFF");
			fo.write("player");
		}
	});
	if (marcador.html=="") {
		var msg = "<div id='precarga'>loading marker data...<br><img src='" + URLbaseGis + "img_pack/loading.gif' /></div>";
		$("#map_loader_msg").append(msg);
		$("#map_loader").show();
		$.getJSON(marcador.json, function(jsonData) {
			$.each(jsonData.marker, function(i,item){
				marcador.html = "<div id='window_" + item.id +"' class='window_content'><div id='player'></div><h3><a href='" + item.link + "'>" + item.title + "</a></h3>" + item.description + "</div>";
				if (typeof(item.enclosure) != "undefined") {
					marcador.son = item.enclosure.url;
				}
				$("#map_loader").hide();
				$("#map_loader_msg").empty();
				marcador.marker.openInfoWindowHtml(marcador.html);
			});
		});
	} else {
		marcador.marker.openInfoWindowHtml(marcador.html);
	}
}


function getNodeText(node){
	return node.text || node.firstChild ? node.firstChild.nodeValue : "";
}

//GL recoller a id da URL do artigo
//ENG get id from the article'URL
//FR recuperer l'id de l'article dans l'URL
// cette fonction n'est plus utilisee depuis qu'on colle l'id dans le guid du RSS
// on la garde en reserve au cas ou...
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

function coordenadas (articulo){
	$.ajax({
		type: "POST",
		url: "'.generer_url_public('cambiar_coordenadas').'",
		data: "id_article="+articulo+"&lat="+document.forms.formulaire_coordenadas.lat.value+"&lonx="+document.forms.formulaire_coordenadas.lonx.value,
		success: function() {
		}
	});
}

function agregarMarcadorJson (jsonItem, idmap, minZoom, maxZoom, markerMngerXD) {
	//almacenamos en distintas variables la informacion contenida nen los chilNodes de cada item-marcador do xml
	var markerMgr = eval('markerManager'+idmap);
	if ((typeof(jsonItem.lat) == "undefined")||(typeof(jsonItem.lonx) == "undefined")) return;
	else {
		var lat = parseFloat(jsonItem.lat);
		var lng = parseFloat(jsonItem.lonx);
		var id = parseInt(jsonItem.id);

		var point = new GPoint(lng,lat);
		var icono_categoria = new GIcon();

		var markerWidth = (jsonItem.icon.imageWidth ? jsonItem.icon.imageWidth : MarkerBaseWidth);
		var markerHeight = (jsonItem.icon.imageHeight ? jsonItem.icon.imageHeight : MarkerBaseHeight);
		var mShadowWidth = (jsonItem.icon.shadowWidth ? jsonItem.icon.shadowWidth : MarkerShadowWidth);
		var mShadowHeight = (jsonItem.icon.shadowHeight ? jsonItem.icon.shadowHeight : MarkerShadowHeight);
		
		icono_categoria.image = (jsonItem.icon.image ? jsonItem.icon.image : MarkerImgBase);
		icono_categoria.shadow = (jsonItem.icon.shadow ? jsonItem.icon.shadow: MarkerShadowBase);
		icono_categoria.iconSize = new GSize(markerWidth, markerHeight);
		icono_categoria.shadowSize = new GSize(mShadowWidth, mShadowHeight);	
		icono_categoria.iconAnchor = new GPoint((markerWidth/2), markerHeight);
		icono_categoria.infoWindowAnchor = new GPoint(markerWidth*1, 0);
		
		eval('marcador_' + id + ' = new GisMarker();');
		var marcador = eval('marcador_' + id);
		marcador.html = "";;
		marcador.son = "";
		marcador.json = jsonItem.data;
		marcador.id = id;
		marcador.marker = new GMarker(point, icono_categoria);
		GEvent.addListener(marcador.marker, "click", function() {
			marcador.openWindow();
		});
		markerMgr.addMarker(marcador.marker, minZoom,  maxZoom);
	}	
}

function agregarMarcador (xmlItem, idmap, minZoom, maxZoom, markerMngerXD, ombre, noplayer) {
	//almacenamos en distintas variables la informacion contenida nen los chilNodes de cada item-marcador do xml
	var xmlLat = $("geo_lat",xmlItem);
	var xmlLng = $("geo_long",xmlItem);
	var xmlSon = $("enclosure",xmlItem);
	var id = $("guid",xmlItem);
	var markerMgr = eval('markerManager'+idmap);
	if ((xmlLat.length == 0) || (xmlLng.length == 0)) return;
	else {
		var lat = parseFloat(xmlLat.text());
		var lng = parseFloat(xmlLng.text());
		var id = parseInt(id.text());
		var html = "<div id='window_" + id +"' class='window_content'>";
    if (noplayer == '') html += "<div id='player'></div>";
    html += "<h3><a href='" + $("link",xmlItem).text() + "'>" + $("title",xmlItem).text() + "</a></h3>" + $("description",xmlItem).text() + "</div>";
		var icon = $("geo_icon",xmlItem);
		var iconWidth = icon.attr("width");
		var iconHeight = icon.attr("height");
		var shadow = $("geo_icon_shadow",xmlItem);
		var shadowWidth = shadow.attr("width");
		var shadowHeight = shadow.attr("height");
		icon = icon.text();
		shadow = shadow.text();
		
		var son;
		if (xmlSon.length != 0) son = xmlSon.attr("url");
   	
		//creamos un Gpoint para situar nel o marcador
		var point = new GPoint(lng,lat);
		
		//creamos un icono para o marcador
		var icono_categoria = new GIcon();
		
		//chequeamos si hai icono personalizado e po–emos a sua url, ancho e alto.
		if(icon != ''){
			icono_categoria.image = icon;
		}else{		
			icono_categoria.image = MarkerImgBase;
			iconWidth = MarkerBaseWidth;
			iconHeight = MarkerBaseHeight;
		}
		
		//chequeamos si queremos sombra
		if(ombre != ''){
			//chequeamos si hai sombra personalizada e po–emos a sœa url, ancho e alto.
			if(shadow != ''){
				icono_categoria.shadow = shadow;
			}else{
				icono_categoria.shadow = MarkerShadowBase;
				shadowWidth = MarkerShadowWidth;
				shadowHeight = MarkerShadowHeight;
			}
		}
		icono_categoria.iconSize = new GSize(iconWidth, iconHeight);
		icono_categoria.shadowSize = new GSize(shadowWidth, shadowHeight);	
		icono_categoria.iconAnchor = new GPoint((iconWidth/2), iconHeight);
		icono_categoria.infoWindowAnchor = new GPoint(iconWidth*1, 1);
			
		// creamos el marcador con los datos almacenados en las variables
		eval('marcador_' + id + ' = new GisMarker();');
		var marcador = eval('marcador_' + id);
		marcador.html = html;
		marcador.son = son;
		marcador.id = id;
		marcador.marker = new GMarker(point, icono_categoria);
		GEvent.addListener(marcador.marker, "click", function() {
			marcador.openWindow();
		});
		markerMgr.addMarker(marcador.marker, minZoom,  maxZoom);
	}	
}

function loadMarkers(url, idmap, msg, minLat, maxLat, monLonx, maxLonx) {
   	var latitudes = calculaIntervalos(minLat, maxLat, 3);
  	var lonxitudes = calculaIntervalos(monLonx, maxLonx, 3);
  	var map = eval('map' + idmap);
  	var markerMgr = eval('markerManager'+idmap);
  	var zoom = map.getZoom();
  	if(zoom>=2) {
  		zoom = zoom - 2;
  	}
	var count = 0;
	if ($("#map_loader").is(":hidden")) {
		var loading_msg = "<div id='precarga'>" +msg+ "<br><img src='" +URLbaseGis+ "img_pack/loading.gif' /><"+"/div>";
   		$("#map_loader_msg").append(loading_msg);
   		$("#map_loader").show();
	}
	for (i=0; i<3; i++) {
		for(u=0; u<3; u++) {
			$.getJSON(url+ '&minlat=' +latitudes[i]+ '&maxlat=' +latitudes[(i+1)]+ '&minlonx=' +lonxitudes[u]+ '&maxlonx=' +lonxitudes[(u+1)], function(data) {
				$.each(data.markers, function(i, item){
					agregarMarcadorJson(item, idmap, zoom, 17, markerMgr);
				});
				count++;
				if(count==9) {
					$("#map_loader").hide();
					$("#map_loader_msg").empty();	
				}
			});
		}
	}
}

/****************************************************
estas son funciones nuevas para realizar calculos 
espaciales de latitude y longitudes en el mapa
****************************************************/
//coordenadas visibles en el mapa
function calculaRecuadro (mapa) {
    var bounds = mapa.getBounds();
    var coordenadas = new Array ();
    //minlat
    coordenadas [0] = Math.round(bounds.getSouthWest().lat()*10000) / 10000;
    //maxlat
    coordenadas [1] = Math.round(bounds.getNorthEast().lat()*10000) / 10000;
    //minlonx
    coordenadas [2] = Math.round(bounds.getSouthWest().lng()*10000) / 10000;
    //maxlonx
    coordenadas [3] = Math.round(bounds.getNorthEast().lng()*10000) / 10000;
    return coordenadas;
}
//intervalos para dibidir un recuadro de coordenadas en 9 zonas diferentes
function calculaIntervalos(min, max, num){
    var incremento = (max - min) / num;
    var intervalos = new Array ();
    for (i=0; i<=num; i++){
    	intervalos[i] = Math.round((min + (incremento * i)) * 10000) / 10000;
    }
    return intervalos;
}

function zoomIci(latit, lonxit, zoom, idmap) {
    var map = eval('map'+ idmap);
    map.panTo(new GLatLng(latit, lonxit));
    map.setZoom(zoom)
}

function abrirVentana(id, idmap) {
	var map = eval('map'+ idmap);
	var marcador = eval('marcador_'+ id);
	map.closeInfoWindow();
	map.setCenter(marcador.marker.getLatLng());
	marcador.openWindow();
}