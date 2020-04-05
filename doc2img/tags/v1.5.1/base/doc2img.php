<?php
/**
 * Plugin Doc2img
 * 
 * Fichier des pipelines en relation avec la base
 * Description des modifications de la base de donnée par le plugin
 * 
 * @package SPIP\Doc2img\Pipelines
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_principales
 * Ajoute un champs "page" sur les documents
 *
 * @param array $tables_principales 
 * 		Tableau de description des tables
 * @return array $tables_principales 
 * 		Tableau de description des tables complété (champ "page" ajouté sur les documents)
 */
function doc2img_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['page'] = "bigint DEFAULT '0' NOT NULL";
	return $tables_principales;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute notre champs dans les champs editables de la table spip_documents
 * 
 * @param array $tables 
 * 		Le tableau des tables des objets déclarés
 * @return array $tables 
 * 		Le tableau des tables complété (champ "page" editable sur les documents)
 */
function doc2img_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'page';
	return $tables;
}
?>