<?php

// Un cron toutes les 5 minutes
function Indexation_taches_generales_cron($taches_generales){
	$taches_generales['indexation'] = 1; //300;
	return $taches_generales;
}

// Cron
function genie_indexation_dist($t) {
	spip_log('Indexation: cron');
	include_spip('inc/indexation');
	effectuer_une_indexation();
}




?>
