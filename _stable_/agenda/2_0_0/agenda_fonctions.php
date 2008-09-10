<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

include_spip('public/criteres_agenda');
include_spip('public/agenda_boucles');
include_spip('inc/agenda_filtres');
include_spip('inc/agenda_vieux_filtres');


function Agenda_heure_selector($date,$suffixe){
	$d = strtotime($date);
	$heure = date('H',$d);
	$minute = date('i',$d);
	return
		afficher_heure($heure, "name='heure_evenement$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='minute_evenement$suffixe' size='1' class='fondl'");
}

function critere_fusion_date_mois($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$date = $GLOBALS['table_date'][$type];
	$champ_date = $boucle->id_table.'.'.$date;

	$boucles[$idb]->group[]  = 'DATE_FORMAT('.$champ_date.', \'%Y-%m\')'; 
	$boucles[$idb]->select[] = 'DATE_FORMAT('.$champ_date.', \'%Y-%m\') AS date';
}


?>