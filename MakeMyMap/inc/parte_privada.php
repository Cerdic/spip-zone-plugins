<?php
/*
 * Spip mymap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */
include_spip('base/abstract_sql');
global$glongs;
global $glats;
global $mymap_ids;
 
function mymap_cambiar_coord($id_article) {
	global $spip_lang_left, $spip_lang_right;
	global$glongs;
	global $glats;
	global $mymap_ids;	
	global $mymap_descs;

	//////////////////////////////////////////////////////////REPERTOIRE//////////////////////////////////////////////////////////////////////////////////////////////////////
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));	
	define('_DIR_PLUGIN_MYMAP', (_DIR_PLUGINS.end($p)));
	define('_NOM_PLUGIN_MYMAP', (end($p)));
	define('_ABSOLUTE_DIR_PLUGIN_MYMAP', str_replace("../","",_DIR_PLUGIN_MYMAP));
	
	$id_article = _request(id_article);
	
	$glat = NULL;
	$glonx = NULL;
	$mapa = "";
	
	///////////LES POINTS/////////
	$glats = array();//LATITUDES
	$glongs = array();//LOnGITUDES
	$mymap_ids = array();//IDENTIFIANTS
	$mymap_descs = array();//DESCRIPTIFS
	$gmark = array();//ICONES
	
	if(_request('actualizar')){
	

	
		$glat = _request('lat');
		$glonx = _request('lonx');
		$desc = _request('desc_mymap');
		$mark = _request('mark_mymap');
		$result= spip_query("SELECT * FROM spip_mymap WHERE id_mymap = '" . _request('mymap_id')."'");
		$row = spip_fetch_array($result);
		if (!$row){
			spip_abstract_insert("spip_mymap", "(id_article, lat, lonx)",
								"(" . _q($id_article) .","._q($glat)." ,"._q($glonx).","._q($desc_mymap).")");
		}
		else{
			spip_query("UPDATE spip_mymap SET lat="._q($glat).", lonx="._q($glonx)." , descriptif="._q($desc)." WHERE id_mymap = '" . _request('mymap_id')."'");
		}
	}
	if(_request('delete')){
		spip_query("DELETE FROM spip_mymap WHERE id_mymap='"._request('id_mymap')."'");
	}
	////////RECUPERATION DES POINTS/////////
	$result= spip_query("SELECT * FROM spip_mymap WHERE id_article = " . intval($id_article));
	while ($row = spip_fetch_array($result)){
		array_push($glats,$row['lat']);
		array_push($glongs,$row['lonx']);
		array_push($mymap_ids,$row['id_mymap']);
		array_push($gmark,$row['marker']);
		array_push($mymap_descs,$row['descriptif']);
	}	
	
	
	if ($glat!==NULL){
		$mymap_append_view_map = charger_fonction('mymap_append_view_map','inc');
		$mapa = "<div id='map' name='map' style='width: 470px; height: 100px; border:1px solid #000'></div>"
		  .$mymap_append_view_map('map',$glat,$glonx,NULL,array(array('lon'=>$glonx,'lat'=>$glat)));
	}

$s .= "";

