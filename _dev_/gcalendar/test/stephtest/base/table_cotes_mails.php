<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_cotes_mails = array(
	"id_mail" => "INT NOT NULL AUTO_INCREMENT",
  "type" => "VARCHAR(255) NOT NULL",
  "id" => "bigint(21)",
  "contenu" => "TEXT",
  "date" => "VARCHAR(255) NOT NULL"
);
					
$spip_cotes_mails_key = array(
  "PRIMARY KEY" => "id_mail"
);

global $table_des_tables;
$table_des_tables['cotes_mails'] = 'cotes_mails';
$tables_principales['spip_cotes_mails'] = array('field' => &$spip_cotes_mails, 'key' => &$spip_cotes_mails_key);
?>
