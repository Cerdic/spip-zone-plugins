<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin HTML Minifier
 *
 * @plugin     HTML Minifier
 * @copyright  2018
 * @author     ladnet
 * @licence    GNU/GPL
 * @package    SPIP\HTMLMinifier\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin HTML Minifier.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function htmlminifier_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// 1ère installation : passe la config en opt-in
	$maj['create'] = array(
		array('htmlminifier_maj_create'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin HTML Minifier.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function htmlminifier_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}


/**
 * Fonction privée pour la mise à jour create (1ère installation)
 * On passe la config des protocoles en opt-in
 *
 * @return Void
 */
function htmlminifier_maj_create(){
	include_spip('inc/config');
	include_spip('class/HTMLMinifier');
	$config = HTMLMinifier::get_presets('super_safe');
	ecrire_config('htmlminifier', $config);
}