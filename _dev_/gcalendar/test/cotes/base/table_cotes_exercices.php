<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_cotes_exercices = array(
	"id_exercice" => "INT NOT NULL AUTO_INCREMENT",
  "titre" => "VARCHAR(255) NOT NULL",
  "description" => "TEXT",
  "id_classe" => "bigint(21)",
  "cote_max" => "bigint(21)",
  "facteur" => "bigint(21)"
);
					
$spip_cotes_exercices_key = array(
  "PRIMARY KEY" => "id_exercice"
);

global $table_des_tables;
$table_des_tables['cotes_exercices'] = 'cotes_exercices';

$tables_principales['spip_cotes_exercices'] = array('field' => &$spip_cotes_exercices, 'key' => &$spip_cotes_exercices_key);
?>
