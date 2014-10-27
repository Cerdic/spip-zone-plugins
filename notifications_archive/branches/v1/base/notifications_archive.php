<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Archive notifications
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function notifications_archive_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['notifications'] = 'notifications';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function notifications_archive_declarer_tables_objets_sql($tables) {

	$tables['spip_notifications'] = array(
		'type' => 'notification',
		'principale' => "oui",
		'field'=> array(
			"id_notification"    => "bigint(21) NOT NULL",
			"sujet"              => "text NOT NULL DEFAULT ''",
			"texte"              => "text NOT NULL DEFAULT ''",
			"type"               => "varchar(50) NOT NULL DEFAULT ''",
			"recipients"         => "text NOT NULL DEFAULT ''",
			"id_objet"           => "int(11) NOT NULL DEFAULT 0",
			"objet"              => "varchar(50) NOT NULL DEFAULT ''",
			"envoi"              => "varchar(50) NOT NULL DEFAULT ''",			
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_notification",
			"KEY id_objet"        => "id_objet,objet",		          		
		),
		'titre' => "sujet AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('sujet','texte','type','recipients','id_objet','objet','envoi'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("sujet" => 8, "texte" => 6),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}



?>