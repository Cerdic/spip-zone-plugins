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


include_spip("inc/utils");
include_spip("inc/presentation");
include_spip('base/abstract_sql');

function exec_gis_dist(){
	global $connect_statut,$spip_lang_right;
	debut_page(_T('gis:configurar_gis'));

	// Google map KEY
	echo debut_grand_cadre(true);
	if (autoriser('administrer','gis')) {
		$geomap_config = charger_fonction('geomap_config','inc');
		echo $geomap_config();
	}
	echo fin_grand_cadre(true);
	fin_page();
}

?>
