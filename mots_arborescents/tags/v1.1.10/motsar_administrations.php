<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Mots arborescents
 *
 * @plugin     Mots arborescents
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Motsar\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Mots arborescents.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function motsar_upgrade($nom_meta_base_version, $version_cible) {
	// pour motsar_definir_heritages()
	include_spip('motsar_fonctions');

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_groupes_mots', 'spip_mots')),
		array('motsar_definir_heritages'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Mots arborescents.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function motsar_vider_tables($nom_meta_base_version) {

	sql_alter("TABLE spip_groupes_mots DROP COLUMN mots_arborescents");
	sql_alter("TABLE spip_mots DROP COLUMN id_parent");
	sql_alter("TABLE spip_mots DROP COLUMN id_mot_racine");
	sql_alter("TABLE spip_mots DROP COLUMN profondeur");

	effacer_meta($nom_meta_base_version);
}
