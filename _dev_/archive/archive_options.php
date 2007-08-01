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

?>
