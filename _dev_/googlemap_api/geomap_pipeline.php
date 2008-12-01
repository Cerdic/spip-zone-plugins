<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio González, Berio Molina
 * (c) 2007 - Distribuído baixo licencia GNU/GPL
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

/* inserer les scripts dans le public */

function geomap_affichage_final($flux){

    if ((strpos($flux, '<div id="map') == true) or (strpos($flux, '<div id="formMap') == true) or (strpos($flux, "<div id='map") == true) && (lire_config('geomap/cle_api'))){
		$incHead = '';
		$geomap_script_init = charger_fonction('geomap_script_init','inc');
		$flux .= $geomap_script_init();
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    } else {
		return $flux;
	}
}

/* inserer les scripts dans le prive */

function geomap_insert_head_prive($flux){
	if (lire_config('geomap/cle_api')){
		$geomap_script_init = charger_fonction('geomap_script_init','inc');
		$flux .= $geomap_script_init();

		if ((_request('exec')=='articles' || _request('exec')=='naviguer')){
			$flux .= '
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(\'#cadroFormulario\').hide()
				});
			</script>';
		}
	}
	return $flux;
}

function geomap_I2_cfg_form($flux) {
	$flux .= recuperer_fond('fonds/inscription2_geoloc');
	return ($flux);
}

?>
