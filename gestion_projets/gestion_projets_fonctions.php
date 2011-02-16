<?php

function difference($date_debut,$date_fin,$secondes=3600){

	
		$date_debut_jours = date('d-m-Y',strtotime($date_debut));
 		$date_debut_heures = date('G:i:s',strtotime($date_debut));

   		$split_jours_debut = explode("-", $date_debut_jours);
   		$split_heures_debut = explode(":", $date_debut_heures);

 		$date_fin_jours = date('d-m-Y',strtotime($date_fin));
 		$date_fin_heures = date('G:i:s',strtotime($date_fin));

   		$split_jours_fin = explode("-", $date_fin_jours);
   		$split_heures_fin = explode(":", $date_fin_heures);
 
   		$date_f = mktime($split_heures_fin[0], $split_heures_fin[1], $split_heures_fin[2], $split_jours_fin[1], $split_jours_fin[0], $split_jours_fin[2]) ;
   		$date_debut = mktime($split_heures_debut[0], $split_heures_debut[1], $split_heures_debut[2], $split_jours_debut[1], $split_jours_debut[0], $split_jours_debut[2]) ;   	
 		$date_difference = ($date_f-$date_debut)/60;
		
		return number_format($date_difference,20);
		
		

}


function duree($id_projet,$difference=''){

	$sql=sql_select('duree','spip_projets_timetracker','id_projet='.sql_quote($id_projet));
	$duree=0;	
	$date_fin=date('Y-m-d G:i:s');		
	while($r = sql_fetch($sql)){
		$duree=$duree+$r['duree'];
		}
	if($difference){
		$date_debut=sql_getfetsel('date_debut','spip_projets_timetracker','date_fin="0000-00-00 00:00:00" AND id_projet='.sql_quote($id_projet));
		$difference=difference($date_debut,$date_fin);
		$duree=$duree+$difference;
	}
return number_format($duree,2);
}
?>