<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function tickets_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['tickets']='tickets';
	$interfaces['tables_jointures']['spip_tickets'][]= 'documents_liens';
	
	$interface['tables_jointures']['spip_tickets'][] = 'forums';

	return $interface;
}

function tickets_declarer_tables_objets_sql($tables){
	$tables['spip_tickets'] = array(
		'page'=>'ticket',
		'texte_retour' => 'tickets:icone_retour_ticket',
		'texte_objet' => 'tickets:ticket',
		'texte_objets' => 'tickets:tickets',
		'texte_modifier' => 'tickets:icone_modifier_ticket',
		'icone_objet' => 'ticket',
		'info_aucun_objet'=> 'tickets:info_ticket_aucun',
		'info_1_objet' => 'tickets:info_ticket_1',
		'info_nb_objets' => 'tickets:info_ticket_nb',
		'titre' => "titre",
		'principale' => 'oui',
		'champs_editables' => array('titre', 'texte', 'exemple', 'descriptif','severite', 'tracker', 'composant', 'jalon','version', 'navigateur','sticked'),
		'field'=> array(
			"id_ticket"	=> "bigint(21) NOT NULL",
			"titre"	=> "text NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"id_auteur"	=> "bigint(21) NOT NULL",
			"ip"	=> "varchar(16) DEFAULT '' NOT NULL",
			"id_assigne"	=> "bigint(21) NOT NULL",
			"exemple"	=> "varchar(255) DEFAULT '' NOT NULL",
			"severite"	=> "integer DEFAULT '0' NOT NULL",
			"tracker"	=> "integer DEFAULT '0' NOT NULL",
			"projet"	=> "varchar(60) DEFAULT '' NOT NULL",
			"composant"	=> "varchar(40) DEFAULT '' NOT NULL",
			"version"	=> "varchar(30) DEFAULT '' NOT NULL",
			"jalon"	=> "varchar(30) DEFAULT '' NOT NULL",
			"navigateur" => "varchar(60) DEFAULT '' NOT NULL",
			"sticked" 	=> "varchar(3) DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_ticket",
			"KEY date_modif"	=> "date_modif",
			"KEY id_auteur"	=> "id_auteur",
			"KEY id_assigne"	=> "id_assigne",
			"KEY statut"	=> "statut, date"
		),
		'rechercher_champs' => array(
			'titre' => 8,
			'texte' => 8,
			'severite' => 3,
			'tracker' => 3,
			'composant' => 3,
			'projet' => 3,
			'jalon' => 3
		),
		'champs_versionnes' => array('titre','texte','severite','projet','tracker','composant','projet','jalon','version','navigateur','sticked')
	);
	return $tables;
}

?>
