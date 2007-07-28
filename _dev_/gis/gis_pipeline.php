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

function gis_gismot($flux){
	if (_request('exec')=='mots_edit'){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_mots($flux['arg']['id_mot']);
	}
	return $flux;
}

function gis_insertar_maparticle($flux){
	if (_request('exec')=='articles'){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_cambiar_coord($flux['arg']['id_article']);
	}
	return $flux;
}

// --------------------------------
// inserta no head da parte PRIVADA
// --------------------------------
function gis_insertar_head($flux){
	if (($r=_request('exec'))=='articles' OR _request('exec')=='mots_edit' OR $r=='gis'){
		$flux .= '<script type="text/javascript" src="'.generer_url_public('geomap.js').'"></script>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/gis.js"></script>';
		if ((_request('exec')=='articles'))
			$flux .= '<script language="javascript">
		$(document).ready(function() {
			$(\'#cadroFormulario\').hide()
		});
		</script>';
	}
	return $flux;
}

// --------------------------------
// inserta no head da parte PUBLICA
// --------------------------------
function gis_affichage_final($flux){
    if ((strpos($flux, '<div id="map"') == true) or (strpos($flux, '<div id="formMap"') == true)){
	
		$incHead='
		<script type="text/javascript" src="'.generer_url_public('geomap.js').'"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/swfobject.js"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/gis.js"></script>
		<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'googlemap_api/js/customControls.js"></script>';
        $incHead .= '<script language="javascript">
                $(document).ready(function() {
                    load();
                });
                </script>';
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}
?>