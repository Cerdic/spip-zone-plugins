<?php
//Ajout de champs supplémentaires
include_spip('base/serial');
//ajout du champ archive_date, possibilité de #ARCHIVE_DATE
$GLOBALS['tables_principales']['spip_articles']['field']=
array_merge(
    $GLOBALS['tables_principales']['spip_articles']['field'],
    array('archive_date' => "datetime")
);
?>