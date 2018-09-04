<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function grigri_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_auteurs', 'spip_articles', 'spip_rubriques', 'spip_documents', 'spip_mots', 'spip_groupes_mots')),
	);

	$maj['1.0.2'] = array(
		array('maj_tables', array('spip_auteurs', 'spip_articles', 'spip_rubriques', 'spip_documents', 'spip_mots', 'spip_groupes_mots')),
	);
	
/*	$maj['1.0.3'] = array(
		array(ecrire_config('grigri/grigri_public','oui'),
			ecrire_config('grigri/grigri_prive','oui'),
		),
	);
*/	

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function grigri_vider_tables($nom_meta_base_version) {

	effacer_meta($nom_meta_base_version);
}
