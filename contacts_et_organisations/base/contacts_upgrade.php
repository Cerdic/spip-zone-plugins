<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

include_spip('inc/meta');
include_spip('base/create');

function contacts_upgrade($nom_meta_base_version, $version_cible){
 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		spip_log('Tables C&O correctement créées','contacts');
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.1.0","<")){
		sql_alter("TABLE spip_contacts CHANGE prenom prenom tinytext NOT NULL DEFAULT ''");
		ecrire_meta($nom_meta_base_version, $current_version="1.1.0");
	}
	if (version_compare($current_version,"1.1.1","<")){
		maj_tables('spip_contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.1.1");
	}
	if (version_compare($current_version,"1.1.2","<")){
		// dupliquer id_ pour les jointures automatiques.
		maj_tables(array('spip_contacts', 'spip_comptes'));
		sql_alter('TABLE spip_contacts ADD INDEX (id_contact)');
		sql_alter('TABLE spip_comptes ADD INDEX (id_compte)');
		sql_alter('TABLE spip_comptes_contacts ADD INDEX (id_contact)');
		sql_update('spip_contacts', array('id_contact'=>'id_auteur'));
		sql_update('spip_comptes', array('id_compte'=>'id_auteur'));
		ecrire_meta($nom_meta_base_version, $current_version="1.1.2");
	}
	if (version_compare($current_version,"1.2.0","<")){
		// on passe de compte a organisation...
		sql_alter('TABLE spip_comptes DROP INDEX id_compte');
		sql_alter('TABLE spip_comptes_contacts DROP INDEX id_compte');
		
		sql_alter('TABLE spip_comptes RENAME spip_organisations');
		sql_alter('TABLE spip_comptes_contacts RENAME spip_organisations_contacts');

		sql_alter('TABLE spip_organisations CHANGE id_compte id_organisation bigint(21) NOT NULL');
		sql_alter('TABLE spip_organisations_contacts CHANGE id_compte id_organisation bigint(21) NOT NULL');

		sql_alter('TABLE spip_organisations ADD INDEX (id_organisation)');
		sql_alter('TABLE spip_organisations_contacts ADD INDEX (id_organisation)');		
		ecrire_meta($nom_meta_base_version, $current_version="1.2.0");
	}
	if (version_compare($current_version,"1.2.1","<")){
		// on modifie quelques champs de la table organisations
		sql_alter("TABLE spip_organisations CHANGE type statut_juridique TINYTEXT NOT NULL DEFAULT ''"); // renomme le champ 'type' en 'statut_juridique'
		sql_alter("TABLE spip_organisations CHANGE siret identification TINYTEXT NOT NULL DEFAULT ''"); // renomme le champ 'siret' en 'identification'
		sql_alter("TABLE spip_organisations ADD activite TINYTEXT NOT NULL DEFAULT '' AFTER identification"); // ajoute le champ 'activite'
		spip_log('Tables correctement passsées en version 1.2.1','contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.2.1");
	}
	if (version_compare($current_version,"1.3.0","<")){
		// les clés primaires des tables contacts et organisations
		// passent sur le id_contact et id_organisation au lieu du id_auteur
		// afin de gérer éventuellement des contacts/organisations autonomes.
		sql_alter('TABLE spip_organisations DROP INDEX id_organisation');
		sql_alter('TABLE spip_organisations DROP PRIMARY KEY');
		sql_alter('TABLE spip_organisations CHANGE id_auteur id_auteur bigint(21) NOT NULL'); 
		sql_alter('TABLE spip_organisations CHANGE id_organisation id_organisation bigint(21) NOT NULL auto_increment PRIMARY KEY'); 
		sql_alter('TABLE spip_organisations ADD INDEX (id_auteur)');
		
		sql_alter('TABLE spip_contacts DROP INDEX id_contact');
		sql_alter('TABLE spip_contacts DROP PRIMARY KEY');
		sql_alter('TABLE spip_contacts CHANGE id_auteur id_auteur bigint(21) NOT NULL');  
		sql_alter('TABLE spip_contacts CHANGE id_contact id_contact bigint(21) NOT NULL auto_increment PRIMARY KEY');
		sql_alter('TABLE spip_contacts ADD INDEX (id_auteur)');
		
		spip_log('Tables correctement passsées en version 1.3.0','contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.3.0");
	}

	if (version_compare($current_version,"1.3.1","<")){
		if (!sql_alter("TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) {
			spip_log('Probleme lors de la modif de la table spip_contacts','contacts');
		} else {
			spip_log('Table spip_contacts correctement passsée en version 1.3.1','contacts');
		}
		if (!sql_alter("TABLE spip_organisations CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) {
			spip_log('Probleme lors de la modif de la table spip_organisations','contacts');
		} else {
			spip_log('Table spip_organisations correctement passsée en version 1.3.1','contacts');
		}

		ecrire_meta($nom_meta_base_version, $current_version="1.3.1");
	}

	if (version_compare($current_version,"1.3.2","<")){
		maj_tables('spip_contacts_liens');
		$res = sql_select(array("id_auteur","id_contact"),"spip_contacts");
		while ($row = sql_fetch($res)) {
		    sql_insertq(
		        'spip_contacts_liens',
                array(
                    "id_objet" => $row['id_auteur'],
                    "objet" => "auteur",
                    "id_contact" => $row['id_contact']
                )
		    );
		}

		if (!sql_alter("TABLE spip_contacts DROP id_auteur")) {
			spip_log('Probleme lors de la modif de la table spip_contacts','contacts');
		} else {
			spip_log('Table spip_contacts correctement passsée en version 1.3.2','contacts');
		}

		
		spip_log('Tables correctement passsées en version 1.3.2','contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.3.2");
    }

	// le champ descriptif ne changeait pas sur les nouvelles installations (c'etait encore declare tinytext
    if (version_compare($current_version,"1.3.3","<")){
		if (!sql_alter("TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) {
		ecrire_meta($nom_meta_base_version, $current_version="1.3.3");
	}
}


function contacts_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_organisations");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_contacts_liens");
	sql_drop_table("spip_organisations_contacts");	
	
	effacer_meta($nom_meta_base_version);
}

?>
