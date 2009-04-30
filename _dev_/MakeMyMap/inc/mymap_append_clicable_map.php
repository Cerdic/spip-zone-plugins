<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

function inc_mymap_append_clicable_map_dist($target_id,$target_lat_id,$target_long_id,$view_lat,$view_long, $target_zoom_id=NULL,$view_zoom=NULL,$Marker = false,$url,$id_article,$nb_points_courants){

	global$glongs;
	global $glats;
	global $mymap_ids;
	global $base_site; 

	
	if (!strlen($view_zoom) OR !is_numeric($view_zoom)){
		$view_zoom = isset($GLOBALS['meta']['mymap_default_zoom'])?$GLOBALS['meta']['mymap_default_zoom']:'8'; 
		if (!strlen($view_zoom) OR !is_numeric($view_zoom)) $view_zoom='8';
	}
	if(sizeof($view_lat)==0 OR (sizeof($view_lat)> 0  AND !is_numeric($view_lat[0]))){
		$view_lat[0] = isset($GLOBALS['meta']['mymap_default_lat'])?$GLOBALS['meta']['mymap_default_lat']:'42.7631'; 
		if (!strlen($view_lat[0]) OR !is_numeric($view_lat[0])) $view_lat[0]='42.7631';
	}	
	if(sizeof($view_long)==0 OR (sizeof($view_long)> 0  AND !is_numeric($view_long[0]))){
		$view_long[0] = isset($GLOBALS['meta']['mymap_default_lonx'])?$GLOBALS['meta']['mymap_default_lonx']:'42.7631'; 
		if (!strlen($view_long[0]) OR !is_numeric($view_long[0])) $view_long[0]='42.7631';
	}
	//////////////////////////////////////////////////////////REPERTOIRE DU PLUGIN//////////////////////////////////////////////////////////////////////////////
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));	
	/*define('_DIR_PLUGIN_GMAPAPI', (_DIR_PLUGINS.end($p)));
	define('_NOM_PLUGIN_GMAPAPI', (end($p)));*/
	$ajout_marker_js = "";
	$q=0;
	
	
	/////////////PARCOUR DES POINTS.
	while($q < $nb_points_courants){
			//ON AFFECTE UNE ICONE
			$filename = $GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon".($q+1).".png";
			if (!fopen($filename,"r")){$filename = $GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/correxir.png";}		
			$ajout_marker_js .=
			"point".$q." = 	new GPoint(".$view_long[$q].",".$view_lat[$q].");
			var filename = \"".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon\" + ".($q+1)." + \".png\";
			var icono".$q." = getGicon(filename);
			var Mmarker".$q." = getMarker(point".$q.",icono".$q.",".$mymap_ids[$q].");
			//Liste de marqueurs
			mesMarkers[".$q."] = Mmarker".$q." ;
			formMap.addOverlay(Mmarker".$q.");

  			
				
			";
		$q++;
	}	
	$ajout_marker_js .= "
				/*CLICK ON THE MAP !*/
				GEvent.addListener(formMap, 'click', function(overlay, point){		
					if (point) {
						var nbpts = document.getElementById(\"lesformu\");
						nbpts=nbpts.getElementsByTagName(\"form\").length;
						nbpts++;
						var filename = \"".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon\" + nbpts + \".png\";
						var icono = getGicon(filename);
						var id_inser = $.ajax({
							   type: \"POST\",
							   data: \"x=\"+point.x+\"&y=\"+point.y+\"&id=".$id_article."\",
							   url: \"../spip.php?action=mymap_add_marker\",
							   async: false
							}).responseText;	
						var Mmarker= getMarker(point,icono,id_inser);
						formMap.addOverlay(Mmarker);
						$('#$target_lat_id').val(point.y);
						$('#$target_long_id').val(point.x);				
					ajouter_formulaire(point,id_inser,filename,\"\",\"\");				
					}
				});";		
	$mymap_script_init = charger_fonction('mymap_script_init','inc');
	return 
	$mymap_script_init()
	. "<script type='text/javascript'>
		mesMarkers = new Array();
		/*<![CDATA[*/\n
			if (GBrowserIsCompatible()) {
			/* create the map*/
				var lat=".$GLOBALS['meta']['mymap_default_lat'].";
				var long=".$GLOBALS['meta']['mymap_default_lonx'].";
				var formMap = new GMap2(document.getElementById('$target_id'));
				formMap.addControl(new GLargeMapControl());
				formMap.addControl(new GMapTypeControl());
				formMap.setCenter(new GLatLng(lat,long), ".$view_zoom.", G_NORMAL_MAP);
				formMap.clearOverlays();
				"
	. ($Marker?"
				point = new GPoint(long,lat);
				formMap.addOverlay(new GMarker(point));":"")
  ."
			".$ajout_marker_js
  . ($target_zoom_id?"
				GEvent.addListener(formMap, 'zoomend', function(oldlevel, newlevel){ $('#$target_zoom_id').val(newlevel);});":"")
	."		} else {
				alert('Sorry, the Google Maps API is not compatible with this browser');
			}
		/*]]>*/
		
		//REMPLI LA LISTE DE MARKER A PARTIR DU RESULTAT D'UNE REQUETE AJAX
		function remplir_markers(points){
			var reg = new RegExp(\"[ ,helloworlditstommy]+\", \"g\");
			var listepoint = points.split('helloworlditstommy');
			mesMarkers = new Array();
			document.getElementById(\"lesformu\").innerHTML = ' ';
			for(var i=0;i<listepoint.length;i++){
				var unpts = listepoint[i];						
				var reg2 = new RegExp(\"[ ,qqqq]+\", \"g\");
				var listedata = unpts.split('qqqq');;
				if(listedata[0]!=''){							
					var long = listedata[2] ;
					var lat = listedata[1] ;
					var id_mymap = listedata[0] ;
					var descriptif = listedata[3] ;
					var markerpers = listedata[4] ;
					var q = mesMarkers.length;
					var filename = \"".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon\" + (q+1) + \".png\";	
					var monicone = getGicon(filename);
					var monpoint = new GPoint(long,lat);
					var monMarker = new getMarker(monpoint,monicone,id_mymap);
					mesMarkers[q] = monMarker;
					ajouter_formulaire(monpoint,id_mymap,filename,descriptif,markerpers);
				}					
			}
			update_map();
		}
		//VIRE LES MARKER ET LES REREMPLI EN FONCTION DE mesMarker
		function update_map(){
			var formMap = new GMap2(document.getElementById('$target_id'));
			formMap.clearOverlays();
			formMap.addControl(new GLargeMapControl());
			formMap.addControl(new GMapTypeControl());
			formMap.setCenter(new GLatLng(".$GLOBALS['meta']['mymap_default_lat'].",".$GLOBALS['meta']['mymap_default_lonx']."), ".$view_zoom.", G_NORMAL_MAP);
			formMap.clearOverlays();
			for(var i=0;i<mesMarkers.length;i++){
				formMap.addOverlay(mesMarkers[i]);
			}
			//EVENEMENT LORS DU CLICK SUR LA MAP
			GEvent.addListener(formMap, 'click', function(overlay, point){		
					if (point) {
						var nbpts = document.getElementById(\"lesformu\");
						nbpts=nbpts.getElementsByTagName(\"form\").length;
						nbpts++;
						var filename = \"".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon\" + nbpts + \".png\";
						var icono = getGicon(filename);
						var id_inser = $.ajax({
							   type: \"POST\",
							   data: \"x=\"+point.x+\"&y=\"+point.y+\"&id=".$id_article."\",
							   url: \"../spip.php?action=mymap_add_marker\",
							   async: false
							}).responseText;	
						var Mmarker= getMarker(point,icono,id_inser);
						formMap.addOverlay(Mmarker);
						//formMap.panTo(point);
						$('#$target_lat_id').val(point.y);
						$('#$target_long_id').val(point.x);				
					ajouter_formulaire(point,id_inser,filename,\"\",\"\");				
					}
			});		
		}
		
		//GENERE UNE ICONE EN FONCTION D'UN NOM DE FICHIER
		function getGicon(filename){
			var icono = new GIcon(G_DEFAULT_ICON);
			icono.image = filename;
			icono.iconSize = new GSize(24, 38);
			icono.iconAnchor = new GPoint(12, 38);
			icono.shadow =\"". $GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/shadow.png\";
			return icono;
		}
		
		//RETOURNE UN MARKER EN FONCTION D'UN POINT
		function getMarker(monpoint,monicone,id_mymap){
			var options = { icon: monicone, draggable: true };
			var monMarker = new GMarker(monpoint,options);
					GEvent.addListener(monMarker, 'click', function() {
							if (confirm('Supprimer ce point?')) {
								var points =$.ajax({
											   type: \"POST\",
											   data: \"id_mymap=\"+id_mymap+\"&id_article=".$id_article."\",
											   url:  \"../spip.php?action=mymap_del_marker\",
											   async: false
										}).responseText;

								//window.location = \"".$url."&delete=delete&id_mymap=".$mymap_ids[$q]."\"; 
								remplir_markers(points);
							}
						});		
					GEvent.addListener(monMarker, 'dragend', function() {
						var ncoords = monMarker.getPoint();
						 update_coordonnes(id_mymap,ncoords.lat(),ncoords.lng());						
					});		
			return monMarker;
		}
		
		/////////////MISE A JOUR COORDONNES D'UN POINT////////////
		function update_coordonnes(id_mymap,lat,long){
			$.ajax({
				   type: 'POST',
				   data: 'glat='+lat+'&glonx='+long+'&id='+id_mymap,
				   url:  '../spip.php?action=mymap_up_marker_coord',
				   async: true
				}).responseText;
				/*alert(document.getElementById('cadre_mymap_'+id_mymap).childNodes.length);*/
				if(document.getElementById('cadre_mymap_'+id_mymap).childNodes[1]){
				var node = document.getElementById('cadre_mymap_'+id_mymap).childNodes[1];
				}else{
				var node = document.getElementById('cadre_mymap_'+id_mymap).childNodes[0];	
				}
				for(var i=0;i<node.childNodes.length;i++){	
					if(node.childNodes[i].id){
						if(node.childNodes[i].id=='form_long'){
							node.childNodes[i].value=long ;
						}
						if(node.childNodes[i].id=='form_lat'){
							node.childNodes[i].value=lat ;
						}
					}			
				}
		}
		//INSERE DANS LA BASE LES COORDONNES POUR CENTRER LA CARTE
		function setMapCenter(){
			var center = formMap.getCenter();
			var response =$.ajax({
								   type: \"POST\",
								   data: \"lng=\"+center.lng()+\"&lat=\"+center.lat()+\"&zoom=\"+formMap.getZoom()+\"&id_article=".$id_article."\",
								   url:  \"../spip.php?action=mymap_set_center\",
								   async: false
							}).responseText;
			}
		
				//trouve les coordonnees d une destination
		function findDestination(){
			 var geocoder = new GClientGeocoder();
			 var fromAddress = document.getElementById('destination').value;
			 geocoder.getLatLng(fromAddress, function(point) {
				if (point) {
					var nbpts = document.getElementById(\"lesformu\");
					nbpts=nbpts.getElementsByTagName(\"form\").length;
					nbpts++;
					var filename = \"".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon\" + nbpts + \".png\";
					var icono = getGicon(filename);
					var id_inser = $.ajax({
						   type: \"POST\",
						   data: \"x=\"+point.x+\"&y=\"+point.y+\"&id=".$id_article."\",
						   url: \"../spip.php?action=mymap_add_marker\",
						   async: false
						}).responseText;	
					var Mmarker= getMarker(point,icono,id_inser);
					formMap.addOverlay(Mmarker);
					$('#$target_lat_id').val(point.y);
					$('#$target_long_id').val(point.x);				
					ajouter_formulaire(point,id_inser,filename,\"\",\"\");	
					formMap.setCenter(new GLatLng(point.y,point.x));	
					formMap.setZoom(12);
					document.getElementById('destination').value ='';
				} 
				else {
				  alert (fromAddress + ' est inconnu!');
				}
			});
		}
		
		///////AJOUTE UN FORMUALIRE A PARTIR D'UN POINT ET DE L'ID_MYMAP DU POINT
		function ajouter_formulaire(point,id_mymap,filename,descriptif,marker){
			var param1='choix_logo'+id_mymap;
			var formu = '<div class=\"cadre-r\" id=\"cadre_mymap_'+id_mymap+'\"><form id=\"formulaire_coordenadas\" name=\"formulaire_coordenadas\" action=\"".$url."&actualizar=true\" method=\"post\"><a href=\"#choix_logo'+id_mymap+'\" onclick=\"return showPersoCursor(\''+param1+'\',\''+id_mymap+'\');\"><img src=\"'+filename+'\" alt=\"marker\" /></a><input type=\"hidden\" class=\"fondl\" name=\"mark\" id=\"form_mark'+id_mymap+'\" value=\"'+marker+'\" /><input type=\"hidden\" name=\"mymap_id\" id=\"mymap_id\" value=\"'+id_mymap+'\" /><input type=\"text\" name=\"lat\"id=\"form_lat\" class=\"fondl\" value=\"'+point.y+'\" /><input type=\"text\" name=\"lonx\" class=\"fondl\" id=\"form_long\" value=\"'+point.x+'\" /><input type=\"submit\" class=\"fondo\" value=\"actualiser\" name=\"actualizar\" style=\"display:none;\" /><button onClick=\'updateGeomarker(this.parentNode);return false;\' class=\"fondo\">enregistrer</button><br /><div id=\"cursPerso'+id_mymap+'\" style=\"float:left;width:24px;height:38px;background-image:url(".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/perso/'+marker+')\">&nbsp;</div><textarea class=\"formo\" name=\"desc_mymap\" id=\"desc_mymap\" style=\"margin-left:28px;width:380px;\">'+descriptif+'</textarea><div id=\"choix_logo'+id_mymap+'\" class=\"cadre-r\" name=\"choix_logo'+id_mymap+'\" style=\"display:none;margin-left:28px;width:380px;\" >&nbsp;</div></form></div>';
			document.getElementById(\"lesformu\").innerHTML += formu ;
		}

	</script>";
}
?>