<?php

include_spip('base/checklink');

function checklink_taches_generales_cron($taches_generales){
	$taches_generales['checklink_verification'] = 180;
	return $taches_generales;
}
?>