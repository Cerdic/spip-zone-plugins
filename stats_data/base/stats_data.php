<?php

/*
	On ajoute visites_jour et visites_veille à la table spip_referers_articles
*/

function stats_data_declarer_tables_auxiliaires($tables_auxiliaires){
	$tables_auxiliaires['spip_referers_articles']['field']['visites_jour'] = "int(10) unsigned not null default '0'";
	$tables_auxiliaires['spip_referers_articles']['field']['visites_veille'] = "int(10) unsigned not null default '0'";
	return $tables_auxiliaires;
}

