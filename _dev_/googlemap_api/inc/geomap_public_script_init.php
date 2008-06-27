<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */
include_spip('inc/distant');

function inc_geomap_public_script_init_dist(){
	$out = '
	<script type="text/javascript" src="'.generer_url_public('geomap.js').'"></script>
	<script type="text/javascript" src="'._DIR_PLUGIN_GEOMAP.'js/customControls.js"></script>
	<script type="text/javascript">
		jQuery(document).unload(function(){
			Gunload();
		});
	</script>';
    return $out;
}

?>
