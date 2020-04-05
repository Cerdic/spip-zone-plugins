<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin rssconfig.
 *
 * @plugin     rssconfig
 *
 * @copyright  2011-2015
 * @author     Joseph
 * @licence    GNU/GPL
 * @package    SPIP\RSSconfig\Installation
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin rssconfig.
 *
 * @param string $nom_meta_base_version
 *                                      Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *                                      Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 **/
function rssconfig_upgrade($nom_meta_base_version, $version_cible)
{
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin rssconfig.
 *
 * @param string $nom_meta_base_version
 *                                      Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 **/
function rssconfig_vider_tables($nom_meta_base_version)
{
	effacer_meta('rssconfig');
	effacer_meta('rssconfig_breves');
	effacer_meta('rssconfig_evenements');
	effacer_meta('rssconfig_sites');
	effacer_meta($nom_meta_base_version);
}
