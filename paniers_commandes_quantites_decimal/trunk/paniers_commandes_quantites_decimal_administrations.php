<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin paniers
 *
 * @plugin     Paniers
 * @copyright  2013
 * @author     Les Développements Durables, cédric Morin
 * @licence    GPL v3
 * @package    SPIP\Paniers\Installation
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin paniers.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function paniers_commandes_quantites_decimal_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('base/abstract_sql');
	include_spip('inc/config');

	// Première installation
	$maj['create'] = array(
		array('sql_alter', 'TABLE spip_paniers_liens CHANGE quantite quantite decimal(9,3) DEFAULT \'1\' NOT NULL'),
		array('sql_alter', 'TABLE spip_commandes_details CHANGE quantite quantite decimal(9,3) DEFAULT \'0\' NOT NULL'),
	);

	$maj['0.2.0'] = array(
		array('sql_alter', 'TABLE spip_paniers_liens CHANGE quantite quantite decimal(9,3) DEFAULT \'1\' NOT NULL'),
		array('sql_alter', 'TABLE spip_commandes_details CHANGE quantite quantite decimal(9,3) DEFAULT \'0\' NOT NULL'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

