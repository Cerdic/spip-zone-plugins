<?php

/**
 * Pipeline pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014-2016
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @package    SPIP\Xiti\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables xiti
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function xiti_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['1.5.0'] = array(
		// On ajoute les nouvelles table
		// spip_xiti_niveaux
		// spip_xiti_niveaux_liens
		array('maj_tables', array('spip_xiti_niveaux', 'spip_xiti_niveaux_liens'))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables xiti
 *
 * Supprime la configuration de Xiti et les deux tables
 * gérant les niveaux deux
 *
 * @param string $nom_meta_base_version
 */
function xiti_vider_tables($nom_meta_base_version) {
	effacer_meta('xiti');
	sql_drop_table('spip_xiti_niveaux');
	sql_drop_table('spip_xiti_niveaux_liens');
	sql_delete('spip_versions', sql_in('objet', array('xiti_niveau')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('xiti_niveau')));
	effacer_meta($nom_meta_base_version);
}
