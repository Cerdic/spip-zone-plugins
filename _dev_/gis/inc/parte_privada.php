<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */
include_spip('base/abstract_sql');
 
function gis_cambiar_coord($id_article) {
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;
	global $id_article;
	
	$result= spip_query("SELECT * FROM spip_gis WHERE id_article = " . intval($id_article));

	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
		
		if(isset($_POST['actualizar'])){
			$glat = $_POST['lat'];
			$glonx = $_POST['lonx'];
			spip_query("UPDATE spip_gis SET lat='".$glat."', lonx='".$glonx."'  WHERE id_article = '" . $id_article."'");
		}
		$mapa = "<div id='map' name='map' style='width: 470px; height: 100px; border:1px solid #000'></div>
		<script type='text/javascript'>
		/*<![CDATA[*/\n
		if (GBrowserIsCompatible()) {
		/* create the map*/
			var map = new GMap2(document.getElementById('map'));
			map.setCenter(new GLatLng(".$glat.",".$glonx."), 8, G_MAP_TYPE);
			icono = new GIcon();
			icono.image = \""._DIR_PLUGIN_GIS."img_pack/correxir.png\";
			icono.shadow = \"http://www.escoitar.org/loudblog/custom/templates/berio/shadow.png\";
			icono.iconSize = new GSize(20, 34);
			icono.shadowSize = new GSize(22, 20);
			icono.iconAnchor = new GPoint(10, 34);
			icono.infoWindowAnchor = new GPoint(5,1);
			point = new GPoint(".$glonx.",".$glat.");
			marker = new GMarker(point, icono);
			map.addOverlay(marker);
		} else {
			alert('Sorry, the Google Maps API is not compatible with this browser');
		}
		/*]]>*/
	</script>";
	} else {
		$glat = '42.7631';
		$glonx = '-7.9321';
		$mapa = "";
		if(isset($_POST['actualizar'])){
			$glat = $_POST['lat'];
			$glonx = $_POST['lonx'];
			spip_abstract_insert("spip_gis", "(id_article, lat, lonx)", "(" . $id_article .",".$glat." ,".$glonx.")");
			$mapa = "<div id='map' name='map' style='width: 470px; height: 100px; border:1px solid #000'></div>
		<script type='text/javascript'>
		/*<![CDATA[*/\n
		if (GBrowserIsCompatible()) {
		/* create the map*/
			var map = new GMap2(document.getElementById('map'));
			map.setCenter(new GLatLng(".$glat.",".$glonx."), 8, G_MAP_TYPE);
			icono = new GIcon();
			icono.image = \""._DIR_PLUGIN_GIS."img_pack/correxir.png\";
			icono.shadow = \"http://www.escoitar.org/loudblog/custom/templates/berio/shadow.png\";
			icono.iconSize = new GSize(20, 34);
			icono.shadowSize = new GSize(22, 20);
			icono.iconAnchor = new GPoint(10, 34);
			icono.infoWindowAnchor = new GPoint(5,1);
			point = new GPoint(".$glat.",".$glonx.");
			marker = new GMarker(point, icono);
			map.addOverlay(marker);
		} else {
			alert('Sorry, the Google Maps API is not compatible with this browser');
		}
		/*]]>*/
	</script>";
		}
	}
	
	$s .= "";
	// Ajouter un formulaire
	$s .= "\n<p>";
	$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
	$s .= bouton_block_invisible("ajouter_form");
	$s .= "&nbsp;&nbsp;&nbsp;<strong class='verdana3' style='text-transform: uppercase;'>"
		._T("gis:cambiar")." <a onclick=\"$('#cadroFormulario').slideToggle('slow')\">("._T('gis:clic_desplegar').")</a></strong>";
	$s .= "\n";
			$s .= debut_block_visible("ajouter_form");
	$s .= "<div class='verdana2'>";
	$s .= _T("gis:clic_mapa");
	$s .= "</div>";
	
	$s .= debut_block_visible("ajouter_form");
	$s .= "<div id='cadroFormulario' style='border:1px solid #000'>
	<div id='formMap' name='formMap' style='width: 470px; height: 350px'></div>
	<script type='text/javascript'>
		/*<![CDATA[*/\n
		if (GBrowserIsCompatible()) {
		/* create the map*/
			var formMap = new GMap2(document.getElementById('formMap'));
			formMap.addControl(new GLargeMapControl());
			formMap.addControl(new GMapTypeControl());
			formMap.setCenter(new GLatLng(".$glat.",".$glonx."), 8, G_MAP_TYPE);
			/* creamos el evento para crear nuevos marcadores*/
			GEvent.addListener(formMap, 'click', function(overlay, point){
				formMap.clearOverlays();
				if (point) {
					formMap.addOverlay(new GMarker(point));
					formMap.panTo(point);
					document.forms.formulaire_coordenadas.lat.value = point.y;
					document.forms.formulaire_coordenadas.lonx.value = point.x;
				}
			});
		} else {
			alert('Sorry, the Google Maps API is not compatible with this browser');
		}
		/*]]>*/
	</script>";          
	
	// Formulario para actualizar as coordenadas do mapa______________________.
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire(articles."&id_article=".$id_article).'" method="post">
		<input type="text" name="lat" value="" />
		<input type="text" name="lonx" value="" />
		<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" />
		</form>
		</div>';
	$s .= $mapa;
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	$s .= "\n<p>";
	return $s;
}
 
function gis_mot_groupe($id_groupe){
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;
	global $id_groupe;
	$s .= "hola";
	return $s;
} 
function gis_grupo_mots($id_groupe) {
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;
	global $id_groupe;
	
	$s .= "\n<p>";
	$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
	$s .= bouton_block_invisible("ajouter_form");
	$s .= debut_block_visible("ajouter_form");
	$s .= "hola ".$id_groupe;
	
	if(isset($_POST['actualizar'])){
		if ((isset($_POST['grupo'])) && ($_POST['grupo']!=$_POST['exgrupo'])) {
			if ($_POST['exgrupo']!=0) {
				spip_query("DELETE FROM spip_gis_mots_groupes WHERE id_groupe = " . intval($id_groupe));
			}
			if ($_POST['grupo']!=0) {
				spip_abstract_insert("spip_gis_mots_groupes", "(id_gis_groupe, id_groupe)", "( ".$_POST['grupo'].",".$id_groupe.")");
			}			
		}
	}
	$s .= '<form id="formulaire_gruposgis" name="formulaire_gruposgis" action="'.generer_url_ecrire(mots_type."&id_groupe=".$id_groupe).'" method="post">';
	
	$grupo = 0;
	$result = spip_query("SELECT * FROM spip_gis_mots_groupes WHERE id_groupe = " . intval($id_groupe));
	if ($row = spip_fetch_array($result)){
	 	$grupo = $row['id_gis_groupe'];
	 	$s .= '<input type="radio" name="grupo" value="0" /> Sacar do grupo<br>';
	}
	
	$result= spip_query("SELECT * FROM spip_gis_groupes");
	while($row = spip_fetch_array($result)) {
		$s .= '<input type="radio" name="grupo" value="'.$row['id_groupe'];
		if ($row['id_groupe']==$grupo) {
			$s .= ' checked="checked"';
		}
		$s .= '" /> '.$row['titre']."<br>";
	}
	$s .= '<input type="hidden" name="exgrupo" value="'.$grupo.'" /><input type="submit" name="actualizar" value="actualizar" />
		</form>';		
	$s .= debut_block_visible("ajouter_form");
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	$s .= "\n<p>";
	return $s;
}
 

	
?>