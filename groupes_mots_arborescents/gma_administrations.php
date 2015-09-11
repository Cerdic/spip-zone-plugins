<?php
/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 * 
 * Adapte les tables groupes mots et mots
**/
function gma_upgrade($nom_meta_base_version, $version_cible) {
	// pour gma_definir_heritages()
	include_spip('gma_fonctions');

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_groupes_mots', 'spip_mots')),
		array('gma_definir_heritages'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * Supprime les adaptations des tables groupes mots et mots
**/
function gma_vider_tables($nom_meta_base_version) {

	sql_alter("TABLE spip_groupes_mots DROP COLUMN id_parent");
	sql_alter("TABLE spip_groupes_mots DROP COLUMN id_groupe_racine");
	sql_alter("TABLE spip_mots DROP COLUMN id_groupe_racine");

	effacer_meta($nom_meta_base_version);
}