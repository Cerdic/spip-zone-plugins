<?php

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;
global $table_des_tables;

$spip_articles_rubriques = array(
	"id_auto" => "int(11) NOT NULL",
	"id_article" => "int(11) NOT NULL",
	"id_rubrique" => "int(11) NOT NULL"   
);

// Le champ id_auto est créé mais ne sert pas . Spip génére automatiquement la PRIMARY KEY avec l'option auto_increment
// Sans PRIMARY KEY impossible de créer la table
// Pour le plugin, il ne faut PAS que id_breve ou id_article soit en auto_increment
// Donc on créé un champ id_auto qui sert juste à être mis en PRIMARY KEY
$spip_articles_rubriques_key = array(
	"PRIMARY KEY" => "id_auto"
);

$tables_principales['spip_articles_rubriques'] = array(
	'field' => &$spip_articles_rubriques,
	'key' => &$spip_articles_rubriques_key
);

$table_des_tables['articles_rubriques'] = 'articles_rubriques';

?>
