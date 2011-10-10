<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonzalez, Berio Molina
 * (c) 2007 - Distribudo baixo licencia GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/distant');

function inc_geomap_public_script_init_dist(){
	$out = '<script type="text/javascript" src="'.generer_url_public('geomap.js').'"></script>';
	if (function_exists('lire_config') && lire_config("geomap/custom_control") != 'non')
		$out .= '<script type="text/javascript" src="'._DIR_PLUGIN_GEOMAP.'js/customControls.js"></script>';
	$out .= '<script type="text/javascript">
		jQuery(document).unload(function(){
			Gunload();
		});
	</script>';
    return $out;
}

?>
