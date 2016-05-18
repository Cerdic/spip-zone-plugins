<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function xiti_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['xiti_niveaux'] = 'xiti_niveaux';
	$interface['table_des_tables']['xiti_niveaux_liens'] = 'xiti_niveaux_liens';

	return $interface;
}

function xiti_declarer_tables_objets_sql($tables) {
	/* Declaration de la table de points xiti_niveaux */
	$tables['spip_xiti_niveaux'] = array(
		/* Declarations principales */
		'table_objet' => 'xiti_niveaux',
		'table_objet_surnoms' => array('xiti_niveaux'),
		'type' => 'xiti_niveau',
		'type_surnoms' => array('xiti_niveau'),

		/* La table */
		'field' => array(
			'id_xiti_niveau' => 'bigint(21) NOT NULL',
			'titre' => 'text NOT NULL DEFAULT ""',
			'niveau' => 'text NOT NULL DEFAULT ""',
			'xtsite' => 'text NOT NULL DEFAULT ""',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_xiti_niveau'
		),
		'join' => array(
			'id_xiti_niveau' => 'id_xiti_niveau'
		),
		'principale' => 'oui',

		/* Le titre, la date et la gestion du statut */
		'titre' => "titre, '' AS lang",

		/* L'édition, l'affichage et la recherche */
		'page' => false,
		'url_voir' => 'xiti_niveau',
		'url_edit' => 'xiti_niveau_edit',
		'editable' => 'oui',
		'champs_editables' => array('titre', 'niveau', 'xtsite'),
		'champs_versionnes' => array('titre', 'niveau', 'xtsite'),
		'icone_objet' => 'xiti_niveaux',
		'rechercher_champs' => array(
			'titre' => 8
		)
	);

	$tables[]['tables_jointures'][]= 'xiti_niveaux_liens';
	$tables[]['champs_versionnes'][] = 'jointure_xiti_niveaux';

	return $tables;
}

function xiti_declarer_tables_auxiliaires($tables_auxiliaires) {
	$spip_xiti_niveaux_liens = array(
		'id_xiti_niveau' => 'bigint(21) NOT NULL',
		'objet' => 'VARCHAR (25) DEFAULT "" NOT NULL',
		'id_objet' => 'bigint(21) NOT NULL');

	$spip_xiti_niveaux_liens_key = array(
		'PRIMARY KEY' => 'id_xiti_niveau,id_objet,objet',
		'KEY id_xiti_niveau' => 'id_xiti_niveau',
		'KEY id_objet' => 'id_objet',
		'KEY objet' => 'objet'
	);

	$tables_auxiliaires['spip_xiti_niveaux_liens'] = array(
		'field' => &$spip_xiti_niveaux_liens,
		'key' => &$spip_xiti_niveaux_liens_key);

	return $tables_auxiliaires;
}
