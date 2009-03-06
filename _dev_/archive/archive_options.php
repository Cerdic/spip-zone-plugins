<?php
// Lancement des taches cron pour l'archivage
function archive_taches_generales_cron($taches_generales){ 
       $taches_generales['archive_cron']=1*24*3600;
	    return $taches_generales;
 }
?>
