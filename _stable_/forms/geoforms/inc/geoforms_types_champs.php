<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function geoforms_forms_types_champs($flux){
	$flux['geox']=_T('geoforms:geoloc_x');
	$flux['geoy']=_T('geoforms:geoloc_y');
	$flux['geoz']=_T('geoforms:geoloc_z');
	return $flux;
}

function geoforms_forms_bloc_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];

	if (in_array($type,array('geox','geoy','geoz'))){
		$action_link = $flux['args']['action_link'];
		$redirect = $flux['args']['redirect'];
		$idbloc = $flux['args']['idbloc'];
	
		$id_form = $row['id_form'];
		$champ = $row['champ'];
		$titre = $row['titre'];
		$obligatoire = $row['obligatoire'];
		$extra_info = $row['extra_info'];
		$specifiant = $row['specifiant'];
		$public = $row['public'];
		$aide = $row['aide'];
		$html_wrap = $row['html_wrap'];
		
		$out = $flux['data'];
		
		$out .= "<label for='systeme_$champ'>"._T("geoforms:systeme_geographique")."</label> :";
		$out .= " &nbsp;<select name='systeme_$champ' id='systeme_$champ' class='fondo verdana2'>\n";
		$out .= "<option value=''>"._T("geoforms:latitude_longitude_$type")."</option>\n";
		include_spip('inc/geoforms_projections');
		foreach(geoforms_liste_projections() as $key){
			$selected = ($key == $row['extra_info']) ? " selected='selected'": "";
			$out .= "<option value='$key'$selected>"._T("geoforms:lambert_$type")." ("._T("geoforms:$key").")"."</option>\n";
		}
		$out .= "</select>";
		$out .= "<br />\n";
		
		$flux['data'] = $out;
	}
	return $flux;
}

function geoforms_forms_update_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];
	$champ = $row['champ'];
	if (in_array($type,array('geox','geoy','geoz'))){
		if ($s = _request("systeme_$champ")){
			include_spip('inc/geoforms_projections');
			if (in_array($s,geoforms_liste_projections()))
				$flux['data'] = $s;
			else
				$flux['data'] = "";
		}
	}
	return $flux;
}

function geoforms_forms_input_champs($flux){
	static $vu=array();
	$type = $flux['args']['type'];
	if (in_array($type,array('geox','geoy','geoz'))
	  AND ((_DIR_RESTREINT AND $GLOBALS['geoforms_public']!=false) OR _request('exec')!=='forms_edit')
	  ) {
		$id_form = $flux['args']['id_form'];
		$champ = $flux['args']['champ'];
		$extra_info = $flux['args']['extra_info'];
		$vu[$id_form][$type]=array(
			'id'=>extraire_attribut($flux['data'],'id'),
			//'name'=>extraire_attribut($flux['data'],'name'),
			'value'=>extraire_attribut($flux['data'],'value'),
			'syst'=>$extra_info);
		if (isset($vu[$id_form]['geox']) AND isset($vu[$id_form]['geoy'])){
			include_spip('inc/geoforms');
			$syst = $vu[$id_form]['geox']['syst'];
			list($x,$y) = geoforms_latitude_longitude($vu[$id_form]['geox']['value'],$vu[$id_form]['geoy']['value'],$syst);
			if ($geomap_append_moveend_map = charger_fonction('geomap_append_clicable_map','inc',true)){
				$id = $vu[$id_form]['geox']['id']."-".$vu[$id_form]['geoy']['id'];
				$flux['data'].="<div class='geomap' id='map-$id_form-$id'> </div>";
				$flux['data'].=$geomap_append_moveend_map("map-$id_form-$id",$vu[$id_form]['geox']['id'],$vu[$id_form]['geoy']['id'],$x,$y, NULL,NULL,true);
				unset($vu[$id_form]);
			}
		}
	}
	return $flux;
}

function geoforms_forms_pre_edition_donnee($flux){
	$geox = $geoy = NULL;
	foreach($flux['data'] as $champ=>$val){
		if (!$geox && $flux['args']['champs'][$champ]['type']=='geox') $geox = $champ;
		if (!$geoy && $flux['args']['champs'][$champ]['type']=='geoy') $geoy = $champ;
		if ($geox && $geoxy) continue;
	}
	if ($geox && $geoy){
		$syst = $flux['args']['champs'][$geox]['extra_info'];
		if (strlen($syst) AND ($flux['data'][$geox]<=90.0) AND ($flux['data'][$geoy]<=180.0)){
			include_spip('inc/geoforms_projections');
			list($flux['data'][$geox],$flux['data'][$geoy]) = geoforms_lat_lont_vers_syst($flux['data'][$geox],$flux['data'][$geoy],$syst);
		}
	}
	return $flux;
}

function geoforms_forms_ajoute_styles($texte){
	if ($f=find_in_path('geoforms.css')){
		lire_fichier($f,$css);
		$texte = $texte.$css;
	}
	return $texte;
}
?>