<?php
/*
 * Googlemap API
 * 
 *
 * 
 */

include_spip("inc/utils");
include_spip("inc/presentation");

function exec_mymap_config(){
	global $connect_statut,$spip_lang_right;
	$commencer_page = charger_fonction('commencer_page', 'inc') ;
	echo $commencer_page(_T('mymap:configuration'), "", "") ;

	echo '<br /><br /><br />';
	echo gros_titre(_T('titre_configuration'),'',false);
	echo barre_onglets("configuration", "mymap_config");

	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	if (autoriser('administrer','mymap')) {
		$mymap_config = charger_fonction('mymap_config','inc');
		echo $mymap_config();
	}
	echo fin_grand_cadre(true);
	fin_page();
}


?>