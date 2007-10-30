<?php
/*
 * Googlemap API
 * 
 *
 * 
 */

include_spip("inc/utils");
include_spip("inc/presentation");

function exec_geomap_config(){
	global $connect_statut,$spip_lang_right;
	debut_page(_T('geomap:configuration'));

	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	if (autoriser('administrer','geomap')) {
		$geomap_config = charger_fonction('geomap_config','inc');
		echo $geomap_config();
	}
	echo fin_grand_cadre(true);
	fin_page();
}


?>