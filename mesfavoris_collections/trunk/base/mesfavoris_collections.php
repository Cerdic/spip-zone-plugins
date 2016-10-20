<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function mesfavoris_collections_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['favoris_collections'] = 'favoris_collections';
	
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
function mesfavoris_collections_declarer_tables_objets_sql($tables) {
	$tables['spip_favoris_collections'] = array(
		'type' => 'favoris_collection',
		'principale' => "oui",
		'field'=> array(
			"id_favoris_collection" => "bigint(21) NOT NULL",
			'id_auteur'             => 'bigint(21) not null default 0',
			"titre"                 => "text NOT NULL DEFAULT ''",
			"texte"                 => "text NOT NULL DEFAULT ''",
			"statut"                => "varchar(20)  DEFAULT 'public' NOT NULL", 
			"maj"                   => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_favoris_collection",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'champs_editables'  => array('titre', 'texte'),
		'champs_versionnes' => array('titre', 'texte'),
		'rechercher_champs' => array("titre" => 8, "texte" => 5),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'public'    => 'texte_statut_en_cours_redaction',
			'prive'     => 'texte_statut_propose_evaluation',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'public',
				'previsu'   => 'public,prive',
				'exception' => array('statut','tout'),
			)
		),
		'texte_changer_statut' => 'favoris_collection:texte_changer_statut_favoris_collection', 
	);
	
	return $tables;
}

/**
 * Déclarer les tables auxiliaires des itinéraires
 *
 * @pipeline declarer_tables_auxiliaires
 * 
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function mesfavoris_collections_declarer_tables_principales($tables_principales){
	// Choix des infos de locomotions pour un itinéraire
	$tables_principales['spip_favoris']['field']['id_favoris_collection'] =  'bigint(21) not null default 0';
	$tables_principales['spip_favoris']['key']['KEY id_favoris_collection'] =  'id_favoris_collection';
	
	return $tables_principales;
}
