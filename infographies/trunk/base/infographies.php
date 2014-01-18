<?php
/**
 * Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Distribué sous licence GNU/GPL
 *
 * Déclarations relatives à la base de données
 * 
 * @package SPIP\Infographies\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer les interfaces des tables spip_infographies et spip_infographies_datas
 * pour le compilateur
 * 
 * On traite également les raccours sur la balise CREDITS
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function infographies_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['infographies'] = 'infographies';
	$interface['table_des_tables']['infographies_datas'] = 'infographies_datas';
	$interface['table_des_tables']['infographies_donnees'] = 'infographies_donnees';
	
	$interface['table_des_traitements']['AXE_X'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['AXE_Y'][] = _TRAITEMENT_TYPO;
	$interface['table_des_traitements']['CREDITS'][] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['COMMENTAIRE'][] = _TRAITEMENT_RACCOURCIS;
	
	$interface['tables_jointures']['spip_infographies'][] = 'infographies_datas';
	
	return $interface;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * Déclaration de l'objet supplémentaire grappes
 * 
 * @param array $tables
 * 	Le tableau de définition de tous les objets
 * @return array $tables
 * 	Le tableau complété avec notre objet supplémentaire
 */
function infographies_declarer_tables_objets_sql($tables){
	$tables['spip_infographies'] = array(
		'type' => 'infographie',
		'principale' => 'oui',
		'titre' => "titre, '' AS lang",
		'date' => "date",
		'page' => 'infographie',
		'url_voir' => 'infographie',
		'url_edit' => 'infographie_edit',
		'editable' => 'oui',
		'texte_changer_statut' => 'infographie:texte_infographie_statut',
		'field' => array(
			"id_infographie" => "bigint(21) NOT NULL",
			"titre" => "varchar(255) NOT NULL DEFAULT ''",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"credits" => "text DEFAULT '' NOT NULL",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_infographie",
			"KEY statut" => "statut, date",
		),
		'join' => array(
			"id_infographie"=>"id_infographie"
		),
		'champs_editables' => array('titre','texte','credits','date'),
		'champs_versionnes' => array('titre','texte','credits','date','jointure_auteurs'),
		'champs_contenu' => array('texte','credits'),
		'rechercher_champs' => array(
			'titre' => 8,
			'texte' => 5,
			'credits' => 1 
		),
		'rechercher_jointures' => array(
			'auteur' => array('nom' => 10),
		),
		'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => 'statut'
			)
		),
		'statut_titres' => array(
			'prepa'=>'info_article_redaction',
			'prop'=>'infographie:info_infographie_proposee',
			'publie'=>'infographie:info_infographie_publiee',
			'refuse'=>'infographie:info_infographie_refusee',
			'poubelle'=>'infographie:info_infographie_supprimee'
		),
		'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'infographie:texte_statut_propose_evaluation',
			'publie' => 'infographie:texte_statut_publie',
			'refuse' => 'infographie:texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
	);
	
	$tables['spip_infographies_datas'] = array(
		'type' => 'infographies_data',
		'principale' => 'non',
		'field' => array(
			"id_infographies_data" => "bigint(21) NOT NULL",
			"titre" => "varchar(255) NOT NULL DEFAULT ''",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"credits" => "text DEFAULT '' NOT NULL",
			"css_class" => "varchar(255) NOT NULL DEFAULT ''",
			"axe_x" => "varchar(255) NOT NULL DEFAULT ''",
			"axe_y" => "varchar(255) NOT NULL DEFAULT ''",
			"unite" => "varchar(255) NOT NULL DEFAULT ''",
			"type" => "varchar(255) NOT NULL DEFAULT ''",
			"url_externe" => "varchar(255) NOT NULL DEFAULT ''",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_infographies_data"
		),
		'titre' => "titre, '' AS lang",
		'date' => "date",
		'champs_editables' => array('titre','texte','credits','css_class','axe_x','axe_y','unite','type','url_externe','date'),
		'champs_versionnes' => array('titre','texte','credits','css_class','axe_x','axe_y','unite','type','url_externe','date'),
		'champs_contenu' => array('texte','credits','css_class','axe_x','axe_y','unite','url_externe'),
		'rechercher_champs' => array(
			'titre' => 8,
			'texte' => 3,
			'credits' => 1
		)
	);
	
	$tables['spip_infographies_donnees'] = array(
		'type' => 'infographies_donnee',
		'principale' => 'non',
		'field' => array(
			"id_infographies_donnee" => "bigint(21) NOT NULL",
			"id_infographies_data" => "bigint(21) NOT NULL",
			"rang" => "int NOT NULL DEFAULT 0",
			"axe_x" => "varchar(255) NOT NULL DEFAULT ''",
			"axe_y" => "varchar(255) NOT NULL DEFAULT ''",
			"commentaire" => "text NOT NULL",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_infographies_donnee",
		),
		'titre' => "commentaire, '' AS lang",
		'date' => "date",
		'editable' => 'oui',
		'champs_editables' => array('rang','id_infographies_data','axe_x','axe_y','commentaire','date'),
		'champs_versionnes' => array('rang','id_infographies_data','axe_x','axe_y','commentaire','date'),
		'champs_contenu' => array('axe_x','axe_y','axe_x','commentaire'),
		'rechercher_champs' => array(
			'axe_x' => 8,
			'axe_y' => 8,
			'commentaire' => 8
		)
	);
	return $tables;
}

function infographies_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_infographies_datas_liens = array(
		"id_infographies_data" => "bigint(21) NOT NULL",
		"objet" => "VARCHAR (25) DEFAULT '' NOT NULL",
		"id_objet" => "bigint(21) NOT NULL");

	$spip_infographies_datas_liens_key = array(
		"PRIMARY KEY" => "id_infographies_data,id_objet,objet",
		"KEY id_objet" => "id_infographies_data");

	$tables_auxiliaires['spip_infographies_datas_liens'] = array(
		'field' => &$spip_infographies_datas_liens,
		'key' => &$spip_infographies_datas_liens_key);

	return $tables_auxiliaires;
}
?>
