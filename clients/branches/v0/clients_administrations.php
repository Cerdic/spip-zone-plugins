<?php
/**
 * Plugin Clients pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

function clients_upgrade($nom_meta_base_version, $version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('clients');
		if (!is_array($config))
			$config = array();

		$config = array_merge(array(
				'elm' => array('complement', 'pays', 'obli_pays'),
				'type_civ' => '',
				'elm_civ' => ''
		), $config);
		ecrire_meta('clients', serialize($config));
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

function clients_vider_tables($nom_meta_base_version) {
	effacer_meta('clients');
	effacer_meta($nom_meta_base_version);
}

?>