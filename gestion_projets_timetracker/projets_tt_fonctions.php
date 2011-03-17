<?php
     function duree($id_session,$difference=''){
     include_spip('gestion_projet_fonctions');
     
     if($difference){
     $date_fin=date('Y-m-d G:i:s');	
     $date_debut=sql_getfetsel('date_debut','spip_projets_timetracker',' id_session='.sql_quote($id_session));
     $difference=difference($date_debut,$date_fin);
     $duree=$duree+$difference;
     }
     else{
  	$sql=sql_select('duree','spip_projets_timetracker','id_session='.sql_quote($id_session));
     	$duree=0;	
     	$date_fin=date('Y-m-d G:i:s');		
	     while($r = sql_fetch($sql)){
	     $duree=number_format($duree+$r['duree'],2);
	     }
     }
     return $duree;
}
     
     
?>