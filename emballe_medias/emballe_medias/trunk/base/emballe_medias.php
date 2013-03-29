<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Ajouts dans les tables
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline declarer_tables_principales
 * Ajoute un champs em_type sur les articles
 *
 * @param array $tables_principales Tableau de description des tables
 * @return array $tables_principales Tableau de description des tables complété
 */
function emballe_medias_declarer_tables_principales($tables_principales){
	// Extension de la table articles

	$tables_principales['spip_articles']['field']['em_type'] = "VARCHAR(255) DEFAULT 'normal' NOT NULL";

	return $tables_principales;
};

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute notre champs dans les champs editables de la table spip_articles
 * 
 * @param array $tables : le tableau des tables des objets déclarés
 * @return array $tables : le tableau des tables complété 
 */
function emballe_medias_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['champs_editables'][] = 'em_type';
	
	return $tables;
}
?>