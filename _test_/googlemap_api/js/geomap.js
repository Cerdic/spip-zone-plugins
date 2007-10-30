//array para gadar os marcadores
var marcadores = [];
//array para gardar o html contido das xanelas de cada marcador
var contidosHTML = [];
//array para gardar as URL dos sonidos que se reproduciran en cada xanela
var URLsons = [];

function getNodeText(node){
	return node.text || node.firstChild ? node.firstChild.nodeValue : "";
}

//GL recoller a id da URL do artigo
//ENG get id from the article'URL
//FR récupérer l'id de l'article dans l'URL
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

function creaMarcador(point, html, icon, son, idmap) {
	//creamos un obxecto GMarker e o gradamos nunha variable
	var marcador = new GMarker(point, icon);
	//engadimos un evento para que ao pulsar no marcador se abra a ventana co html indicado
	var map = eval('map'+idmap);
	GEvent.addListener(marcador, "click", function() {
		marcador.openInfoWindowHtml(html);
		//cando se abre a ventana do marcador executamos as seguintes intsruccions
		GEvent.addListener(map,"infowindowopen", function() {
			if(son){
				//esta parte del codigo enbebe un obxecto flah na ventana creado con flashobject.js
				var fo = new FlashObject(URLbase + "/gis/img_pack/musicplayer.swf?autoplay=true&song_url="+son, "player_x", "17", "17", "6", "#FFFFFF");
				fo.write("player");
			}
		});
	});		
	return marcador;
}

function agregarMarcador (xmlItem, idmap, minZoom, maxZoom) {
	//almacenamos en distintas variables la informacion contenida nen los chilNodes de cada item-marcador do xml
	var xmlLat = $("geo_lat",xmlItem);
	var xmlLng = $("geo_long",xmlItem);
	var xmlSon = $("enclosure",xmlItem);
	var marker = eval('markerManager'+idmap);
	if ((xmlLat.length == 0) || (xmlLng.length == 0)) return;
	else {
		var lat = parseFloat(xmlLat.text());
		var lng = parseFloat(xmlLng.text());
		var id = extraerID($("link",xmlItem).text());
		var html = $("description",xmlItem).text();
		var icon = $("geo_icon",xmlItem).text();
		var son;
		if (xmlSon.length != 0) son = xmlSon.attr("url");
   	
		//creamos un Gpoint para situar nel o marcador
		var point = new GPoint(lng,lat);
		
		//creamos un icono para o marcador
		var icono_categoria = new GIcon();
		icono_categoria.image = (icon != "" ? icon : URLbase + "/gis/img_pack/correxir.png");
		icono_categoria.shadow = URLbase + "/gis/img_pack/shadow.png";
		icono_categoria.iconSize = new GSize(20, 34);
		icono_categoria.shadowSize = new GSize(22, 20);	
		icono_categoria.iconAnchor = new GPoint(10, 34);
		icono_categoria.infoWindowAnchor = new GPoint(5, 1);
			
		// creamos el marcador con los datos almacenados en las variables
		var marcador = creaMarcador(point, html, icono_categoria, son, idmap);
		// recollemos a informacion que sexa necesaria en distintos arrays, usando como identificador a id do artigo
		marcadores[id] = marcador;
		contidosHTML[id] = html;
		URLsons[id] = son;
		//engadimos o marcador ao markerManager antes "map.addOverlay(marker);"
		if (maxZoom) {
			if(marker){
				marker.addMarker(marcador, minZoom,  maxZoom);
			}
			else{
				markerManager.addMarker(marcador, minZoom,  maxZoom);
			}
		} else if (marker){
			eval(marker).addMarker(marcador, minZoom);
		}
	}	
}

function abrirVentana(identificador) {
	map.closeInfoWindow();
	marcadores[identificador].openInfoWindowHtml(contidosHTML[identificador]);
	//enbebemos o flash player do son
	var fo = new FlashObject( URLbase + "/img_pack/musicplayer.swf?autoplay=true&song_url=" + URLsons[identificador], "player_x", "17", "17", "6", "#FFFFFF");
	fo.write("player");
}