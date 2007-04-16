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
	global $connect_statut;
	debut_page(_T('gis:configurar_gis'));

// Google map KEY	
	echo debut_grand_cadre(true);
	if ($connect_statut == "0minirezo") {
		echo debut_cadre('r', _DIR_PLUGIN_GIS."img_pack/correxir.png");
		if($_POST['ok']){
			include_spip('inc/meta');
			ecrire_meta('gis_googlemapkey',$_POST['key']);
			ecrire_metas();
		}
		$apikey = isset($GLOBALS['meta']['gis_googlemapkey'])?$GLOBALS['meta']['gis_googlemapkey']:"";
		echo '<br/>';
		echo '<a href="http://www.google.com/apis/maps" target="_blank" ><img src="'._DIR_PLUGIN_GIS.'img_pack/logo_google.gif" border="0" align="left" hspace="10" ></a>';
		echo '<form name="googlemapkey" method="post" action="'.generer_url_ecrire('gis').'">';
		echo '<br/>';
		echo '<label>Google Map API Key <a href="http://www.google.com/apis/maps/signup.html" target="_blank" >'._T('gis:conseguir').'</a></label> <input type="text" name="key" value="'.$apikey.'" size="30" />';
		echo '<input type="submit" name="ok" value="ok" />';
		echo '</form>';
		
		if($_POST['ok']){
			echo '<div align="center" style="margin:20px auto">';
			echo ''._T('gis:clave_engadida').'<code>'.$_POST['key'].'</code>';
			echo '</div>';
		}
		echo fin_cadre(true);
	}
	echo fin_grand_cadre(true);
	fin_page();
}

?>
