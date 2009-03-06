<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function archive_declarer_tables_principales($tables_principales){

    $tables_principales['spip_articles']['field']['archive_date']= "datetime not null";
    $tables_principales['spip_articles']['field']['archive']= "BOOLEAN";    
    
    $tables_principales['spip_rubriques']['field']['archive_date']= "datetime not null";
    $tables_principales['spip_rubriques']['field']['archive']= "BOOLEAN";        
    
    return $tables_principales;
};
?>
