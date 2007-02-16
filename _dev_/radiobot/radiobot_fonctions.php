<?php
//declarer les champs supplémentaires dans la table spip_document
include_spip("base/serial");
global $tables_principales;

$tables_principales['spip_documents']['field']['actif']=
"CHAR(3) NOT NULL";

$tables_principales['spip_documents']['field']['banni']=
"CHAR(3) NOT NULL";
?>