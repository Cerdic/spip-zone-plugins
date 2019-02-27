<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Location d’objets - paiements
 *
 * @plugin     Location d’objets - paiements
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets_bank\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Location d’objets - paiements.
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
function location_objets_bank_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('inc/config');

	$version_actuelle = lire_config($nom_meta_base_version, 0);

	// Définir les liaisons souhaités
	if ($version_actuelle == 0) {
		$config_location_objets = lire_config('location_objets', array());

		$config_location_objets = array_merge(
			$config_location_objets, array('statut_defaut' => 'encours')
			);

	}

	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_transactions', 'spip_objets_locations')),
		array('ecrire_config', 'location_objets', $config_location_objets
			),
		);
	$maj['1.0.2'] = array(
		array('maj_tables', array('spip_objets_locations')),
		array(
			'sql_alter',
			'TABLE spip_transactions ADD INDEX `id_objets_location` (`id_objets_location`)'
		)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Location d’objets - paiements.
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
function location_objets_bank_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table('spip_xx');
	# sql_drop_table('spip_xx_liens');


	effacer_meta($nom_meta_base_version);
}
