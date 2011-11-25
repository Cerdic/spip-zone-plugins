<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

include_spip('inc/meta');
include_spip('base/create');

function contacts_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('creer_base'),
	);
	
	$maj['1.1.0'] = array(
		array('sql_alter', 'TABLE spip_contacts CHANGE prenom prenom tinytext NOT NULL DEFAULT ""'),
	);
	
	$maj['1.1.1'] = array(
		array('maj_tables', 'spip_contacts'),
	);
	
	// Dupliquer pour les jointures automatiques
	$maj['1.1.2'] = array(
		array('maj_tables', array('spip_contacts', 'spip_comptes')),
		array('sql_alter', 'TABLE spip_contacts ADD INDEX (id_contact)'),
		array('sql_alter', 'TABLE spip_comptes ADD INDEX (id_compte)'),
		array('sql_alter', 'TABLE spip_comptes_contacts ADD INDEX (id_contact)'),
		array('sql_update', 'spip_contacts', array('id_contact'=>'id_auteur')),
		array('sql_update', 'spip_comptes', array('id_compte'=>'id_auteur')),
	);
	
	// On passe de compte à organisation
	$maj['1.2.0'] = array(
		array('sql_alter', 'TABLE spip_comptes DROP INDEX id_compte'),
		array('sql_alter', 'TABLE spip_comptes_contacts DROP INDEX id_compte'),
		
		array('sql_alter', 'TABLE spip_comptes RENAME spip_organisations'),
		array('sql_alter', 'TABLE spip_comptes_contacts RENAME spip_organisations_contacts'),

		array('sql_alter', 'TABLE spip_organisations CHANGE id_compte id_organisation bigint(21) NOT NULL'),
		array('sql_alter', 'TABLE spip_organisations_contacts CHANGE id_compte id_organisation bigint(21) NOT NULL'),

		array('sql_alter', 'TABLE spip_organisations ADD INDEX (id_organisation)'),
		array('sql_alter', 'TABLE spip_organisations_contacts ADD INDEX (id_organisation)'),		
	);
	
	// On modifie quelques champs de la table organisations
	$maj['1.2.1'] = array(
		// renomme le champ 'type' en 'statut_juridique'
		array('sql_alter', "TABLE spip_organisations CHANGE type statut_juridique TINYTEXT NOT NULL DEFAULT ''"),
		// renomme le champ 'siret' en 'identification'
		array('sql_alter', "TABLE spip_organisations CHANGE siret identification TINYTEXT NOT NULL DEFAULT ''"),
		// ajoute le champ 'activite'
		array('sql_alter', "TABLE spip_organisations ADD activite TINYTEXT NOT NULL DEFAULT '' AFTER identification"),
	);
	
	// Les clés primaires des tables contacts et organisations
	// passent sur le id_contact et id_organisation au lieu du id_auteur
	// afin de gérer éventuellement des contacts/organisations autonomes.
	$maj['1.3.0'] = array(
		array('sql_alter', 'TABLE spip_organisations DROP INDEX id_organisation'),
		array('sql_alter', 'TABLE spip_organisations DROP PRIMARY KEY'),
		array('sql_alter', 'TABLE spip_organisations CHANGE id_auteur id_auteur bigint(21) NOT NULL'), 
		array('sql_alter', 'TABLE spip_organisations CHANGE id_organisation id_organisation bigint(21) NOT NULL auto_increment PRIMARY KEY'),
		array('sql_alter', 'TABLE spip_organisations ADD INDEX (id_auteur)'),
		
		array('sql_alter', 'TABLE spip_contacts DROP INDEX id_contact'),
		array('sql_alter', 'TABLE spip_contacts DROP PRIMARY KEY'),
		array('sql_alter', 'TABLE spip_contacts CHANGE id_auteur id_auteur bigint(21) NOT NULL'), 
		array('sql_alter', 'TABLE spip_contacts CHANGE id_contact id_contact bigint(21) NOT NULL auto_increment PRIMARY KEY'),
		array('sql_alter', 'TABLE spip_contacts ADD INDEX (id_auteur)'),
	);
	
	$maj['1.3.1'] = array(
		array('sql_alter', "TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL"),
		array('sql_alter', "TABLE spip_organisations CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL"),
	);
	
	$maj['1.3.3'] = array(
		array('sql_alter', "TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL"),
	);
	
	// le champ id_auteur sur spip_organisations peut ne pas etre unique si une organisation
	// n'est pas liee a un auteur (id_auteur=0). Plus tard, il faudra certainement mettre une
	// table de relation spip_organisations_liens pour cela.
	$maj['1.3.4'] = array(
		// pas de UNIQUE sur l'index auteur
		array('sql_alter', "TABLE spip_organisations DROP INDEX id_auteur"),
		array('sql_alter', "TABLE spip_organisations CHANGE id_auteur id_auteur bigint(21) DEFAULT 0 NOT NULL"),
		array('sql_alter', "TABLE spip_organisations ADD INDEX (id_auteur)"),
	);
	
	// Le champ id_parent sur spip_organisations pour définir des hiérarchies d'organisations.
	$maj['1.3.5'] = array(
		array('sql_alter', "TABLE spip_organisations ADD COLUMN id_parent bigint(21) DEFAULT 0 NOT NULL"),
	);
	
	// Le champ type_liaison sur spip_organisations_contacts pour définir des types de liaisons donc.
	$maj['1.3.6'] = array(
		array('sql_alter', "TABLE spip_organisations_contacts ADD COLUMN type_liaison tinytext NOT NULL DEFAULT ''"),
	);
	
	// On crée la table spip_organisations_liens
	// Le champ type_liaison sur spip_organisations_contacts pour définir des types de liaisons donc.
	$maj['1.3.7'] = array(
		array('maj_tables', 'spip_organisations_liens'),
	);
	
	// Coquille sur la clé de spip_organisations_liens
	$maj['1.4.1'] = array(
		array('sql_alter', 'TABLE spip_organisations DROP INDEX id_contact'),
		array('sql_alter', 'TABLE spip_organisations ADD INDEX (id_organisation)'),
	);
	
	// Rajout d'un type_liaison dans les liens
	$maj['1.4.2'] = array(
		array('maj_tables', array('spip_contacts_liens', 'spip_organisations_liens')),
        array('sql_alter', 'TABLE `spip_organisations_liens` DROP PRIMARY KEY'),
        array('sql_alter', 'TABLE `spip_organisations_liens` ADD PRIMARY KEY ( `id_organisation` , `id_objet` , `objet`, `type_liaison`(25)) '),
        array('sql_alter', 'TABLE `spip_contacts_liens` DROP PRIMARY KEY'),
        array('sql_alter', 'TABLE `spip_contacts_liens` ADD PRIMARY KEY ( `id_contact` , `id_objet` , `objet`, `type_liaison`(25)) '),
	);
	
	/*
	Il s'agissait de supprimer spip_organisations_contacts
	pour le mettre dans spip_organisations_liens...
	ce qui s'est avéré très bugué en spip 2.1...
	La version 1.6.0 fait l'inverse de 1.5.0 du coup, pour remettre dans l'ordre
	*/
	$maj['1.6.0'] = array(
		array('contacts_maj_1_6_0'),
	);
	
	$maj['1.7.1'] = array(
		array('contacts_migrer_liens_auteurs'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function contacts_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_organisations");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_contacts_liens");
	sql_drop_table("spip_organisations_contacts");	
	
	effacer_meta($nom_meta_base_version);
}

