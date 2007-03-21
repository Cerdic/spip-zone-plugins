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
	if ($GLOBALS['connect_statut'] == "0minirezo") {
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

		$query = "SELECT * FROM spip_gis_config WHERE name='googlemapkey'";
		$result = spip_query($query);
		$row = spip_fetch_array($result);
		
		if ((_request('exec')=='articles')){
			$flux.='<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$row['value'].'"></script>
			<script language="javascript">
			$(document).ready(function() {
				$(\'#cadroFormulario\').hide()
			});
			function coordenadas (articulo){
				$.ajax({
					type: "POST",
					url: "../spip.php?page=cambiar_coordenadas",
					data: "id_article="+articulo+"&lat="+document.forms.formulaire_coordenadas.lat.value+"&lonx="+document.forms.formulaire_coordenadas.lonx.value,
					success: function() {
						alert("ok");
					}
				});
			}
			</script>
			<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/googlemap.js"></script>';
		}

	
	return $flux;
}

// --------------------------------
// inserta no head da parte PUBLICA
// --------------------------------
function gis_insertarp_head($flux){

		$query = "SELECT * FROM spip_gis_config WHERE name='googlemapkey'";
		$result = spip_query($query);
		$row = spip_fetch_array($result);
		
		$flux.='
<!-- scripts head plugin gis _______________________.-->
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$row['value'].'"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/swfobject.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GIS.'js/googlemap.js"></script>';
		
	
	return $flux;
}
	
?>