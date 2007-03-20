//array para gadar os marcadores
var marcadores = [];
//array para gardar o html contido das xanelas de cada marcador
var contidosHTML = [];
//array para gardar as URL dos sonidos que se reproduciran en cada xanela
var URLsons = [];

function creaMarcador(point, html, icon, son) {
	//creamos un obxecto GMarker e o gradamos nunha variable
	var marcador = new GMarker(point, icon);
	//engadimos un evento para que ao pulsar no marcador se abra a ventana co html indicado
	GEvent.addListener(marcador, "click", function() {
		marcador.openInfoWindowHtml(html);
		//cando se abre a ventana do marcador executamos as seguintes intsruccions
		GEvent.addListener(map,"infowindowopen", function() {
			//esta parte del codigo enbebe un obxecto flah na ventana creado con flashobject.js
			var fo = new FlashObject("http://www.escoitar.org/loudblog/custom/templates/berio/musicplayer.swf?autoplay=true&song_url="+son, "player_x", "17", "17", "6", "#FFFFFF");
			fo.write("player");
			//inicializamos lightbox para permitir o efecto de apertura da imaxe
			//initLightbox();
		});
	});		
	return marcador;
}

function agregarMarcador (xmlMarker, minZoom,  maxZoom) {
	//almacenamos en variables a informacion contida nos atributos de cada marcador no xml
	var id = parseFloat(xmlMarker.getAttribute("id"));
	var lat = parseFloat(xmlMarker.getAttribute("lat"));
	var lng = parseFloat(xmlMarker.getAttribute("lng"));
	var cat = parseFloat(xmlMarker.getAttribute("categoria"));
	var son = xmlMarker.getAttribute("arquivo");
	var texto = xmlMarker.getAttribute("texto")
	var autor = xmlMarker.getAttribute("autor")
	var img = xmlMarker.getAttribute("img")
	var urlweb = xmlMarker.getAttribute("urlweb")
	var web = xmlMarker.getAttribute("web")
	var hora = xmlMarker.getAttribute("hora")
	var data = xmlMarker.getAttribute("fecha")
	//construimos o html que contera a xanala do marcador
    var html = escribeHtml(texto, autor, img, urlweb, web, hora, data);
    //asignamos o icono que lle corresponde a sua categoria
    var icono_categoria = conxunto_iconos[cat];
    //creamos un GPoint para as coordenadas correspondentes 
    var point = new GPoint(lat,lng);
	//creamos o marcador
    var marcador = creaMarcador(point, html, icono_categoria, son);
    //na posicion adecuada dos arrays recollemos a informacion que sexa necesaria
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

function escribeHtml(texto, autor, img, urlweb, web, hora, fecha) {

	var html = "<div style=\"width:200px\"><div id=\"player\" style=\"margin-bottom: 5px;\"></div><span class=\"windowText\">" + texto + "</span>";
	
	if (img) {
		html += "<br><br><a href=\"http://www.escoitar.org/upload/imaxes/";
		html += img;
		html += "\" rel=\"lightbox[titulo]\"><img src=\"http://www.escoitar.org/upload/imaxes/mini_";
		html += img;
		html += "\" border=\"0\" height=\"60\"></a>";
	}
	
	html += "<br><b class=\"windowBlack\">" + autor + "</b>&nbsp;&nbsp;&nbsp;";
	
	if (urlweb.length>4) {
		html += "<a class=\"window\" target=\"_blank\" href=\"" + urlweb + "\">" + web + "</a><br>";
	}
	
    html += "<span class=\"windowRight\"><b>as " + hora + " do " + fecha + "</b></span><br><br></div>";
    
	return html;
}

function abrirVentana(identificador) {
	map.closeInfoWindow();
	marcadores[identificador].openInfoWindowHtml(contidosHTML[identificador]);
	//enbebemos o flash player do son
	var fo = new FlashObject("http://www.escoitar.org/loudblog/custom/templates/berio/musicplayer.swf?autoplay=true&song_url=" + URLsons[identificador], "player_x", "17", "17", "6", "#FFFFFF");
	fo.write("player");
	//inicializamos lightbox para permitir o efecto de apertura da imaxe
	//initLightbox();
}
