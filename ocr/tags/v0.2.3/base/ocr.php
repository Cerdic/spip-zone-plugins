<?php
/**
 * Plugin ocr
 * 
 * Fichier des pipelines en relation avec la base
 * Description des modifications de la base de données par le plugin
 * 
 * @package SPIP\ocr\Pipelines
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_principales
 * Ajoute un champ "ocr" sur les documents
 *
 * @param array $tables_principales 
 * 		Tableau de description des tables
 * @return array $tables_principales 
 * 		Tableau de description des tables complété (champ "ocr" ajouté sur les documents)
 */
function ocr_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['ocr'] = "longtext DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['ocr_analyse'] = "VARCHAR(3) NOT NULL default 'non'";
	return $tables_principales;
}
?>