//SI ON EST DANS UN ARTICLE QUI A LE MOT-CLE PLAN
$modifier_flux = true;
$query ='SELECT id_article FROM spip_mymap_articles WHERE id_article ='.$id_article ;
$requete= spip_query($query);
if(mysql_num_rows($requete)==0){
		$modifier_flux = false;
}
if($modifier_flux){
		// Ajouter un formulaire
		$s .= "\n<p>";
		$s .= debut_cadre_enfonce(_DIR_PLUGIN_MYMAP."img_pack/correxir.png",true,"",bouton_block_depliable(_T('mymap:cambiar'),true,"ajouter_form"));
		$s .= debut_block_depliable(true,"ajouter_form");
		$s .= "<div class='verdana2'>";
		$s .= _T("mymap:clic_mapa");
		$s .= "</div>";
		$s .= "<div class='cadre-r' style='padding:5px;'>";
		$s.= "<label>Chercher un lieu&nbsp;&nbsp;&nbsp;</label><input name='destination' id='destination' class='fondl' value=''/>&nbsp;<button class=\"fondo\" onclick=\"findDestination();return false;\">Chercher</button>";
		$s .= '</div>';
		$s .= debut_block_depliable(true,"ajouter_form");
		
		$s .= '<div style="text-align:center"><button class="fondo" onclick="setMapCenter();return false;">'._T("mymap:centrer").'</button></div>';
				
		$mymap_append_clicable_map = charger_fonction('mymap_append_clicable_map','inc');
		
		//$q=0;
		$s .= "<div id='cadroFormulario' style='border:1px solid #000'>
		<div id='formMap' name='formMap' style='width: 470px; height: 350px'></div>"
		. $mymap_append_clicable_map('formMap','form_lat','form_long',$glats,$glongs,NULL,NULL,$row?true:false,$_SERVER['REQUEST_URI'],$id_article,sizeof($glats));
		//}
		// Formulario para actualizar as coordenadas do mapa______________________.
		$qq=0;
		
		/////FONCTION JS POUR ACTUALISER LES POINTS
		$s.='<script type="text/javascript">
	
			
			//AFFICHE L IMAGE POUR PATIENTER
			function wait(){
				document.getElementById("wait").style.display="block";
			}
			//ENLEVE L IMAGE POUR PATIENTER
			function unWait(){
				document.getElementById("wait").style.display="none";
			}
			//MONTRE LA FENTRE POUR PERSONALISER LE CURSEUR
			function showPersoCursor(id_elem,id_mymap){
				var icosrc = "ico"+id_elem;
				var selectsrc = "select"+id_elem;
				/*document.getElementById(id_elem).style.display="block";*/
				document.getElementById(id_elem).innerHTML="<h2>Personnaliser l\'icone</h2><br /><ul style=\'list-style-type:none;margin:0;padding:0;\'>';
				
				$icofold = dirname(__FILE__)."/../img_pack/perso/";
				$dossier = opendir(dirname(__FILE__)."/../img_pack/perso/");
				$zz=0;
				while ($Fichier = readdir($dossier)) {			
					if ($Fichier != "." && $Fichier != ".." && $Fichier != ".svn") {
						$zz++;
						$s.="<li style='text-align:center;float:left;padding:5px;margin-bottom:5px;'><a href='#' style='display:block' onclick='upwico(".'\"'.$Fichier.'\"'.",".'\"'."\"+id_mymap+\"".'\"'.",this.parentNode,true);return false;'><img id='\"+id_mymap+\"filename".$Fichier."' src='".$GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/perso/".$Fichier."' alt='icone' /><br />Selectionner</a></li>";
						if($zz%4==0){
							$s.="<p style='clear:both;'>&nbsp;</p>";
						}
					}
					
				}			
				closedir($dossier);
		$s.='</ul><p style=\"clear:both;\">&nbsp;</p>";';
		$s.='if(document.getElementById(id_elem).style.display!="block"){document.getElementById(id_elem).style.display="block";	}else{document.getElementById(id_elem).style.display="none";}
			/*alert(document.getElementById("form_mark"+id_mymap).value);*/
					if(document.getElementById("form_mark"+id_mymap).value!=""){					/*document.getElementById(id_mymap+"filename"+document.getElementById("form_mark"+id_mymap).value).parentNode.parentNode.className="fond1";*/					upwico(document.getElementById("form_mark"+id_mymap).value,"\'"+id_mymap+"\'",document.getElementById(id_mymap+"filename"+document.getElementById("form_mark"+id_mymap).value).parentNode.parentNode,false);
				}
				return false;
			}
			//INSERE DANS LA BASE L ICONE MYMAP PERSO ET ENCADRE L ICONE CONCERNEE
			function upwico(file,id_mymap,elemaencadrer,updb){
				/*alert(id_mymap);*/
				if(updb){
					$.ajax({
					type:"POST",
					data: "marker="+file+"&id="+id_mymap,
					url:"../spip.php?action=mymap_up_marker_from_ico",
					async: false}).responseText;
				}
				
				// la ul
				var liste = elemaencadrer.parentNode;
				//tout les li
				var lis=liste.getElementsByTagName("li");
				for(var i=0;i<lis.length;i++){
						lis[i].className="";
				}
				elemaencadrer.className="fondl";
				if(document.getElementById("form_mark"+id_mymap)){
				document.getElementById("form_mark"+id_mymap).value = file;}

				if(document.getElementById("cursPerso"+id_mymap)){
				var file = "url('.$GLOBALS['meta']['adresse_site'].'/'._ABSOLUTE_DIR_PLUGIN_MYMAP.'img_pack/perso/"+file+")";
				document.getElementById("cursPerso"+id_mymap).style.backgroundImage = file;
				}
				if(updb){
					elemaencadrer.parentNode.parentNode.style.display="none";
				}
			}
			

			
		
			function updateGeomarker(node){		
				//wait();
				for(var i=0;i<node.childNodes.length;i++){	
					if(node.childNodes[i].id){
						if(node.childNodes[i].id=="form_long"){
							var long = node.childNodes[i].value ;
						}
						if(node.childNodes[i].id=="form_lat"){
							var lat = node.childNodes[i].value ;
						}
						if(node.childNodes[i].id=="mymap_id"){
							var id = node.childNodes[i].value ;
						}
						if(node.childNodes[i].id=="desc_mymap"){
							var desc = node.childNodes[i].value ;
						}	
					}			
				}
				
				$.ajax({
						   type: "POST",
						   data: "glat="+lat+"&glonx="+long+"&id="+id+"&desc="+desc+"",
						   url:  "../spip.php?action=mymap_up_marker",
						   async: true
						}).responseText;
				//unWait();
			}
			</script>'; 
		$s.='<div id="lesformu" class="cadre-padding">';
		while($qq < sizeof($glats)){		
		//L'ICONE LIEE AU POINT
		$filename = $GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/icon".($qq+1).".png";
		//echo $filename;
		if (!fopen($filename,"r")){$filename = $GLOBALS['meta']['adresse_site']."/"._ABSOLUTE_DIR_PLUGIN_MYMAP."img_pack/correxir.png";}
		$s .= '<div class="cadre-r" id="cadre_mymap_'.$mymap_ids[$qq].'"><img id="wait" src="'.$GLOBALS['meta']['adresse_site'].'/'._ABSOLUTE_DIR_PLUGIN_MYMAP.'img_pack/loadingAnimation.gif" alt="searching" style="position:fixed;top:50%;left:50%;display:none;width=100px;height=100px;"/><form onSubmit="return false;" id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire(articles."&id_article=".$id_article).'" method="post" >
				<a href="#choix_logo'.$mymap_ids[$qq].'" onclick="return showPersoCursor(\'choix_logo'.$mymap_ids[$qq].'\',\''.$mymap_ids[$qq].'\');"><img src="'.$filename.'" alt="marker" /></a><input type="hidden" name="mymap_id" id="mymap_id" value="'.$mymap_ids[$qq].'" />
				<input type="text" class="fondl" name="lat" id="form_lat" value="'.$glats[$qq].'" />
				<input type="text" class="fondl" name="lonx" id="form_long" value="'.$glongs[$qq].'" />
				<input type="hidden" class="fondl" name="mark" id="form_mark'.$mymap_ids[$qq].'" value="'.$gmark[$qq].'" />
				<input type="submit" class="fondo" name="actualizar" value="'._T("mymap:boton_actualizar").'" style="display:none;" />
				<button class="fondo" onClick=\'updateGeomarker(this.parentNode);return false;\'>enregistrer</button>
				<br />		
				<div id="cursPerso'.$mymap_ids[$qq].'" style="float:left;width:24px;height:38px;background-image:url('.$GLOBALS['meta']['adresse_site'].'/'._ABSOLUTE_DIR_PLUGIN_MYMAP.'img_pack/perso/'.$gmark[$qq].')">&nbsp;</div>
				<textarea class="formo" name="desc_mymap" id="desc_mymap" style="margin-left:28px;width:380px;">'.$mymap_descs[$qq].'</textarea>
				<div id="choix_logo'.$mymap_ids[$qq].'" class="cadre-r" name="choix_logo'.$mymap_ids[$qq].'" style="display:none;margin-left:28px;width:80%;">&nbsp;</div>
			   </form></div>';
				$qq++;
		}
		$s.='</div>';//FIN DIV "lesformu"
		$s .= '</div>';
		$s .= $mapa;
		$s .= fin_block();
		$s .= fin_block();
		$s .= fin_cadre_enfonce(true);
}
	$s .= "\n<p>";
	return $s;
}

function mymap_mots($id_mot) {

	return $s;
}
 

	
?>