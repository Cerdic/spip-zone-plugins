<?php
/**
 * Plugin daterubriques
 *
 * @plugin     daterubriques
 * @copyright  2011-2016
 * @author     Touti, Yffic
 * @licence    GPL 3
 * @package    SPIP\daterubriques\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/cextras');
include_spip('inc/config');
include_spip('base/daterubriques');
	
function daterubriques_upgrade($nom_meta_base_version,$version_cible){
	if (!lire_config('daterubriques/secteurs',array())){
		ecrire_config('daterubriques/secteurs',array(0));
	}

	$maj = array();
	$maj['create'] = array(
	);
	$champs = daterubriques_declarer_champs_extras();
	cextras_api_upgrade($champs, $maj['create']);

	$maj['1.0.0'] = $maj['create'];

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function daterubriques_vider_tables($nom_meta_base_version) {
	// C'est le plugin Champs Extras qui supprime le meta nom_meta_base_version
	$champs = daterubriques_declarer_champs_extras();
	cextras_api_vider_tables($champs);
	effacer_meta($nom_meta_base_version);
}

