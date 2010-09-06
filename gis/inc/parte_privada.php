<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonzalez, Berio Molina
 * (c) 2007 - Distribudo baixo licencia GNU/GPL
 *
 */
include_spip('base/abstract_sql');
include_spip('inc/vieilles_defs');
include_spip('inc/autoriser');


function gis_cambiar_coord($id,$table,$exec) {
	global $spip_lang_left, $spip_lang_right;

	$pkey = id_table_objet($table);

	// on recupere l'id de l'auteur en cours
	if ($GLOBALS["auteur_session"])
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	// et on verifie qu'il est autorisé à modifier l'élément en cours
	$autoriser = autoriser("modifier",$table,$id);

	$glat = NULL;
	$glonx = NULL;
	$gzoom = NULL;
	$gicon = NULL;
	$mapa = "";
	$defaut = _DIR_PLUGIN_GEOMAP ? 'geomap' : '';
	$api_carte = lire_config('gis/api_carte',$defaut);
	if (lire_config('gis/geocoding'))
		$geocoding = true;
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
		if ($geocoding){
			$pays = _request('pays');
			$code_pays = _request('code_pays');
			$region = _request('region');
			$ville = _request('ville');
			$code_postal = _request('code_postal');
		}
		if (!$row){
			if ($geocoding) {
				spip_abstract_insert("spip_gis",
					"($pkey, lat, lonx, zoom, pays, code_pays, region, ville, code_postal)",
					"("._q($id) .", "._q($glat).", "._q($glonx).", "._q($gzoom).", "._q($pays).", "._q($code_pays).", "._q($region).", "._q($ville).", "._q($code_postal).")"
				);
			} else {
				spip_abstract_insert("spip_gis",
					"($pkey, lat, lonx, zoom)",
					"("._q($id) .", "._q($glat).", "._q($glonx).", "._q($gzoom).")"
				);
			}
		} else {
			if ($geocoding) {
				spip_query("UPDATE spip_gis SET lat="._q($glat).", lonx="._q($glonx).", zoom="._q($gzoom).", pays="._q($pays).", code_pays="._q($code_pays).", region="._q($region).", ville="._q($ville).", code_postal="._q($code_postal)."  WHERE $pkey = " . _q($id));
			} else {
				spip_query("UPDATE spip_gis SET lat="._q($glat).", lonx="._q($glonx).", zoom="._q($gzoom)."  WHERE $pkey = " . _q($id));
			}
		}
	}
	if(_request('supprimer')){
		sql_delete("spip_gis", array("$pkey = " . sql_quote($id)));
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
		if ($api_carte) {
			$gis_append_view_map = charger_fonction($api_carte.'_append_view_map','inc');
			$mapa = '<div id="viewMap" style="width:477px;height:100px;border:1px solid #000;overflow:hidden;"></div>';
		  	$mapa .= $gis_append_view_map('viewMap',$glat,$glonx,$zoom,array(array('lonx'=>$glonx,'lat'=>$glat)),$gicon);
		} else {
			$mapa = '<div>' . _T('gis:falta_plugin') . '</div>';
		}

	}

	$s .= '';

	// Ajouter un formulaire de modification si l'auteur est autorisé
	if ($autoriser){
		// On teste la version de SPIP utilisee 2 ou 1.9
		if(function_exists('bouton_block_depliable')){
			$s .= debut_cadre('e', _DIR_PLUGIN_GIS."img_pack/correxir.png",'',bouton_block_depliable('&nbsp;&nbsp;&nbsp;<span style="text-transform: uppercase;">'._T('gis:cambiar').'</span>', false, "cadroFormulario"));
		}else{
			$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
			$s .= bouton_block_invisible("ajouter_form");
			$s .= '&nbsp;&nbsp;&nbsp;<strong class="verdana3" style="text-transform: uppercase;">' . _T('gis:cambiar') . ' <a onclick="$(\'#cadroFormulario\').slideToggle(\'slow\')">(' . _T('gis:clic_desplegar') . ')</a></strong>';
		}

		$s .= debut_block_visible("ajouter_form");
		$s .= '<div id="cadroFormulario" class="formulaire_spip formulaire_editer formulaire_cfg" style="margin-bottom:5px;">
		<span class="verdana2">' . _T("gis:clic_mapa") . '</span>';
		if ($api_carte) {
			$gis_append_clicable_map = charger_fonction($api_carte.'_append_clicable_map','inc');
			$s .= '<div id="formMap" style="width: 467px; height: 350px; margin:5px auto; overflow:hidden;"></div>';
			$s .= $gis_append_clicable_map('formMap','form_lat','form_long',$glat,$glonx,'form_zoom',$gzoom,$row?true:false);
		} else {
			$s .= '<div>' . _T('gis:falta_plugin') . '</div>';
		}
		// Formulario para actualizar as coordenadas do mapa______________________.
		$s .= '
			<form id="formulaire_address" action="#">
			<ul style="text-align:center;">
				<li style="padding-left:0; display:inline;">
					<input type="text" class="text" size="50" name="map_address" id="map_address" value="'._T('gis:address').'" onfocus="this.value=\'\';" style="width:360px; margin-right:10px;" />
					<input type="submit" value="'._T('gis:label_address').'" />
				</li>
			</ul>
			</form>
			<form id="formulaire_coordenadas" action="'.generer_url_ecrire($exec,"$pkey=".$id).'" method="post">
			'.($geocoding?'<input type="hidden" name="code_pays" id="code_pays" value="" />
			<input type="hidden" name="region" id="region" value="" />
			<input type="hidden" name="code_postal" id="code_postal" value="" />':'').'
			<ul style="text-align:center;">
			<li style="padding-left:0; display:inline;"><label for="form_lat" style="margin-left:0; float:none; display:inline;">'._T('gis:lat').': </label><input type="text" class="text" name="lat" id="form_lat" value="'.$glat.'" size="12" style="width:80px;" /></li>
			<li style="padding-left:0; display:inline;"><label for="form_long" style="margin-left:0; float:none; display:inline;">'._T('gis:long').': </label><input type="text" class="text" name="lonx" id="form_long" value="'.$glonx.'" size="12" style="width:80px;" /></li>
			<li style="padding-left:0; display:inline;"><label for="form_zoom" style="margin-left:0; float:none; display:inline;">'._T('gis:zoom').': </label><input type="text" class="text" name="zoom" id="form_zoom" value="'.$gzoom.'" size="6" style="width:30px;" /></li>
			</ul>
			'.($geocoding?'<ul style="text-align:center;">
				<li style="padding-left:0; display:inline;">
					<label for="pays" style="margin-left:0; float:none; display:inline;">'._T('gis:label_pays').': </label>
					<input type="text" class="text" name="pays" id="pays" value="'.$row['pays'].'" style="width:95px;" />
				</li>
				<li style="padding-left:0; display:inline;">
					<label for="ville" style="margin-left:0; float:none; display:inline;">'._T('gis:label_ville').': </label>
					<input type="text" class="text" name="ville" id="ville" value="'.$row['ville'].'" style="width:95px;" />
				</li>
			</ul>':'').'
			<p class="boutons">
			<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" /><input type="submit" name="supprimer" value="'._T("gis:bouton_supprimer").'" />
			</p>
			</form>
			</div>';
		$s .= $mapa;
		$s .= fin_block();
		$s .= fin_cadre(true);
	}else{
		$s .= debut_cadre('e', _DIR_PLUGIN_GIS."img_pack/correxir.png",'',_T("gis:coord"),'','', true);
		$s .= $mapa;
		$s .= fin_cadre(true);
	}
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

	if ($api_carte = lire_config('gis/api_carte')) {
		$gis_append_mini_map = charger_fonction($api_carte.'_append_mini_map','inc');
		$s .= '<div id="formMap" name="formMap" style="width: 180px; height: 180px;overflow:hidden;"></div>';
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