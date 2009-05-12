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
 
function gis_cambiar_coord($id,$table,$exec) {
	global $spip_lang_left, $spip_lang_right;
	
	
	$pkey = id_table_objet($table);
	
	$glat = NULL;
	$glonx = NULL;
	$gzoom = NULL;
	$gicon = NULL;
	$mapa = "";
	$api_carte = lire_config('gis/api_carte');
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
	if(_request('supprimer')){
		sql_delete("spip_gis", array("$pkey = " . sql_quote($id)));
	}
	if ($glat!==NULL){
		// on cherche un mot clé pour cet(te) article/rubrique
		$nbMots = sql_countsel("spip_mots_{$table}s smt, spip_mots sm","smt.$pkey=".intval($id)." and smt.id_mot=sm.id_mot and sm.type='marker_icon'");
		//echo "nbMots : ", $nbMots,"<br>";
		if ($nbMots > 0) {
			// il existe un mot-clé pour cet(te) article/rubrique du groupe de mots 'marker_icon'
			// on recherche son identifiant
			$id_mot = sql_getfetsel("smt.id_mot","spip_mots_{$table}s smt, spip_mots sm","smt.$pkey=".intval($id)." and smt.id_mot=sm.id_mot and sm.type='marker_icon'");
			if ($id_mot != '') {
				if (file_exists(_DIR_IMG."/moton$id_mot.png")) {
				  	$gicon = "moton$id_mot.png";
				} else if (file_exists(_DIR_IMG."/moton$id_mot.gif")) {
					$gicon = "moton$id_mot.gif";
				}
			}
		} else {
			// l'article/ rubrique n'a pas de puce associé --> on cherche dans sa hiérarchie
			$nomTable = table_objet($table);
			$parents = array();
			$id_parent = 0;
			if ($nomTable == 'articles') {
				// table articles
				$id_parent = sql_getfetsel("id_rubrique","spip_articles","id_article=".intval($id));
				$parents[] = $id_parent;
			} else if ($nomTable == 'rubriques') {
				// table rubriques
				$id_parent = sql_getfetsel("id_parent","spip_rubriques","id_rubrique=".intval($id));
				$parents[] = $id_parent;
			}
			
			// ensuite on cherche toutes les rubriques parent de cet objet
			while ($id_parent != 0) {
				$id_parent = sql_getfetsel("id_parent","spip_rubriques","id_rubrique=".intval($id_parent));
				if ($id_parent != 0) {
					$parents[] = $id_parent;
				}
			} 
			
			if (count($parents) > 0) {
				// on a donc des rubriques parents, on en cherche une qui a un mot clé de type 'marker_icon'
				foreach ($parents as $id_parent) {
					$nbMots = sql_countsel("spip_mots_rubriques smr, spip_mots sm","smr.id_rubrique=".intval($id_parent)." and smr.id_mot=sm.id_mot and sm.type='marker_icon'");
					if ($nbMots > 0) {
						// il existe un mot-clé pour cette rubrique du groupe de mots 'marker_icon'
						// on recherche son identifiant
						$id_mot = sql_getfetsel("smr.id_mot","spip_mots_rubriques smr, spip_mots sm","smr.id_rubrique=".intval($id_parent)." and smr.id_mot=sm.id_mot and sm.type='marker_icon'");
						if ($id_mot != '') {
							if (file_exists(_DIR_IMG."/moton$id_mot.png")) {
				  				$gicon = "moton$id_mot.png";
				  				break;
							} else if (file_exists(_DIR_IMG."/moton$id_mot.gif")) {
								$gicon = "moton$id_mot.gif";
								break;
							}
						}
					}
				}
			}
			
			if ($gicon == '') {
				// pas d'icone trouvé
				// on recherche le mot clé 'default' de type 'marker_icon'
				$id_mot = sql_getfetsel("id_mot","spip_mots","titre='default' and sm.type='marker_icon'");
				if ($id_mot != '') {
					if (file_exists(_DIR_IMG."/moton$id_mot.png")) {
				  		$gicon = "moton$id_mot.png";
					} else if (file_exists(_DIR_IMG."/moton$id_mot.gif")) {
						$gicon = "moton$id_mot.gif";
					}
				}
			}
			
			// si pas d'icone, on laisse google la gérer
		}
		
		if ($api_carte) {
			$gis_append_view_map = charger_fonction($api_carte.'_append_view_map','inc');
			$mapa = '<div id="viewMap" style="width: 477px; height: 100px; border:1px solid #000"></div>';
		  	$mapa .= $gis_append_view_map('viewMap',$glat,$glonx,$zoom,array(array('lonx'=>$glonx,'lat'=>$glat)),$gicon);
		} else {
			$mapa = '<div>' . _T('gis:falta_plugin') . '</div>';
		}
		
	}
	
	$s .= '';
	
	// Ajouter un formulaire
	
	// On teste la version de SPIP utilisee 2 ou 1.9
	if(function_exists('bouton_block_depliable')){
		$s .= debut_cadre('e', _DIR_PLUGIN_GIS."img_pack/correxir.png",'',bouton_block_depliable('&nbsp;&nbsp;&nbsp;<span>'._T('gis:cambiar').'</span>', false, "cadroFormulario"));
	}else{
		$s .= debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png"); 
		$s .= bouton_block_invisible("ajouter_form"); 
		$s .= '&nbsp;&nbsp;&nbsp;<strong class="verdana3">' . _T('gis:cambiar') . ' <a onclick="$(\'#cadroFormulario\').slideToggle(\'slow\')">(' . _T('gis:clic_desplegar') . ')</a></strong>';
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
		<ul style="text-align:center;">
		<li style="padding-left:0; display:inline;"><label for="form_lat" style="margin-left:0; float:none; display:inline;">'._T('gis:lat').': </label><input type="text" class="text" name="lat" id="form_lat" value="'.$glat.'" size="12" style="width:80px;" /></li>
		<li style="padding-left:0; display:inline;"><label for="form_long" style="margin-left:0; float:none; display:inline;">'._T('gis:long').': </label><input type="text" class="text" name="lonx" id="form_long" value="'.$glonx.'" size="12" style="width:80px;" /></li>
		<li style="padding-left:0; display:inline;"><label for="form_zoom" style="margin-left:0; float:none; display:inline;">'._T('gis:zoom').': </label><input type="text" class="text" name="zoom" id="form_zoom" value="'.$gzoom.'" size="6" style="width:30px;" /></li>
		</ul>
		<p class="boutons">
		<input type="submit" name="actualizar" value="'._T("gis:boton_actualizar").'" /><input type="submit" name="supprimer" value="'._T("gis:bouton_supprimer").'" />
		</p>
		</form>
		</div>';
	$s .= $mapa;
	$s .= fin_block();
	$s .= fin_cadre(true);
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
	$s .= '&nbsp;&nbsp;&nbsp;<strong class="verdana3">'. _T("gis:cambiar") .' '. $id_mot .' <a onclick="$(\'#cadroFormulario\').slideToggle(\'slow\')">('. _T('gis:clic_desplegar') .')</a></strong>';
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