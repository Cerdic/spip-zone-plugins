<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Profils.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function profils_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_profils', 'spip_auteurs'))
	);
	
	$maj['1.0.1'] = array(
		array('sql_updateq', 'spip_auteurs', array('pass' => ' '), array('id_profil>0', 'pass=""'))
	);
	
	$maj['1.0.2'] = array(
		array('sql_update', 'spip_auteurs', array('login' => 'md5(email)'), array('id_profil>0', 'login=""', 'email!=""'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Profils.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function profils_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_profils');

	# Nettoyer les versionnages
	sql_delete('spip_versions', sql_in('objet', array('profil')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('profil')));

	effacer_meta($nom_meta_base_version);
}
