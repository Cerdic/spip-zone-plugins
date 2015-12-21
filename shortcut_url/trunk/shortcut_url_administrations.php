<?php

/**
 * Administrations pour shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\shortcut_url\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables shortcut_url
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function shortcut_url_upgrade($nom_meta_base_version,$version_cible){
	
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_shortcut_urls', 'spip_shortcut_urls_logs'))
	);

	$maj['0.0.3'] = array(
		array('maj_tables', array('spip_shortcut_urls_bots'))
	);

	$maj['0.0.4'] = array(
		array('maj_tables', array('spip_shortcut_urls_bots'))
	);

	$maj['0.0.5'] = array(
		// Changer le type de champs pour le tri par click
		array('sql_alter',"TABLE spip_shortcut_urls MODIFY COLUMN click bigint(11)"));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables shortcut_url
 *
 * @param string $nom_meta_base_version
 */
function shortcut_url_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_shortcut_urls");
	sql_drop_table("spip_shortcut_urls_logs");
	effacer_meta("shortcut_url");
	effacer_meta($nom_meta_base_version);
}

?>