<?php

/**
 * Administrations pour Petit Cochon
 *
 * @plugin     Petit Cochon
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\petitcochon\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables petitcochon
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function petitcochon_upgrade($nom_meta_base_version, $version_cible) {
	
	$maj = array();

	$maj['create'] = array(
		// Ajout de champs dans spip_petitcochon
		array('maj_tables', array('spip_petitcochon'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables petitcochon
 *
 * @param string $nom_meta_base_version
 */
function petitcochon_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_petitcochon');
	effacer_meta('petitcochon');
	effacer_meta($nom_meta_base_version);
}
