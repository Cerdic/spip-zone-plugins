<?php

// Un cron toutes les 3 heures
function popularite_taches_generales_cron($taches_generales){
	$taches_generales['popularite'] = 3*3600;
	return $taches_generales;
}