function contacts_maj_1_6_0(){
	// remettre spip_organisations_contacts si besoin
	creer_base();
	
	// repeupler
	$contacts = sql_allfetsel(
		array('id_objet AS id_contact', 'id_organisation', 'type_liaison'),
		'spip_organisations_liens',
		array('objet='.sql_quote('contact'), 'id_objet > 0')
	);
	if ($contacts) {
		$inserts = array();
		foreach ($contacts as $r) {
			$inserts[] = array(
				'id_organisation' => $r['id_organisation'],
				'id_contact' => $r['id_contact'],
                'type_liaison' => $r['type_liaison'],
			);
		}
		if ($inserts) {
			sql_insertq_multi('spip_organisations_contacts', $inserts);
		}
	}

	// enlever les contacts de spip_organisations_liens
	sql_delete('spip_organisations_liens', 'objet='.sql_quote('contact'));
}

function contacts_migrer_liens_auteurs() {
	// remettre id_auteur sur spip_contacts et spip_organisations
	include_spip('base/create');
	maj_tables(array('spip_contacts', 'spip_organisations'));
	sql_alter('TABLE spip_contacts ADD INDEX (id_auteur)');
	sql_alter('TABLE spip_organisations ADD INDEX (id_auteur)');

	// pour chaque table, remettre les petits auteurs dans les tables
	foreach (array('spip_contacts', 'spip_organisations') as $table) {
		$_id = id_table_objet($table);
		$auteurs = sql_allfetsel(
			array($_id, 'id_objet AS id_auteur'),
			$table . '_liens',
			array('objet='.sql_quote('auteur'), 'id_objet > 0'));
		if ($auteurs) {
			// on supprime 1 par 1 en cas de timeout
			foreach ($auteurs as $r) {
				sql_updateq($table, array('id_auteur' => $r['id_auteur']), $_id . '=' . $r[$_id]);
				sql_delete($table . '_liens',
					array('objet='.sql_quote('auteur'), 'id_objet=' . $r['id_auteur'], $_id . '=' . $r[$_id]));
			}
			$auteurs = sql_allfetsel(
				array($_id, 'id_objet AS id_auteur'),
				$table . '_liens',
				array('objet='.sql_quote('auteur'), 'id_objet > 0'));
			if (!$auteurs) {
				// enlever eventuellement des id_auteur = 0 ?
				sql_delete($table . '_liens', 'objet='.sql_quote('auteur'));
			} 
		}
	}
}

?>
