<?php
     function duree($id_tache,$difference=''){
     include_spip('gestion_projet_fonctions');
     
     $sql=sql_select('duree','spip_projets_timetracker','id_tache='.sql_quote($id_tache));
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