<?php

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$spip_breves_articles = array(
	"id_auto" => "int(11) NOT NULL",
	"id_breve" => "int(11) NOT NULL",
	"id_article" => "int(11) NOT NULL"   
);

// Le champ id_auto est créé mais ne sert pas . Spip génére automatiquement la PRIMARY KEY avec l'option auto_increment
// Sans PRIMARY KEY impossible de créer la table
// Pour le plugin, il ne faut PAS que id_breve ou id_article soit en auto_increment
// Donc on créé un champ id_auto qui sert juste à être mis en PRIMARY KEY
$spip_breves_articles_key = array(
	"PRIMARY KEY" => "id_auto"
);

$tables_principales['spip_breves_articles'] = array(
	'field' => &$spip_breves_articles,
	'key' => &$spip_breves_articles_key
);

$table_des_tables['breves_articles'] = 'breves_articles';

?>
