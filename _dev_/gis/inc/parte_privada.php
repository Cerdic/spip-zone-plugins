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
 
function gis_cambiar_coord($id,$table,$exec) {
	global $spip_lang_left, $spip_lang_right;
	
	$pkey = id_table_objet($table);
	
	$glat = NULL;
	$glonx = NULL;
	$gzoom = NULL;
	$gicon = NULL;
	$mapa = "";
	$result= spip_query("SELECT * FROM spip_gis WHERE $pkey = " . intval($id));
	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
		$gzoom = $row['zoom'];
	}
	if(_request('actualizar')){
		$glat = _request('lat');
		$glonx = _request('lonx');
		$gzoom = _request('zoom');
		if (!$row)
			spip_abstract_insert("spip_gis", "($pkey, lat, lonx, zoom)", "("._q($id) .", "._q($glat).", "._q($glonx).", "._q($gzoom).")");
		else
			spip_query("UPDATE spip_gis SET lat="._q($glat).", lonx="._q($glonx).", zoom="._q($gzoom)."  WHERE $pkey = " . _q($id));
	}
	if ($glat!==NULL){
		$resultMots = spip_query("SELECT * FROM spip_mots_{$table}s WHERE $pkey = ".intval($id));
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
		if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
			$gis_append_view_map = charger_fonction($GLOBALS['meta']['gis_map'].'_append_view_map','inc');
			$mapa = '<div id="viewMap" name="viewMap" style="width: 470px; height: 100px; border:1px solid #000"></div>';
		  	$mapa .= $gis_append_view_map('viewMap',$glat,$glonx,$zoom,array(array('lonx'=>$glonx,'lat'=>$glat)),$gicon);
		} else {
			$mapa = '<div>' . _T('gis:falta_plugin') . '</div>';
		}
		
	}
	
	$s .= '';
	// Ajouter un formulaire
	$s .= '<p>';
	$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
	$s .= bouton_block_invisible("ajouter_form");
	$s .= '&nbsp;&nbsp;&nbsp;<strong class="verdana3" style="text-transform: uppercase;">' . _T('gis:cambiar') . ' <a onclick="$(\'#cadroFormulario\').slideToggle(\'slow\')">(' . _T('gis:clic_desplegar') . ')</a></strong>';
	$s .= debut_block_visible("ajouter_form");
	$s .= '<div class="verdana2">' . _T("gis:clic_mapa") . '</div>';
	$s .= debut_block_visible("ajouter_form");
	$s .= '<div id="cadroFormulario" style="border:1px solid #000">';
	
	if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
		$gis_append_clicable_map = charger_fonction($GLOBALS['meta']['gis_map'].'_append_clicable_map','inc');
		$s .= '<div id="formMap" name="formMap" style="width: 470px; height: 350px"></div>';
		$s .= $gis_append_clicable_map('formMap','form_lat','form_long',$glat,$glonx,'form_zoom',$gzoom,$row?true:false);
	} else {
		$s .= '<div>' . _T('gis:falta_plugin') . '</div>';
	}
		
	// Formulario para actualizar as coordenadas do mapa______________________.
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire($exec,"$pkey=".$id).'" method="post">
		<label>'._T('gis:lat').': </label><input type="text" name="lat" id="form_lat" value="'.$glat.'" size="12" />
		<label>'._T('gis:long').': </label><input type="text" name="lonx" id="form_long" value="'.$glonx.'" size="12" />
		<label>'._T('gis:zoom').': </label><input type="text" name="zoom" id="form_zoom" value="'.$gzoom.'" size="6" />
		<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" />
		</form>
		</div>';
	$s .= $mapa;
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	$s .= '</p>';
	return $s;
}

function gis_mots($id_mot) {
	global $spip_lang_left, $spip_lang_right;
	$glat = NULL;
	$glonx = NULL;
	$gzoom = NULL;
	$result = spip_query("SELECT * FROM spip_gis_mots WHERE id_mot = ".intval($id_mot));
	if ($row = spip_fetch_array($result)){
		$glat = $row['lat'];
		$glonx = $row['lonx'];
		$gzoom  = $row['zoom'];
	}
	if(_request('actualizar')){
		$glat = _request('lat');
		$glonx = _request('lonx');
		$gzoom = _request('zoom');
		if (!$row)
			spip_abstract_insert("spip_gis_mots", "(id_mot, lat, lonx, zoom)", "("._q($id_mot).", "._q($glat).", "._q($glonx).", "._q($gzoom).")");
		else
			spip_query("UPDATE spip_gis_mots SET lat="._q($glat).", lonx="._q($glonx).", zoom="._q($gzoom)."  WHERE id_mot = "._q($id_mot));
	}
	
	$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
	$s .= bouton_block_invisible("ajouter_form");
	$s .= '&nbsp;&nbsp;&nbsp;<strong class="verdana3" style="text-transform: uppercase;">'. _T("gis:cambiar") .' '. $id_mot .' <a onclick="$(\'#cadroFormulario\').slideToggle(\'slow\')">('. _T('gis:clic_desplegar') .')</a></strong>';
	$s .= debut_block_visible("ajouter_form");
	$s .= '<div class="verdana2">'. _T("gis:clic_mapa") .'</div>';
	$s .= debut_block_visible("ajouter_form");
	$s .= '<div id="cadroFormulario" style="border:1px solid #000">';
	
	if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
		$gis_append_mini_map = charger_fonction($GLOBALS['meta']['gis_map'].'_append_mini_map','inc');
		$s .= '<div id="formMap" name="formMap" style="width: 180px; height: 180px"></div>';
		$s .= $gis_append_mini_map('formMap','form_lat','form_long',$glat,$glonx,'form_zoom',$gzoom,$row?true:false);
	} else {
		$s .= '<div>'. _T('gis:falta_plugin') .'</div>';
	}
	
	// Formulario para actualizar as coordenadas do mapa
	$s .= '<form id="formulaire_coordenadas" name="formulaire_coordenadas" action="'.generer_url_ecrire("mots_edit","&id_mot=".$id_mot).'" method="post">
		<div style="height:20px; padding:4px;"><input type="text" name="lat" id="form_lat" value="'.$glat.'" size="12" style="float:right"/><label>'._T('gis:lat').': </label></div>
		<div style="height:20px; padding:4px;"><input type="text" name="lonx" id="form_long" value="'.$glonx.'" size="12" style="float:right"/><label>'._T('gis:long').': </label></div>
		<div style="height:20px; padding:4px;"><input type="text" name="zoom" id="form_zoom" value="'.$gzoom.'" size="12" style="float:right"/><label>'._T('gis:zoom').': </label></div>
		<div style="height:20px; padding:4px;"><input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" /></div>
		</form>
		</div>';
		
	$s .= fin_block();
	$s .= fin_block();
	$s .= fin_cadre(true);
	return $s;
}
 

	
?>
