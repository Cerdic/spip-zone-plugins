<?php

function zotspip_taches_generales_cron($taches){
	$taches['maj_zotspip'] = 3600*4; // toutes les 4 heures
	$taches['maj_schema_zotero'] = 3600*24*30; // une fois par mois
	return $taches;
}
?>