<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function genie_speedsyndic_dist($t){
	$sites = lire_config('speedsyndic/syndicatedlist');
	foreach($sites as $id_syndic){
		traiter_site($id_syndic);
	}
	return true;
}


function traiter_site($id_syndic) {
	include_spip('genie/syndic');
	define('_GENIE_SYNDIC', 2); // Pas de faux message d'erreur
	$t = syndic_a_jour($id_syndic);
	return $t ? 0 : $id_syndic;
}
