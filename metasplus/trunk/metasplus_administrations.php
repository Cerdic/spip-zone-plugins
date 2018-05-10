<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Installation
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Métas+.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function metasplus_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// 1ère installation : passe la config en opt-in
	$maj['create'] = array(
		array('metasplus_maj_create'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Métas+.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function metasplus_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}


/**
 * Fonction privée pour la mise à jour create (1ère installation)
 * On passe la config des protocoles en opt-in
 *
 * @return Void
 */
function metasplus_maj_create(){
	include_spip('inc/config');
	$config = lire_config('metasplus');
	$protocoles = array('dublincore', 'opengraph', 'twitter');
	foreach($protocoles as $protocole) {
		if (empty($config[$protocole])) {
			$config[$protocole] = 'on';
		} else {
			unset($config[$protocole]);
		}
	}
	ecrire_config('metasplus', $config);
}