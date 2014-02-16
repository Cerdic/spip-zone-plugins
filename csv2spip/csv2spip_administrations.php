<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin
 */
function csv2spip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('ecrire_config','csv2spip_separateur','§'));

	// Déclaration de la valeur par défaut du séparateur de champs
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}
?>
