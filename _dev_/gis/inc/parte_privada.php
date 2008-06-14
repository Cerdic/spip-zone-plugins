<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz�lez, Berio Molina
 * (c) 2007 - Distribu�do baixo licencia GNU/GPL
 *
 */
include_spip('base/abstract_sql');
 
function gis_cambiar_coord($id,$table,$exec) {
	global $spip_lang_left, $spip_lang_right;
	
	$pkey = id_table_objet($table);
	
	$glat = NULL;
	$glonx = NULL;
	$gicon = NULL;
	$mapa = "";
	$result= spip_query("SELECT * FROM spip_gis WHERE $pkey = " . intval($id));
	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
	}
	if(_request('actualizar')){
		$glat = _request('lat');
		$glonx = _request('lonx');
		if (!$row)
			spip_abstract_insert("spip_gis", "($pkey, lat, lonx)", "(" . _q($id) .","._q($glat)." ,"._q($glonx).")");
		else
			spip_query("UPDATE spip_gis SET lat="._q($glat).", lonx="._q($glonx)."  WHERE $pkey = " . _q($id));
	}
	if ($glat!==NULL){
		$resultMots = spip_query("SELECT * FROM spip_mots_$table WHERE $pkey = ".intval($id));
		while ($rowMot = spip_fetch_array($resultMots)) {
			$resultMotIcon = spip_query("SELECT * FROM spip_mots WHERE type ='marker_icon' AND id_mot=".$rowMot['id_mot']);
			if ($rowMotIcon = spip_fetch_array($resultMotIcon)){
				if (file_exists("../IMG/"."moton".$rowMot['id_mot'].".png")) {
  				  	$gicon = "moton".$rowMot['id_mot'].".png";
				} else if (file_exists("../IMG/"."moton".$rowMot['id_mot'].".gif")) {
					$gicon = "moton".$rowMot['id_mot'].".gif";
				}
			}
		}
		$gis_append_view_map = charger_fonction('geomap_append_view_map','inc');
		$mapa = "<div id='viewMap' name='viewMap' style='width: 470px; height: 100px; border:1px solid #000'></div>"
		  .$gis_append_view_map('viewMap',$glat,$glonx,null,array(array('lonx'=>$glonx,'lat'=>$glat)),$gicon);
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
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire($exec,"$pkey=".$id).'" method="post">
		<input type="text" name="lat" id="form_lat" value="'.$glat.'" />
		<input type="text" name="lonx" id="form_long" value="'.$glonx.'" />
		<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" />
		</form>
		</div>';
	$s .= $mapa;
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	$s .= "\n</p>";
	return $s;
}
/*
function gis_mot_groupe($id_groupe){
	global $spip_lang_left, $spip_lang_right;
	global $id_groupe;
	$s .= "hola gis mot groupe";
	return $s;
}*/
function gis_mots($id_mot) {
	global $spip_lang_left, $spip_lang_right;
	
	$glat = NULL;
	$glonx = NULL;
	$result = spip_query("SELECT * FROM spip_gis_mots WHERE id_mot = " . intval($id_mot));
	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
	}
	if(_request('actualizar')){
		$glat = _request('lat');
		$glonx = _request('lonx');
		if (!$row)
			spip_abstract_insert("spip_gis_mots", "(id_mot, lat, lonx)", "(" . _q($id_mot) .", "._q($glat)." ,"._q($glonx).")");
		else
			spip_query("UPDATE spip_gis_mots SET lat="._q($glat).", lonx="._q($glonx)."  WHERE id_mot = " . _q($id_mot));
	}
	//$s .= "\n<p>";
	$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
	$s .= bouton_block_invisible("ajouter_form");
	$s .= "&nbsp;&nbsp;&nbsp;<strong class='verdana3' style='text-transform: uppercase;'>"
		._T("gis:cambiar")." ".$id_mot." <a onclick=\"$('#cadroFormulario').slideToggle('slow')\">("._T('gis:clic_desplegar').")</a></strong>";
	$s .= "\n";
			$s .= debut_block_visible("ajouter_form");
	$s .= "<div class='verdana2'>";
	$s .= _T("gis:clic_mapa");
	$s .= "</div>";
	
	$s .= debut_block_visible("ajouter_form");
	
	$gis_append_mini_map = charger_fonction('geomap_append_mini_map','inc');
	
	$s .= "<div id='cadroFormulario' style='border:1px solid #000'>
	<div id='formMap' name='formMap' style='width: 180px; height: 180px'></div>"
	. $gis_append_mini_map('formMap','form_lat','form_long',$glat,$glonx,NULL,NULL,$row?true:false);
	
	// Formulario para actualizar as coordenadas do mapa______________________.
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire("mots_edit","&id_mot=".$id_mot).'" method="post">
		<input type="text" name="lat" id="form_lat" value="'.$glat.'" />
		<input type="text" name="lonx" id="form_long" value="'.$glonx.'" />
		<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" />
		</form>
		</div>';
	//$s .= debut_block_visible("ajouter_form");
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	//$s .= "\n</p>";
	return $s;
}
 

	
?>
