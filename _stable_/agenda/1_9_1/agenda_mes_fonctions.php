<?php

//include_spip('inc/agenda_filtres'); // declaration directe dans le xml pour eviter un find_in_path
include_spip('public/criteres_agenda');
include_spip('inc/agenda_filtres');

function Agenda_heure_selector($date,$suffixe){
	$d = strtotime($date);
	$heure = date('H',$d);
	$minute = date('i',$d);
	return
		afficher_heure($heure, "name='heure_evenement$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='minute_evenement$suffixe' size='1' class='fondl'");
}
?>