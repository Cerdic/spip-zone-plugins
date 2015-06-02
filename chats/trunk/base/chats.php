<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function chats_declarer_tables_objets_sql($tables){
	$tables['spip_chats'] = array(
	
		'principale' => "oui",
		'field'=> array(
			"id_chat"        => "bigint(21) NOT NULL",
			"id_rubrique"    => "bigint(21) NOT NULL DEFAULT 0",
			"nom"            => "tinytext DEFAULT '' NOT NULL",
			"race"           => "tinytext DEFAULT '' NOT NULL",
			"date"           => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_naissance" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"robe"           => "tinytext DEFAULT '' NOT NULL",
			"infos"          => "text DEFAULT '' NOT NULL",
			"statut"         => "varchar(255) DEFAULT '0' NOT NULL",
			"lang"           => "VARCHAR(10) DEFAULT '' NOT NULL",
			"langue_choisie" => "VARCHAR(3) DEFAULT 'non'",
			"id_trad"        => "bigint(21) DEFAULT '0' NOT NULL",
			"maj"            => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"      => "id_chat",
			"KEY id_rubrique"  => "id_rubrique",
		),
		'titre' => "nom AS titre, '' AS lang",
		'date' => "date",

		'champs_editables' => array(
			"nom", "race", "robe", "infos", "date_naissance"
		),
		'champs_versionnes' => array(
			"nom",  "race", "robe", "infos",  "date_naissance", 
		),
		'rechercher_champs' => array(
			'nom' => 8, 'race' => 1, 'robe' => 1, 'infos' => 2
		),
		'tables_jointures' => array(
			'chats_liens'
		),

		'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'statut_textes_instituer' => 	array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'texte_changer_statut' => 'chat:texte_changer_statut',
		
	);
	
	return $tables;
}



function chats_declarer_tables_auxiliaires($tables) {
	$tables['spip_chats_liens'] = array(
		'field' => array(
			"id_chat"  => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet" => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"    => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"       => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY" => "id_chat,id_objet,objet",
			"KEY id_chat" => "id_chat"
		)
	);
	return $tables;
}

function chats_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['chats'] = 'chats';
	return $interfaces;
}

