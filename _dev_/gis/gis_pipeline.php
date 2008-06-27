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

include_spip('exec/gis');

function gis_ajouterBoutons($boutons_admin) {
	// si eres administrador
	if (autoriser('administrer','gis')) {
    // vese o bot—n na barra de "configuraci—n"
	    $boutons_admin['configuration']->sousmenu['gis_config']= new Bouton(
		    _DIR_PLUGIN_GIS.'img_pack/correxir.png', _T('gis:configuration'));
	}
	return $boutons_admin;
}

function gis_gismot($flux){
	if (_request('exec')=='mots_edit'){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_mots($flux['args']['id_mot']);
	}
	return $flux;
}

function gis_insertar_maparticle($flux){
	if ($flux['args']['exec']=='articles'){
		//on teste si cfg est actif
		if (function_exists('lire_config')) {
			$arracfgrubriques_gis=lire_config("gis/rubriques_gis",'');
			$id_article = $flux['args']["id_article"];
			if ($id_article!=''){
				//on cherche la rubrique de l'article
				$s = spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_article");
				$row = spip_fetch_array($s);
				$id_rubrique = $row['id_rubrique'];
				//et si la rubrique est dans l'arrayrub
				if ($arracfgrubriques_gis=='' OR in_array($id_rubrique, $arracfgrubriques_gis)) {
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
			$arracfgrubriques_gis=lire_config("gis/rubriques_gis",'');
			$id_rubrique = $flux['args']["id_rubrique"];
			if ($id_rubrique!=''){
				//et si la rubrique est dans l'arrayrub
				if ($arracfgrubriques_gis=="" OR in_array($id_rubrique, $arracfgrubriques_gis)) {
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
// inserta no head da parte PRIVADA
// --------------------------------
function gis_insertar_head($flux){
	if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
		$gis_script_init = charger_fonction($GLOBALS['meta']['gis_map'].'_script_init','inc');
		$flux .= $gis_script_init();
	}
	if ((_request('exec')=='articles' || _request('exec')=='naviguer'))
		$flux .= '
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(\'#cadroFormulario\').hide()
			});
		</script>';
	return $flux;
}

// --------------------------------
// inserta no head da parte PUBLICA
// --------------------------------
function gis_affichage_final($flux){

    if ((strpos($flux, '<div id="map') == true) or (strpos($flux, '<div id="formMap') == true) or (strpos($flux, "<div id='map") == true)){
		$incHead = '';
		if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
			$gis_public_script_init = charger_fonction($GLOBALS['meta']['gis_map'].'_public_script_init','inc');
			$incHead .= $gis_public_script_init();
		}
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}
?>