<?php
global $tables_principales;
global $tables_auxiliaires;

$spip_cotes_etudiants = array(
	"id_etudiant" => "INT NOT NULL AUTO_INCREMENT",
  "nom" => "VARCHAR(255) NOT NULL",
  "prenom" => "VARCHAR(255) NOT NULL",
  "id_classe" => "bigint(21)",
  "commentaire" => "TEXT",
  "mail" => "VARCHAR(255) NOT NULL"
);
					
$spip_cotes_etudiants_key = array(
  "PRIMARY KEY" => "id_etudiant"
);

global $table_des_tables;
$table_des_tables['cotes_etudiants'] = 'cotes_etudiants';

$tables_principales['spip_cotes_etudiants'] = array('field' => &$spip_cotes_etudiants, 'key' => &$spip_cotes_etudiants_key);
?>
