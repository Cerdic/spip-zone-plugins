<?php
$CompteurGraphiqueTable = array( 
    "id_compteur" => "INTEGER NOT NULL AUTO_INCREMENT", 
    "decompte" => "INTEGER DEFAULT NULL", 
    "id_article" => "INTEGER DEFAULT NULL", 
    "id_rubrique" => "INTEGER DEFAULT NULL", 
    "statut" => "INTEGER DEFAULT NULL", 
    "longueur" => "INTEGER DEFAULT NULL", 
    "habillage" => "INTEGER DEFAULT NULL"); 

$CompteurGraphiqueTable_key = array( 
"PRIMARY KEY" => "id_compteur");

$GLOBALS['tables_principales']['ext_compteurgraphique'] = 
    array('field' => &$CompteurGraphiqueTable, 'key' => &$CompteurGraphiqueTable_key);

?>