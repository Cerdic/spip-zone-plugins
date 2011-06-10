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


function gis_gismot($flux){
	if ((_request('exec')=='mots_edit') AND (_request('new')!=oui)){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_mots($flux['args']['id_mot']);
	}
	return $flux;
}

function gis_insertar_map($flux){
	if ($flux['args']['exec']=='articles'){
		//on teste si cfg est actif
		if (function_exists('lire_config')) {
			$arracfgrubriques_gis=lire_config("gis/rubriques_gis",array(0,-1));
			$id_article = $flux['args']["id_article"];
			if ($id_article!=''){
				//on cherche la rubrique de l'article
				$s = spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_article");
				$row = spip_fetch_array($s);
				$id_rubrique = $row['id_rubrique'];
				//et si la rubrique est dans l'arrayrub
				if (in_array(-1,$arracfgrubriques_gis) OR in_array($id_rubrique, $arracfgrubriques_gis)) {
					include_spip('inc/parte_privada');
					$flux['data'].= gis_cambiar_coord($flux['args']['id_article'],"article","articles");
				}
			}
		}else {
			include_spip('inc/parte_privada');
			$flux['data'].= gis_cambiar_coord($flux['args']['id_article'],"article","articles");	 
		}
	} else if ($flux['args']['exec']=='naviguer'){
		//on teste si cfg est actif
		if (function_exists('lire_config')) {
			$arracfgrubriques_gis=lire_config("gis/rubriques_gis",array(0,-1));
			$id_rubrique = $flux['args']["id_rubrique"];
			if ($id_rubrique!=''){
				//et si la rubrique est dans l'arrayrub
				if (in_array(-1,$arracfgrubriques_gis) OR in_array($id_rubrique, $arracfgrubriques_gis)) {
					include_spip('inc/parte_privada');
					$flux['data'].= gis_cambiar_coord($flux['args']['id_rubrique'],"rubrique","naviguer");
				}
			}
		}else {
			include_spip('inc/parte_privada');
			$flux['data'].= gis_cambiar_coord($flux['args']['id_rubrique'],"rubrique","naviguer");	 
		}
	
	}
	return $flux;
}


// --------------------------------
// inserta no head da parte PUBLICA
// --------------------------------
function gis_affichage_final($flux){

    if ((strpos($flux, '<div id="map') == true) or (strpos($flux, '<div id="formMap') == true) or (strpos($flux, "<div id='map") == true)){
		$incHead = '';
		if (function_exists('lire_config') && lire_config('gis/api_carte')) {
			if (function_exists('lire_config') && lire_config("gis/swfobject") != 'non')
				$incHead .= '
		<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/swfobject.js"></script>';
		}
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}
?>