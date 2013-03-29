<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Ajouts dans les tables
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline declarer_tables_principales
 * Ajoute un champs em_type sur les articles
 *
 * @param array $tables_principales Array de description des tables
 */
function emballe_medias_declarer_tables_principales($tables_principales){
	// Extension de la table articles

	$tables_principales['spip_articles']['field']['em_type'] = "VARCHAR(255) DEFAULT 'normal' NOT NULL";

	return $tables_principales;
};
?>