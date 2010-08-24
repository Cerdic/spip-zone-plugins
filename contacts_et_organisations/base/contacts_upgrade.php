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
		sql_alter('TABLE spip_organisations CHANGE type statut_juridique'); // renomme le champ 'type' en 'statut_juridique'
		sql_alter('TABLE spip_organisations CHANGE siret identification'); // renomme le champ 'siret' en 'identification'
		sql_alter('TABLE spip_organisations ADD activite TINYTEXT NOT NULL AFTER identification'); // ajoute le champ 'activite'
		ecrire_meta($nom_meta_base_version, $current_version="1.2.1");
	}

}

function contacts_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_organisations");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_organisations_contacts");	

	effacer_meta($nom_meta_base_version);
}

?>
