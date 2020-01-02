<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le plugin Archivage de contenu ne crée aucune nouvelle table mais ajoute des champs à tous les objets déclarés.
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function archobjet_upgrade($nom_meta_base_version, $version_cible) {

	// Initialisation des modifications des tables d'objet
	$maj['create'] = array();

	// Récupérer les tables d'objet et rajouter les champs :
	// -- est_archive : 0 pour non archivé, 1 pour archivé
	// -- date_archive : la date d'archive ou la date de fin d'archive suivant l'état précédent
	// -- raison_archive : identifiant unique pour une raison (les raisons sont propres aux pllugins utilisateur)
	include_spip('base/objets');
	$tables_objets = array_keys(lister_tables_objets_sql());
	foreach ($tables_objets as $_table) {
		$maj['create'][] = array('sql_alter', "TABLE ${_table} ADD est_archive tinyint(1) DEFAULT 0 NOT NULL");
		$maj['create'][] = array('sql_alter', "TABLE ${_table} ADD date_archive datetime DEFAULT NULL");
		$maj['create'][] = array('sql_alter', "TABLE ${_table} ADD raison_archive varchar(32) DEFAULT '' NOT NULL");
	}

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Suppression de l'ensemble du schéma de données propre au plugin, c'est-à-dire
 * les tables et les variables de configuration.
 *
 * @param string $nom_meta_base_version
 */
function archobjet_vider_tables($nom_meta_base_version) {

	// Récupérer les tables d'objet et supprimer les champs ajoutés :
	include_spip('base/objets');
	$tables_objets = array_keys(lister_tables_objets_sql());
	foreach ($tables_objets as $table) {
		sql_alter("TABLE ${table} DROP est_archive");
		sql_alter("TABLE ${table} DROP date_archive");
		sql_alter("TABLE ${table} DROP raison_archive");
	}

	effacer_meta('archobjet');
	effacer_meta($nom_meta_base_version);
}
