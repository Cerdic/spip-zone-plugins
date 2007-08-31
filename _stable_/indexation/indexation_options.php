<?php

// Un cron toutes les 5 minutes
function Indexation_taches_generales_cron($taches_generales){
	$taches_generales['indexation'] = 1; //300;
	return $taches_generales;
}

// include_spip('googlelike');

?>
