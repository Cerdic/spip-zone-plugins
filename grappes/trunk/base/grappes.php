<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

function grappes_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_grappes'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_grappes_liens'][] = 'grappes';
	$interface['tables_jointures']['spip_articles'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_breves'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_documents'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_groupes_mots'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_mots'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'grappes_liens';
	$interface['tables_jointures']['spip_syndic'][] = 'grappes_liens';

	$interface['table_des_tables']['grappes']='grappes';
	$interface['table_des_tables']['grappes_liens']='grappes_liens';

	return $interface;
}

function grappes_declarer_tables_objets_sql($tables){
	$tables['spip_grappes'] = array(
		'type' => 'grappe',
		'principale' => 'oui',
		'field' => array(
			"id_grappe" => "bigint(21) NOT NULL",
			"id_admin" => "bigint(21) NOT NULL DEFAULT '0'",
			"titre" => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif" => "text NOT NULL DEFAULT ''",
			"options" => "text NOT NULL DEFAULT ''",
			"liaisons" => "text NOT NULL DEFAULT ''",
			"type" => "varchar(255) NOT NULL DEFAULT ''",
			"visibilite" => "varchar(10) NOT NULL DEFAULT 'public'",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_grappe",
		),
		'titre' => "titre, '' AS lang",
		'date' => "date",
		'page' => 'grappe',
		'url_voir' => 'grappe',
		'url_edit' => 'grappe_edit',
		'editable' => 'oui',
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array(
			'titre' => 8,
			'descriptif' => 5
		),
		'texte_objet' => 'grappes:titre_grappe',
		'texte_objets' => 'grappes:titre_grappes',
		'texte_logo_objet' => 'grappes:titre_logo_grappe',
		'texte_creer' => 'grappes:icone_creation_grappe',
		'texte_modifier' => 'grappes:icone_modifier_grappe',
		'info_aucun_objet' => 'grappes:info_aucune_grappe'
	);
	return $tables;
}

function grappes_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_grappes_liens = array(
		"id_grappe" 	=> "bigint(21) NOT NULL",
		"objet" 	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL",
		"rang" => "bigint(21) NOT NULL"
	);

	$spip_grappes_liens_key = array(
		"PRIMARY KEY" 	=> "id_grappe,id_objet,objet",
		"KEY id_objet" => "id_grappe"
	);

	$tables_auxiliaires['spip_grappes_liens'] = array(
		'field' => &$spip_grappes_liens,
		'key' => &$spip_grappes_liens_key
	);

	return $tables_auxiliaires;
}

?>
