<?php

/*
	On ajoute visites_jour et visites_veille à la table spip_referers_articles
*/

function stats_data_declarer_tables_auxiliaires($tables_auxiliaires){
	$tables_auxiliaires['spip_referers_articles']['field']['visites_jour'] = "int UNSIGNED NOT NULL";
	$tables_auxiliaires['spip_referers_articles']['field']['visites_veille'] = "int UNSIGNED NOT NULL";
	return $tables_auxiliaires;
}

