//array para gadar os marcadores
var marcadores = [];
//array para gardar o html contido das xanelas de cada marcador
var contidosHTML = [];
//array para gardar as URL dos sonidos que se reproduciran en cada xanela
var URLsons = [];

var id = 0;


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

function creaMarcador(point, html, icon,survol) {
	//creamos un obxecto GMarker e o gradamos nunha variable
	//alert("coucou");
	var marcador = new GMarker(point, icon);
	var tooltip = new Tooltip(marcador,survol,4); 
	marcador.tooltip = tooltip;
	map1.addOverlay(tooltip);
	GEvent.addListener(marcador,'mouseover',function(){ this.tooltip.show(); });
	GEvent.addListener(marcador,'mouseout',function(){ this.tooltip.hide(); });

	//engadimos un evento para que ao pulsar no marcador se abra a ventana co html indicado
	GEvent.addListener(marcador, "click", function(){
		marcador.openInfoWindowHtml(html);
		//cando se abre a ventana do marcador executamos as seguintes intsruccions
		/*GEvent.addListener(map,"infowindowopen", function() {
			if(son){
				//esta parte del codigo enbebe un obxecto flah na ventana creado con flashobject.js
				var fo = new FlashObject(URLbase + "plugins/mymap/img_pack/musicplayer.swf?autoplay=true&song_url="+son, "player_x", "17", "17", "6", "#FFFFFF");
				fo.write("player");
			}
		});*/
	});		
	return marcador;
}
function var_dump(obj) {
   if(typeof obj == "object") {
      return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
   } else {
      return "Type: "+typeof(obj)+"\nValue: "+obj;
   }
}//end function var_dump

function agregarMarcador (xmlItem, minZoom,  maxZoom, idmanager) {

	$(xmlItem).find("geo_points").each(function() { 
	    $(this).find("geo_point").each(function() {

				var xmlLng = $("geo_long",this).text();
				var html = $("geo_desc",this).text();
				var survol = $("geo_descrapide",this).text();
				//alert($("geo_desc",this).text().innerHTML);
				var xmlLat = $("geo_lat",this).text();
				var micon =  $("geo_mmark",this).text();
				//var xmlSon = $("enclosure",xmlItem);
				//alert(xmlLng+xmlLat+html);
				if ((xmlLat.length == 0) || (xmlLng.length == 0)) return;
				else {
				
					var lat = parseFloat(xmlLat);
					var lng = parseFloat(xmlLng);
					
					
					//var id = extraerID($("link",xmlItem).text());
					//var html = $("description",xmlItem).text();
					//////////ICONE PERSO//////////////////////
					if(micon!=''){
						//alert(URLbase + URLabsolute + "/img_pack/perso/"+micon);
						var icon = URLbase + URLabsolute + "/img_pack/perso/"+micon;
					}
					else{
						var icon = $("geo_icon",xmlItem).text();						
					}
					//alert(icon != "" ? icon : URLbase + URLabsolute + "/img_pack/icone-noire.png");
					//creamos un Gpoint para situar nel o marcador
					var point = new GPoint(lng,lat);
					//alert("bbbbbbbb"+icon);
					//creamos un icono para o marcador
					var icono_categoria = new GIcon();
					icono_categoria.image = (icon != "" ? icon : URLbase + URLabsolute + "/img_pack/icone-noire.png");	
					//alert(icono_categoria.image);
					icono_categoria.shadow = URLbase + URLabsolute + "/img_pack/shadow.png";
					icono_categoria.iconSize = new GSize(24, 38);
					icono_categoria.shadowSize = new GSize(22, 20);	
					icono_categoria.iconAnchor = new GPoint(10, 34);
					icono_categoria.infoWindowAnchor = new GPoint(5, 1);
					//alert("a");
					// creamos el marcador con los datos almacenados en las variables
					var marcador = creaMarcador(point, html, icono_categoria,survol);

					id = id+1;
					// recollemos a informacion que sexa necesaria en distintos arrays, usando como identificador a id do artigo
					marcadores[id] = marcador;
	
					contidosHTML[id] = html;
				
					//URLsons[id] = son;
					//engadimos o marcador ao markerManager antes "map.addOverlay(marker);"
					if (maxZoom) {
						if(idmanager){
							idmanager.addMarker(marcador, minZoom,  maxZoom);
						}
						else{
							markerManager.addMarker(marcador, minZoom,  maxZoom);
						}
					} else if (idmanager){
						idmanager.addMarker(marcador, minZoom);
					}
				}
			
			
		});
	});

}

function abrirVentana(identificador) {
	map.closeInfoWindow();
	marcadores[identificador].openInfoWindowHtml(contidosHTML[identificador]);
	//enbebemos o flash player do son
	var fo = new FlashObject( URLbase + URLabsolute +"/img_pack/musicplayer.swf?autoplay=true&song_url=" + URLsons[identificador], "player_x", "17", "17", "6", "#FFFFFF");
	fo.write("player");
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