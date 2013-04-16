<?php
/**
 * Plugin Licence
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 * 
 * Déclaration des champs id_licence supplémentaires 
 * 
 * @package SPIP\Licences\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_principales (SPIP)
 * 
 * On ajoute le champ id_licence aux tables spip_articles et spip_documents
 * 
 * @param array $flux
 * 		Le tableau de description des tables
 * @return arrau $flux
 * 		Le tableau de description des tables complétées
 */
function licence_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['id_licence'] = "bigint(21) NOT NULL DEFAULT '0'";
	$tables_principales['spip_articles']['field']['id_licence'] = "bigint(21) NOT NULL DEFAULT '0'";
	return $tables_principales;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * On ajoute nos champs ajoutés dans declarer_tables_principales 
 * dans les champs editables des tables spip_articles et spip_documents.
 * Ils sont également versionnés
 * 
 * @param array $tables
 * 		Le tableau des objets déclarés
 * @return array $tables
 * 		Le tableau des objets déclarés complété
 */
function licence_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['champs_editables'][] = 'id_licence';
	$tables['spip_articles']['champs_versionnes'][] = 'id_licence';
	$tables['spip_documents']['champs_editables'][] = 'id_licence';
	$tables['spip_documents']['champs_versionnes'][] = 'id_licence';
	
	return $tables;
}
?>