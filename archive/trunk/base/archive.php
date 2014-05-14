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
	
	/**
	 * Ajouter le nouveau statut "archive" pour les articles
	 * 
	 * TODO : On est obligé d'ajouter tous les statut_images car le code des puce_statuts ne permet pas de n'en déclarer qu'un seul 
	 */
	$tables['spip_articles']['statut_titres']['archive'] = "archive:info_article_archive";
	$tables['spip_articles']['statut_textes_instituer']['archive'] = "archive:texte_statut_archive";
	$tables['spip_articles']['statut_images']['prepa'] = "puce-preparer-8.png";
	$tables['spip_articles']['statut_images']['prop'] = "puce-proposer-8.png";
	$tables['spip_articles']['statut_images']['publie'] = "puce-publier-8.png";
	$tables['spip_articles']['statut_images']['refuse'] = "puce-refuser-8.png";
	$tables['spip_articles']['statut_images']['poubelle'] = "puce-supprimer-8.png";
	$tables['spip_articles']['statut_images']['archive'] = "puce-archiver-8.png";
	if(is_array($tables['spip_articles']['statut'][0]['exception']))
		$tables['spip_articles']['statut'][0]['exception'][] = 'archive';
	else
		$tables['spip_articles']['statut'][0]['exception'] = array($tables['spip_articles']['statut'][0]['exception'],'archive');
	
	return $tables;
}
?>
