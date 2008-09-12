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

function agenda_critere_fusion_par_xx($format, $as, $idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$_date = isset($crit->param[0]) ? calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent)
	  : "'".(isset($GLOBALS['table_date'][$type])?$GLOBALS['table_date'][$type]:"date")."'";

	$date = $boucle->id_table. '.' .substr($_date,1,-1);

	$boucles[$idb]->group[]  = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format')"; 
	$boucles[$idb]->select[] = 'DATE_FORMAT('.$boucle->id_table.'.".'.$_date.'.", ' . "'$format') AS $as";	
}

function critere_fusion_par_jour($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y-%m-%d','jour',$idb, $boucles, $crit);
}
function critere_fusion_par_mois($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y-%m','mois',$idb, $boucles, $crit);
}
function critere_fusion_par_annee($idb, &$boucles, $crit) {
	agenda_critere_fusion_par_xx('%Y','annee',$idb, $boucles, $crit);
}


?>