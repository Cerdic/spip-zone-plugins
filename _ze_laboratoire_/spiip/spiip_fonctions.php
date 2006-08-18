<?php


	
	
function http_calendrier_mini_agenda ($annee, $mois, $jour_ved, $mois_ved, $annee_ved, $semaine = false,  $script='', $ancre='', $evt=''){
	include_spip('inc/agenda');
	return http_calendrier_agenda ($annee, $mois, $jour_ved, $mois_ved, $annee_ved, $semaine, $script, $ancre, $evt);
}

?>