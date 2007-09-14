<?php
/*
 * ctrlforms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/utils");
include_spip("inc/presentation");

function exec_ctrlforms_config(){
	global $connect_statut,$spip_lang_right;
	debut_page(_T('ctrlforms:configuration'));

	// Configuration du systeme geographique
	echo debut_grand_cadre(true);
	if (autoriser('administrer','ctrlforms')) {
		$geomap_config = charger_fonction('geomap_config','inc');
		echo $geomap_config();
	}
	echo fin_grand_cadre(true);
	fin_page();
}


?>