<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function roles_gis_declarer_tables_objets_sql($tables) {

	array_set_merge($tables, 'spip_gis', array(
			'roles_colonne' => 'role',
			'roles_titres' => array(
				'action'  => 'gis_roles:role_action',
				'informatif' => 'gis_roles:role_informatif',
		),
		'roles_objets' => array(
			'cartes' => array(
				'choix' => array('action','informatif'),
				'defaut' => 'action'
			)
		)
	));
	$tables['spip_gis']['champs_critere_gis'][] = 'gis_liens.role as role_gis';
	return $tables;
}

function roles_gis_declarer_tables_auxiliaires($tables) {
	$tables['spip_gis_liens']['field']['role'] = "varchar(30) NOT NULL DEFAULT ''";
	$tables['spip_gis_liens']['key']['PRIMARY KEY'] = 'id_gis,id_objet,objet,role';
	return $tables;
}
