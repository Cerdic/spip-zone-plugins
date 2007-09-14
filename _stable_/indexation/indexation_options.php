<?php

// Un cron toutes les 5 minutes
function Indexation_taches_generales_cron($taches_generales){
	$taches_generales['indexation'] = 5;
	return $taches_generales;
}

// Tester la disponibilite de la methode FULLTEXT
function Indexation_test_fulltext() {
	// TODO
	return true;
}

?>
