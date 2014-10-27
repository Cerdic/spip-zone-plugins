<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Archive notifications
 *
 * @plugin     Archive notifications
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Archive notifications.
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
function notifications_archive_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$notifications=charger_fonction('notifications_archiver','inc',true);
	$notifications=$notifications();
	

	$maj['create'] = array(
		array('maj_tables', array('spip_notifications')),
		array('ecrire_config', 'notifications_archive', $notifications)    
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Archive notifications.
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
function notifications_archive_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_notifications");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('notification')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('notification')));
	sql_delete("spip_forum",                 sql_in("objet", array('notification')));

	effacer_meta($nom_meta_base_version);
	effacer_meta('notifications_archive');    
}

?>