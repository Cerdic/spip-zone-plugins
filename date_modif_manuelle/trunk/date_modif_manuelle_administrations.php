<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Date de modification manuelle
 *
 * @plugin     Date de modification manuelle
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_modif_manuelle\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Date de modification manuelle.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function date_modif_manuelle_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('sql_alter', "TABLE spip_articles ADD COLUMN date_modif_manuelle datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER date_modif")
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Date de modification manuelle.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function date_modif_manuelle_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_alter("TABLE spip_articles DROP COLUMN date_modif_manuelle");
	effacer_meta('date_modif_manuelle');
	effacer_meta($nom_meta_base_version);
}
