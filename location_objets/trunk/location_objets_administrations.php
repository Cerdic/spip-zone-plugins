<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Location d&#039;objets
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Location d&#039;objets.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function location_objets_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_objets_locations', 'spip_objets_locations_liens', 'spip_objets_locations_details', 'spip_objets_locations_details_liens')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_objets_locations')));
	$maj['1.0.2'] = array(array('maj_tables', array('spip_objets_locations_details')));
	$maj['1.0.4'] = array(array('maj_tables', array('spip_objets_locations_details')));
	$maj['1.0.5'] = array(array('maj_tables', array('spip_objets_locations')));
	$maj['1.1.0'] = array(
		array('maj_tables', array('spip_objets_locations_details')),
		array('lo_upgrade', '1.1.0'),
		array('sql_alter','TABLE spip_objets_locations DROP COLUMN `date_debut`'),
		array('sql_alter','TABLE spip_objets_locations DROP COLUMN `date_fin`'),
	);
	$maj['1.1.10'] = array(
		array('sql_alter', 'TABLE spip_objets_locations_details CHANGE prix_unitaire_ht prix_unitaire_ht DECIMAL(20,6) NULL DEFAULT NULL')
	);
	$maj['1.2.0'] = array(
		array('sql_alter', 'TABLE spip_objets_locations_details CHANGE jours duree INT(11) NOT NULL DEFAULT 0'),
		array('maj_tables', array('spip_objets_locations')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Location d&#039;objets.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function location_objets_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_objets_locations');
	sql_drop_table('spip_objets_locations_liens');
	sql_drop_table('spip_objets_locations_details');
	sql_drop_table('spip_objets_locations_details_liens');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('objets_location', 'objets_locations_detail')));
	sql_delete('spip_mots_liens', sql_in('objet', array('objets_location', 'objets_locations_detail')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('objets_location', 'objets_locations_detail')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('objets_location', 'objets_locations_detail')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('objets_location', 'objets_locations_detail')));
	sql_delete('spip_forum', sql_in('objet', array('objets_location', 'objets_locations_detail')));

	effacer_meta($nom_meta_base_version);
}
