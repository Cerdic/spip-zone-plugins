<?php
/**
 * Plugin Lecteur (mp3)
 * Licence GPL
 * 2007-2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Upgrade des tables
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function player_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();

	$default = array(
		'player_mp3' => 'eraplayer'
	);
	
	$meta = isset($GLOBALS['meta']['player'])?$GLOBALS['meta']['player']:$default;
	if (is_string($meta)){
		$meta = array(
			'player_mp3' => $meta,
		);
	}

	$maj['create'] = array(
		array('ecrire_meta','player',serialize($meta)),
	);

	$maj[$nom_meta_base_version] = array(
		array('ecrire_meta','player',serialize($meta)),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function player_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}


?>