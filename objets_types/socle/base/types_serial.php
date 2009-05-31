<?php

include_spip('base/serial');

//ajout du champ lieu
global  $tables_principales;
$tables_principales['spip_articles']['field'][_TYPE] = "varchar(10) DEFAULT 'article' NOT NULL";
$tables_principales['spip_articles']['key'][_TYPE] = _TYPE;

$tables_principales['spip_rubriques']['field'][_TYPE] = "varchar(10) DEFAULT 'rubrique' NOT NULL";
$tables_principales['spip_rubriques']['key'][_TYPE] = _TYPE;

?>