<?php
/**
 * Plugin Projets
 *
 * @plugin  Projets
 * @license GPL (c) 2009-2017
 * @author  Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Projets\Adminstrations
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 **/
function projets_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_projets', 'spip_projets_liens', 'spip_projets_cadres')));

	// on ne gère plus les categories de projets (voir avec les groupes de mots si les gens en veulent).
	$maj['1.1.0'] = array(array('sql_drop_table', 'spip_projets_categories'), array('sql_alter', 'TABLE spip_projets DROP id_projets_categorie'),);
	$maj['1.1.1'] = array(array('maj_tables', array('spip_projets')),);
	$maj['1.1.2'] = array(array('sql_alter', "TABLE spip_projets CHANGE nom nom text NOT NULL DEFAULT ''"),);

	$maj['1.1.3'] = array(array('sql_alter', "TABLE spip_projets CHANGE id_parent id_projet_parent bigint(21) NOT NULL DEFAULT 0"), array('sql_alter', "TABLE spip_projets CHANGE id_projets_cadre id_projets_cadre bigint(21) NOT NULL DEFAULT 0"),);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 **/
function projets_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_projets");
	sql_drop_table("spip_projets_liens");
	sql_drop_table("spip_projets_cadres");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('projet', 'projets_cadre')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('projet', 'projets_cadre')));
	sql_delete("spip_forum", sql_in("objet", array('projet', 'projets_cadre')));

	effacer_meta($nom_meta_base_version);
}

