<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function notifavancees_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['notifications_abonnements'] = 'notifications_abonnements';
	
	// Traitements sur certains champs
	// On désarialise d'avance les tableaux
	$interface['table_des_traitements']['MODES']['notifications_abonnements'] = 'unserialize(%s)';
	$interface['table_des_traitements']['PREFERENCES']['notifications_abonnements'] = 'unserialize(%s)';
	
	return $interface;
}

function notifavancees_declarer_tables_principales($tables_principales){
	//-- Table notifications -----------------------------------------------------------
	$notifications_abonnements = array(
		'id_notifications_abonnement' => 'bigint(21) NOT NULL',
		'id_auteur' => 'bigint(21) not null default 0',
		'contact' => 'tinytext not null default ""', // pour ceux qui n'ont pas un compte auteur
		'quoi' => 'tinytext not null',
		'id' => 'bigint(21) not null default 0',
		'preferences' => 'text not null default ""', // pour les options *de l'abonnement* et non de la notif
		'modes' => 'text not null default ""',
		'actif' => 'tinyint(1) not null default 1'
	);
	
	$notifications_abonnements_cles = array(
		'PRIMARY KEY' => 'id_notifications_abonnement',
		'KEY id_auteur' => 'id_auteur'
	);
	
	$tables_principales['spip_notifications_abonnements'] = array(
		'field' => &$notifications_abonnements,
		'key' => &$notifications_abonnements_cles,
		'join'=> array(
			'id_notification' => 'id_notifications_abonnement'
		)
	);

	return $tables_principales;
}

?>
