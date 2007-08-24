<?php

include_spip('public/criteres_agenda');
include_spip('public/criteres_pim_agenda');
include_spip('inc/agenda_filtres');


// Pre traitements -----------------------------------------------------------------------

function PIMAgenda_heure_selector($date,$suffixe){
	$d = strtotime($date);
	$heure = date('H',$d);
	$minute = date('i',$d);
	return
		afficher_heure($heure, "name='heure_evenement$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='minute_evenement$suffixe' size='1' class='fondl'");
}
?>