<?php

function zotspip_taches_generales_cron($taches){
	$taches['maj_items_zotspip'] = 3600*6; // toutes les 6 heures
	$taches['nettoyer_zotspip'] = 3600*6; // toutes les 6 heures
	$taches['maj_collections_zotspip'] = 3600*6; // toutes les 6 heures
	$taches['maj_schema_zotero'] = 3600*24*30; // une fois par mois
	return $taches;
}
?>