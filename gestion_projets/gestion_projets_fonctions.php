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

?>