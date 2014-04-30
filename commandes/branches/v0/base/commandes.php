<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function commandes_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['commandes'] = 'commandes';
	$interface['table_des_tables']['commandes_details'] = 'commandes_details';
	
	$interface['table_date']['commandes'] = 'date';
	
	$interface['table_titre']['commandes'] = 'reference as titre, "" as lang';
	
	return $interface;
}

function commandes_declarer_tables_principales($tables_principales){
	// Table commandes
	$commandes = array(
		'id_commande' => 'bigint(21) not null',
		'reference' => 'varchar(255) not null default ""',
		'id_auteur' => 'bigint(21) not null default 0',
		'statut' => 'varchar(25) not null default "encours"', // pourra être "encours", "paye", "envoye", "retour", "retour_partiel"...
		'date' => 'datetime not null default "0000-00-00 00:00:00"',
		'date_paiement' => 'datetime not null default "0000-00-00 00:00:00"',
		'date_envoi' => 'datetime not null default "0000-00-00 00:00:00"',
		'maj' => 'timestamp'
	);
	
	$commandes_cles = array(
		'PRIMARY KEY' => 'id_commande',
		'KEY id_auteur' => 'id_auteur'
	);
	
	$tables_principales['spip_commandes'] = array(
		'field' => &$commandes,
		'key' => &$commandes_cles,
		'join'=> array(
			'id_commande' => 'id_commande'
		)
	);

	
	// Table commandes_details
	$commandes_details = array(
		'id_commandes_detail' => 'bigint(21) not null',
		'id_commande' => 'bigint(21) not null default 0',
		'descriptif' => 'text not null default ""',
		'quantite' => 'int not null default 0',
		'prix_unitaire_ht' => 'float not null default 0',
		'taxe' => 'decimal(4,3) not null default 0',
		'statut' => 'varchar(25) not null default ""',
		'objet' => 'varchar(25) not null default ""',
		'id_objet' => 'bigint(21) not null default 0',
		'maj' => 'timestamp'
	);
	
	$commandes_details_cles = array(
		'PRIMARY KEY' => 'id_commandes_detail',
		'KEY id_commande' => 'id_commande'
	);
	
	$tables_principales['spip_commandes_details'] = array(
		'field' => &$commandes_details,
		'key' => &$commandes_details_cles,
		'join'=> array(
			'id_commandes_detail' => 'id_commandes_detail',
			'id_commande' => 'id_commande'
		)
	);

	return $tables_principales;
}


function commandes_rechercher_liste_des_champs($tables){
	$tables['commande']['reference'] = 8;
	return $tables;
}


function commandes_rechercher_liste_des_jointures($tables){
	$tables['commande']['auteur']['nom'] = 1;
	$tables['commande']['commandes_detail']['descriptif'] = 4;
	return $tables;
}

// definir la jointur commande_auteur qui n'est pas sur spip_commandes_auteurs
// cf. inc/rechercher.php
function inc_rechercher_joints_commande_auteur_dist($table, $table_liee, $ids, $serveur) {
	if (!autoriser('voir', 'commande')) {
		return array("id_commande", "id_auteur", array());
	}
	$s = sql_select("id_commande, id_auteur", "spip_commandes", sql_in("id_auteur", $ids), '','','','',$serveur);
	return array("id_commande", "id_auteur", $s);
}



?>
