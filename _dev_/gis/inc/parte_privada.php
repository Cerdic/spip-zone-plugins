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
	global $spip_lang_left, $spip_lang_right;
	global $id_article;
	
	$glat = NULL;
	$glonx = NULL;
	$mapa = "";
	$result= spip_query("SELECT * FROM spip_gis WHERE id_article = " . intval($id_article));
	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
	}
	if(_request('actualizar')){
		$glat = _request('lat');
		$glonx = _request('lonx');
		if (!$row)
			spip_abstract_insert("spip_gis", "(id_article, lat, lonx)", "(" . _q($id_article) .","._q($glat)." ,"._q($glonx).")");
		else
			spip_query("UPDATE spip_gis SET lat="._q($glat).", lonx="._q($glonx)."  WHERE id_article = " . _q($id_article));
	}
	if ($glat!==NULL){
		$gis_append_view_map = charger_fonction('geomap_append_view_map','inc');
		$mapa = "<div id='map' name='map' style='width: 470px; height: 100px; border:1px solid #000'></div>"
		  .$gis_append_view_map('map',$glat,$glonx,NULL,array(array('lon'=>$glonx,'lat'=>$glat)));
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
	
	$gis_append_clicable_map = charger_fonction('geomap_append_clicable_map','inc');
	
	$s .= "<div id='cadroFormulario' style='border:1px solid #000'>
	<div id='formMap' name='formMap' style='width: 470px; height: 350px'></div>"
	. $gis_append_clicable_map('formMap','form_lat','form_long',$glat,$glonx,NULL,NULL,$row?true:false);
	
	// Formulario para actualizar as coordenadas do mapa______________________.
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire(articles."&id_article=".$id_article).'" method="post">
		<input type="text" name="lat" id="form_lat" value="'.$glat.'" />
		<input type="text" name="lonx" id="form_long" value="'.$glonx.'" />
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
	global $spip_lang_left, $spip_lang_right;
	global $id_groupe;
	$s .= "hola";
	return $s;
} 
function gis_grupo_mots($id_groupe) {
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