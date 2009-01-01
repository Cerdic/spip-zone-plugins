<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_cotes_classes = array(
	"id_classe" => "INT NOT NULL AUTO_INCREMENT",
	  "nom" => "VARCHAR(255) NOT NULL",
	  "descriptif" => "TEXT",
);
					
$spip_cotes_classes_key = array(
  "PRIMARY KEY" => "id_classe"
);

global $table_des_tables;
$table_des_tables['cotes_classes'] = 'cotes_classes';

$tables_principales['spip_cotes_classes'] = array('field' => &$spip_cotes_classes, 'key' => &$spip_cotes_classes_key);
?>
