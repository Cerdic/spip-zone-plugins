<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin Unsplash.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Unsplash.
 *
 * @param string $nom_meta_base_version Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible         Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 **/
function unsplash_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_unsplash')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Unsplash.
 *
 * @param string $nom_meta_base_version Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 **/
function unsplash_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_unsplash');

	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('unsplash')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('unsplash')));
	sql_delete('spip_forum', sql_in('objet', array('unsplash')));

	effacer_meta($nom_meta_base_version);
}
