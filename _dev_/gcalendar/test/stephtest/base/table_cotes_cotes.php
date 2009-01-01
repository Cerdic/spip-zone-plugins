<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_cotes_cotes = array(
	"id" => "INT NOT NULL AUTO_INCREMENT",
	"id_etudiant" => "bigint(21)",
	  "id_classe" => "bigint(21)",
	  "id_exercice" => "bigint(21)",
	  "cote" => "float(10,1)",
	  "commentaire" => "TEXT",
	  "id_coteur" => "INT(3) NOT NULL",
	  "date_cote" => "datetime",
	  "envoi_cote" => "datetime",
	  
);
					
$spip_cotes_cotes_key = array(
  "PRIMARY KEY" => "id"
);

global $table_des_tables;
$table_des_tables['cotes_cotes'] = 'cotes_cotes';

$tables_principales['spip_cotes_cotes'] = array('field' => &$spip_cotes_cotes, 'key' => &$spip_cotes_cotes_key);
?>
