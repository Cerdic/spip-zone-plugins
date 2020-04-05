<?php
function crcn_install($action){
	
	switch ($action){
		case 'install':
			include_spip('base/crcn_base_entrees');
			ecrire_meta('crcn_install','1');
		break;
		
		case 'test':
			if ($GLOBALS['meta']['crcn_install']!='1') {
				return false;
			}
			else return true;
		break;
		case 'uninstall':
			effacer_meta('crcn_install');
		break;
	}
	
}
?>