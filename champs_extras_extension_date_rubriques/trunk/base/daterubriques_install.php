<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/daterubriques');
	
function daterubriques_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		$config = lire_config('daterubriques');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'secteurs' => array(0),
		), $config);
		ecrire_meta('daterubriques', serialize($config));

		// C'est le plugin Champs Extras qui ecrit le meta
		$champs = daterubriques_declarer_champs_extras();
		installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
	}

}

function daterubriques_vider_tables($nom_meta_base_version) {
	// C'est le plugin Champs Extras qui supprime le meta nom_meta_base_version
	$champs = daterubriques_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
	effacer_meta('daterubriques');
}
?>
