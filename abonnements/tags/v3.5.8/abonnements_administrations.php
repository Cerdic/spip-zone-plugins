<?php

/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction d'installation du plugin et de mise à jour.
 * */
function abonnements_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_abonnements_offres', 'spip_abonnements_offres_liens', 'spip_abonnements', 'spip_abonnements_offres_notifications')));

	// Ajout de la config des notifications
	$maj['2.1.0'] = array(
		array('maj_tables', array('spip_abonnements_offres_notifications'))
	);

	// Ajout de la date d'échéance possiblement différente avec la date de fin
	$maj['2.2.0'] = array(
		array('maj_tables', array('spip_abonnements')),
		array('sql_update', 'spip_abonnements', array('date_echeance' => 'date_fin'))
	);

	// Ajout des champs taxe et prix_ht, on copie la valeur de prix dans prix_ht
	$maj['2.2.2'] = array(
		array('maj_tables', array('spip_abonnements_offres')),
		array('sql_alter', 'TABLE spip_abonnements_offres ADD prix_ht float(10,2) not null default 0 AFTER periode'),
		array('sql_alter', 'TABLE spip_abonnements_offres ADD taxe decimal(4,4) null default 0 AFTER prix_ht'),
		array('sql_update', 'spip_abonnements_offres', array('prix_ht' => 'prix')),
		array('sql_update', 'spip_abonnements_offres', array('prix' => '0'))
	);
	
	// Nettoyage (d'une table inexistante...)
	$maj['2.2.3'] = array(
		array('sql_alter',"TABLE spip_contacts_abonnements DROP prix"),
	);
	
	// relancer des abonnements après échéance
	$maj['2.2.4'] = array(
		array('sql_alter',"TABLE spip_abonnements_offres_notifications ADD `quand` ENUM('avant','apres') DEFAULT 'avant' NOT NULL AFTER `periode`"),
	);

	// Nettoyage : le champ `prix` est inutile, il suffit de `prix_ht` et `taxe`
	$maj['2.2.5'] = array(
		array('sql_alter',"TABLE spip_abonnements_offres DROP prix"),
	);

	// Ajout d'une valeur possible au champ `quand` 
	$maj['2.2.6'] = array(
		array('sql_alter',"TABLE spip_abonnements_offres_notifications CHANGE `quand` `quand` ENUM('avant','apres','pendant') DEFAULT 'avant' NOT NULL")
	);
	
	// Ajout d'un champ immatériel pour savoir si c'est un service virtuel ou matériel
	$maj['2.3.0'] = array(
		array('maj_tables', array('spip_abonnements_offres')),
	);
	
	// Passage en décimal
	$maj['2.3.1'] = array(
		array('sql_alter', 'TABLE spip_abonnements_offres CHANGE prix_ht prix_ht DECIMAL(20,6) NOT NULL DEFAULT 0'), 
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin.
 * */
function abonnements_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_abonnements_offres");
	sql_drop_table("spip_abonnements_offres_liens");
	sql_drop_table("spip_abonnements");
	sql_drop_table("spip_abonnements_offres_notifications");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('abonnements_offre', 'abonnement')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('abonnements_offre', 'abonnement')));
	sql_delete("spip_forum", sql_in("objet", array('abonnements_offre', 'abonnement')));

	effacer_meta($nom_meta_base_version);
}
