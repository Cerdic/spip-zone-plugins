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
	    $boutons_admin['configuration']->sousmenu['gis']= new Bouton(
		    _DIR_PLUGIN_GIS.'img_pack/correxir.png', _T('gis:configurar_gis'));
	}
	return $boutons_admin;
}

function gis_affiche_droite($arguments) {
  global $connect_statut, $connect_toutes_rubriques;
  include_spip('inc/parte_privada');
  if (($connect_statut == '0minirezo') AND $connect_toutes_rubriques) {
	if ($arguments['args']['exec'] == 'mots_types') {
	  $arguments['data'] .= gis_grupo_mots($flux['arg']['id_groupe']);
	}
  }
  return $arguments;
}

function gis_gismot($flux){
	if (_request('exec')=='mots_type'){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_grupo_mots($flux['arg']['id_groupe']);
	}
	/*if (_request('exec')=='articles'){
		include_spip('inc/parte_privada');
		$flux['data'] .= gis_grupo_mots($flux['arg']['id_article']);
	}*/
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
	if (($r=_request('exec'))=='articles' OR $r=='gis'){
		$geomap_script_init = charger_fonction('geomap_script_init','inc');
		$flux .= $geomap_script_init();
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
function gis_insertarp_head($flux){
	$geomap_script_init = charger_fonction('geomap_script_init','inc');
	$flux .= $geomap_script_init();
	$flux.='
<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/swfobject.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/gis.js"></script>';
	return $flux;
}

?>