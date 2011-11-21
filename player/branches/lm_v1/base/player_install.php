<?php 

	// base/player_install.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

// CP-20080321

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/plugin_globales_lib");

function player_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = intval(isset($GLOBALS['meta'][_PLAYER_META_PREFERENCES]));
			return($result);
			break;
		case 'install':
			return(player_init());
			break;
		case 'uninstall':
			// est appelle lorsque "Effacer tout" dans exec=admin_plugin
			return(player_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function player_init () {
	spip_log("PLAYER: INSTALL");
	$player_init = array(
		'date_install' => date('Y-m-d_H:i:s')
	);
	ecrire_meta(_PLAYER_META_PREFERENCES, serialize($player_init));
	if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

function player_vider_tables () {
	spip_log("PLAYER: UNINSTALL");
	effacer_meta(_PLAYER_META_PREFERENCES);
	if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

?>