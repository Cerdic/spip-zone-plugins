<?php
//Ajout de champs supplémentaires
include_spip('base/serial');
//ajout du champ archive_date pour l'article, possibilité de #ARCHIVE_DATE
$GLOBALS['tables_principales']['spip_articles']['field']=
array_merge(
    $GLOBALS['tables_principales']['spip_articles']['field'],
    array('archive_date' => "datetime")
);
//ajout du champ archive_date pour la rubrique, possibilité de #ARCHIVE_DATE
$GLOBALS['tables_principales']['spip_rubriques']['field']=
array_merge(
    $GLOBALS['tables_principales']['spip_rubriques']['field'],
    array('archive_date' => "datetime")
);

// Lancement des taches cron pour l'archivage
function archive_taches_generales_cron($taches_generales){ 
       $taches_generales['archive_cron']=1*24*3600;
	    return $taches_generales;
 }

?>
