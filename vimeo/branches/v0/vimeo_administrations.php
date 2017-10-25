<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Vimeos
 *
 * @plugin     Vimeos
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Vimeo\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Vimeos.
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
function vimeo_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_vimeos')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Vimeos.
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
function vimeo_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_vimeos');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('vimeo')));
	sql_delete('spip_mots_liens', sql_in('objet', array('vimeo')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('vimeo')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('vimeo')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('vimeo')));
	sql_delete('spip_forum', sql_in('objet', array('vimeo')));

	effacer_meta($nom_meta_base_version);
}
