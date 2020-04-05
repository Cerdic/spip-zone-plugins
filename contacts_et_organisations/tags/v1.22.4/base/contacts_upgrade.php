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
		spip_log('Tables C&O correctement creees','contacts');
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
		spip_log('Tables correctement passsees en version 1.2.1','contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.2.1");
	}
	if (version_compare($current_version,"1.3.0","<")){
		// les cles primaires des tables contacts et organisations
		// passent sur le id_contact et id_organisation au lieu du id_auteur
		// afin de gerer eventuellement des contacts/organisations autonomes.
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
		
		spip_log('Tables correctement passsees en version 1.3.0','contacts');
		ecrire_meta($nom_meta_base_version, $current_version="1.3.0");
	}

	if (version_compare($current_version,"1.3.1","<")){
		if (!sql_alter("TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) {
			spip_log('Probleme lors de la modif de la table spip_contacts','contacts');
		} else {
			spip_log('Table spip_contacts correctement passsee en version 1.3.1','contacts');
		}
		if (!sql_alter("TABLE spip_organisations CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) {
			spip_log('Probleme lors de la modif de la table spip_organisations','contacts');
		} else {
			spip_log('Table spip_organisations correctement passsee en version 1.3.1','contacts');
		}

		ecrire_meta($nom_meta_base_version, $current_version="1.3.1");
	}

/*
	// on utilise la table spip_contacts_liens
	// pour stocker le id_auteur de spip_contacts
    if (version_compare($current_version,"1.3.2","<")){
		maj_tables('spip_contacts_liens');
		$auteurs = sql_allfetsel(array('id_auteur', 'id_contacts'), 'spip_contacts', 'id_auteur > 0');
		if ($auteurs) {
			$inserts = array();
			foreach ($auteurs as $r) {
				// possibilité d'erreur sql si la ligne est déjà là.
				// rien de dramatique
				$inserts = array(
					'id_contact' => $r['id_contact'],
					'id_objet' => $r['id_auteur'],
					'objet' => 'auteur',
				);
			}
			if ($inserts) {
				sql_insertq_multi('spip_contacts_liens', $inserts);
			}
		}
		sql_alter('TABLE spip_contacts DROP INDEX id_auteur');
		sql_alter('TABLE spip_contacts DROP COLUMN id_auteur');
		ecrire_meta($nom_meta_base_version, $current_version="1.3.2");
	}
*/	

	// le champ descriptif ne changeait pas sur les nouvelles installations (c'etait encore declare tinytext
    if (version_compare($current_version,"1.3.3","<")){
		sql_alter("TABLE spip_contacts CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL");
		ecrire_meta($nom_meta_base_version, $current_version="1.3.3");
	}
	
	// le champ id_auteur sur spip_organisations peut ne pas etre unique si une organisation
	// n'est pas liee a un auteur (id_auteur=0). Plus tard, il faudra certainement mettre une
	// table de relation spip_organisations_liens pour cela.
    if (version_compare($current_version,"1.3.4","<")){
		// pas de UNIQUE sur l'index auteur
		sql_alter("TABLE spip_organisations DROP INDEX id_auteur");
		sql_alter("TABLE spip_organisations CHANGE id_auteur id_auteur bigint(21) DEFAULT 0 NOT NULL");
		sql_alter("TABLE spip_organisations ADD INDEX (id_auteur)");
		ecrire_meta($nom_meta_base_version, $current_version="1.3.4");
	}
	
	// le champ id_parent sur spip_organisations pour definir des hierarchies d'organisations.
    if (version_compare($current_version,"1.3.5","<")){
		sql_alter("TABLE spip_organisations ADD COLUMN id_parent bigint(21) DEFAULT 0 NOT NULL");
		ecrire_meta($nom_meta_base_version, $current_version="1.3.5");
	}
	
	// le champ type_liaison sur spip_organisations_contacts pour definir des types de liaisons donc.
    if (version_compare($current_version,"1.3.6","<")){
		sql_alter("TABLE spip_organisations_contacts ADD COLUMN type_liaison tinytext NOT NULL DEFAULT ''");
		ecrire_meta($nom_meta_base_version, $current_version="1.3.6");
	}

	// on cree la table spip_organisations_liens
    if (version_compare($current_version,"1.3.7","<")){
		maj_tables('spip_organisations_liens');
		ecrire_meta($nom_meta_base_version, $current_version="1.3.7");
	}

/*	
	// on utilise la table spip_organisations_liens
	// pour stocker le id_auteur de spip_organisations
    if (version_compare($current_version,"1.4.0","<")){
		$auteurs = sql_allfetsel(array('id_auteur', 'id_organisation'), 'spip_organisations', 'id_auteur > 0');
		if ($auteurs) {
			$inserts = array();
			foreach ($auteurs as $r) {
				// possibilité d'erreur sql si la ligne est déjà là.
				// rien de dramatique
				$inserts = array(
					'id_organisation' => $r['id_organisation'],
					'id_objet' => $r['id_auteur'],
					'objet' => 'auteur',
				);
			}
			if ($inserts) {
				sql_insertq_multi('spip_organisations_liens', $inserts);
			}
		}
		sql_alter('TABLE spip_organisations DROP INDEX id_auteur');
		sql_alter('TABLE spip_organisations DROP COLUMN id_auteur');
		ecrire_meta($nom_meta_base_version, $current_version="1.4.0");
	}
*/

	// coquille sur la cle de spip_organisations_liens
	if (version_compare($current_version,"1.4.1","<")){
		sql_alter('TABLE spip_organisations DROP INDEX id_contact');
		sql_alter('TABLE spip_organisations ADD INDEX (id_organisation)');

		ecrire_meta($nom_meta_base_version, $current_version="1.4.1");
	}

	// rajout d'un type_liaison dans les liens
	if (version_compare($current_version,"1.4.2","<")){
		maj_tables(array('spip_contacts_liens', 'spip_organisations_liens'));
        sql_alter('TABLE `spip_organisations_liens` DROP PRIMARY KEY');
        sql_alter('TABLE `spip_organisations_liens` ADD PRIMARY KEY ( `id_organisation` , `id_objet` , `objet`, `type_liaison`(25)) ');
        sql_alter('TABLE `spip_contacts_liens` DROP PRIMARY KEY');
        sql_alter('TABLE `spip_contacts_liens` ADD PRIMARY KEY ( `id_contact` , `id_objet` , `objet`, `type_liaison`(25)) ');

		ecrire_meta($nom_meta_base_version, $current_version="1.4.2");
	}

	/*
	Il s'agissait de supprimer spip_organisations_contacts
	pour le mettre dans spip_organisations_liens...
	ce qui s'est avere tres bugge un spip 2.1...
	la version 1.6.0 fait l'inverse de 1.5.0 du coup, pour remettre dans l'ordre

	if (version_compare($current_version,"1.5.0","<")){
		$contacts = sql_allfetsel(array('id_contact', 'id_organisation','type_liaison'), 'spip_organisations_contacts', 'id_contact > 0');
		if ($contacts) {
			$inserts = array();
			foreach ($contacts as $r) {
				// possibilité d'erreur sql si la ligne est déjà là.
				// rien de dramatique
				$inserts[] = array(
					'id_organisation' => $r['id_organisation'],
					'id_objet' => $r['id_contact'],
					'objet' => 'contact',
                    'type_liaison' => $r['type_liaison'],
				);
			}
			if ($inserts) {
				sql_insertq_multi('spip_organisations_liens', $inserts);
			}
		}
		sql_drop_table('spip_organisations_contacts');

		ecrire_meta($nom_meta_base_version, $current_version="1.5.0");
	}
	*/

	if (version_compare($current_version,"1.6.0","<")) {
		include_spip('base/create');
		// remettre spip_organisations_contacts si besoin
		creer_base();
		// repeupler
		$contacts = sql_allfetsel(
			array('id_objet AS id_contact', 'id_organisation', 'type_liaison'),
			'spip_organisations_liens',
			array('objet='.sql_quote('contact'), 'id_objet > 0'));
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

		ecrire_meta($nom_meta_base_version, $current_version="1.6.0");
	}


	if (version_compare($current_version,"1.7.1","<")) {
		contacts_migrer_liens_auteurs();
		ecrire_meta($nom_meta_base_version, $current_version="1.7.1");
	}

	if (version_compare($current_version,"1.7.2","<")) {
		sql_alter('TABLE spip_contacts_liens ADD INDEX (id_objet)');
		sql_alter('TABLE spip_contacts_liens ADD INDEX (objet)');
		sql_alter('TABLE spip_organisations_liens ADD INDEX (id_objet)');
		sql_alter('TABLE spip_organisations_liens ADD INDEX (objet)');
		ecrire_meta($nom_meta_base_version, $current_version="1.7.2");
	}

}


function contacts_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_organisations");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_contacts_liens");
	sql_drop_table("spip_organisations_contacts");	
	
	effacer_meta($nom_meta_base_version);
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
