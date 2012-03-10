<?php
/*
 * Plugin Article Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function article_accueil_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_rubriques']['field']['id_article_accueil'] = "bigint(21) DEFAULT '0' NOT NULL";
	return $tables_principales;
}

?>