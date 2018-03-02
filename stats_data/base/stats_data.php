<?php

/*
	On ajoute visites_jour et visites_veille à la table spip_referers_articles
*/

function stats_data_declarer_tables_auxiliaires($tables_auxiliaires){
	$tables_auxiliaires['spip_referers_articles']['field']['visites_jour'] = "int(10) unsigned";
	$tables_auxiliaires['spip_referers_articles']['field']['visites_veille'] = "int(10) unsigned";
	return $tables_auxiliaires;
}

