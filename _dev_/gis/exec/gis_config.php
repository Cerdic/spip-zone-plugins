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
include_spip("inc/vieilles_defs");

function exec_gis_config(){
	global $connect_statut,$spip_lang_right;
	debut_page(_T('gis:configuration'));


	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	if (autoriser('administrer','gis')) {
		if ((isset($GLOBALS['meta']['gis_map']))&&($GLOBALS['meta']['gis_map']!='no')&&(strpos($GLOBALS['meta']['plugin'] , strtoupper($GLOBALS['meta']['gis_map'])))) {
			$gis_config = charger_fonction($GLOBALS['meta']['gis_map'].'_config','inc');
			echo $gis_config();
		} else {
			echo debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png").'<br>'.gros_titre(_T('gis:falta_plugin'),'',false).'<br>'.fin_cadre('r');
		}
	}
	echo fin_grand_cadre(true);
	fin_page();
}

?>