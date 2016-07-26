<?php

// we set a cron job to refresh specific pages defined by user
function refresher_taches_generales_cron($taches_generales){
	$taches_generales['refresh_cron'] = 300;
	return $taches_generales;
}

function refresher_pre_edition($arr){
	include_spip("inc/refresher_functions");
	refresh("lien", $arr);
	return $arr;
}


function refresher_post_edition($arr){
	include_spip("inc/refresher_functions");
	refresh("objet", $arr);	
	return $arr;
}


function refresher_post_edition_lien($arr){
	include_spip("inc/refresher_functions");
	refresh("lien", $arr);
	return $arr;
}

?>