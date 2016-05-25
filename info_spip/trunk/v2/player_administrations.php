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
		'player_mp3' => 'mejs'
	);
	
	$meta = (isset($GLOBALS['meta']['player'])?$GLOBALS['meta']['player']:$default);
	if (is_string($meta)){
		if ($m = unserialize($meta))
			$meta = $m;
		else {
			$meta = array(
				'player_mp3' => $meta,
			);
		}
	}
	if (!isset($meta['insertion_auto']))
		$meta['insertion_auto'] = array('inline_mini');

	$maj['create'] = array(
		array('ecrire_meta','player',serialize($meta)),
	);

	$maj[$version_cible] = array(
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