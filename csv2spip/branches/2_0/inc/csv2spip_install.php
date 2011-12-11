<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function csv2spip_install($action, $prefix, $version){

	switch ($action){
	case 'install':
			ecrire_meta('csv2spip_version',$version);
			break;
	case 'uninstall':
			effacer_meta('csv2spip_version');
			break;
	case 'test': return isset($GLOBALS['meta']['csv2spip_version']);
	}
}

?>
