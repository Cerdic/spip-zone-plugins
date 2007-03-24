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
//FR ???
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

function creaMarcador(point, html, icon, son) {
	//creamos un obxecto GMarker e o gradamos nunha variable
	var marcador = new GMarker(point, icon);
	//engadimos un evento para que ao pulsar no marcador se abra a ventana co html indicado
	GEvent.addListener(marcador, "click", function() {
		marcador.openInfoWindowHtml(html);
		//cando se abre a ventana do marcador executamos as seguintes intsruccions
		GEvent.addListener(map,"infowindowopen", function() {
			if(son){
				//esta parte del codigo enbebe un obxecto flah na ventana creado con flashobject.js
				var fo = new FlashObject(URLbase + "plugins/gis/img_pack/musicplayer.swf?autoplay=true&song_url="+son, "player_x", "17", "17", "6", "#FFFFFF");
				fo.write("player");
			}
		});
	});		
	return marcador;
}

function agregarMarcador (xmlItem, minZoom,  maxZoom) {
	//almacenamos en distintas variables la informacion contenida nen los chilNodes de cada item-marcador do xml
	var id = extraerID(getNodeText(xmlItem.childNodes[2]));
	var lat = parseFloat(getNodeText(xmlItem.childNodes[6]));
	var lng = parseFloat(getNodeText(xmlItem.childNodes[7]));
	var son = xmlItem.childNodes[8].getAttribute("url");
	var html = getNodeText(xmlItem.childNodes[4]);
   	
	//creamos un Gpoint para situar nel o marcador
	var point = new GPoint(lng,lat);
		
	//creamos un icono para o marcador
	var icono_categoria = new GIcon();
	icono_categoria.image = URLbase + "/plugins/gis/img_pack/correxir.png";
	icono_categoria.shadow = URLbase + "/plugins/gis/img_pack/shadow.png";
	icono_categoria.iconSize = new GSize(20, 34);
	icono_categoria.shadowSize = new GSize(22, 20);	
	icono_categoria.iconAnchor = new GPoint(10, 34);
	icono_categoria.infoWindowAnchor = new GPoint(5, 1);
			
	// creamos el marcador con los datos almacenados en las variables
	var marcador = creaMarcador(point, html, icono_categoria, son);
		
	// recollemos a informacion que sexa necesaria en distintos arrays, usando como identificador a id do artigo
	marcadores[id] = marcador;
	contidosHTML[id] = html;
	URLsons[id] = son;
    		
	//engadimos o marcador ao markerManager antes "map.addOverlay(marker);"
	if (maxZoom) {
		markerManager.addMarker(marcador, minZoom,  maxZoom);
	} else {
		markerManager.addMarker(marcador, minZoom);
	}
}

function abrirVentana(identificador) {
	map.closeInfoWindow();
	marcadores[identificador].openInfoWindowHtml(contidosHTML[identificador]);
	//enbebemos o flash player do son
	var fo = new FlashObject( URLbase + "/plugins/gis/img_pack/musicplayer.swf?autoplay=true&song_url=" + URLsons[identificador], "player_x", "17", "17", "6", "#FFFFFF");
	fo.write("player");
}