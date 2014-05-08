<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * Declarer le champ archive_date sur les articles et les rubriques et le statut archive
 *
 * @param array $tables
 * 	La définition des objets SPIP
 * @return array
 * 	La définition des objets SPIP modifiés
 */
function archive_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['field']['archive_date'] = "datetime not null";
	$tables['spip_rubriques']['field']['archive_date'] = "datetime not null";
	return $tables;
}
?>
