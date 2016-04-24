<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Dictionnaire français
 *
 * @plugin     Dictionnaire français
 * @copyright  2016
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\vocabulaire\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Dictionnaire français.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function vocabulaire_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_vocabulaires'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Dictionnaire français.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function vocabulaire_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_dict_fr');

	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('dict_fr')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('dict_fr')));
	sql_delete('spip_forum', sql_in('objet', array('dict_fr')));

	effacer_meta($nom_meta_base_version);
}

function installer_vocabulaire($fichier = 'vocabulaires/fr.txt') {
    include_spip('inc/headers');
    $url = generer_action_auteur('installer_vocabulaire', $fichier.'|0', '', true);
    redirige_par_entete($url);
}
