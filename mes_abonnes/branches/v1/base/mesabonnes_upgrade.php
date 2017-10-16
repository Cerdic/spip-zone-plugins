<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['mesabonnes_base_version'] = 0.1;
    
function mesabonnes_upgrade(){

		$version_base = $GLOBALS['mesabonnes_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['mesabonnes_base_version']) )
				|| (($current_version = $GLOBALS['meta']['mesabonnes_base_version'])!=$version_base)){
			include_spip('base/mesabonnes');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();        
				// annoncer creation base ?
			  ecrire_meta('mesabonnes_base_version',$current_version=$version_base,'non');				
			}
			ecrire_metas();
		}
}
	
function mesabonnes_vider_tables() {
		spip_query("DROP TABLE spip_mesabonnes");
		effacer_meta('mesabonnes_base_version');
		ecrire_metas();
}
	
function mesabonnes_install($action){
		$version_base = $GLOBALS['mesabonnes_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['mesabonnes_base_version']) AND ($GLOBALS['meta']['mesabonnes_base_version']>=$version_base));
				break;
			case 'install':
				mesabonnes_upgrade();
				break;
			case 'uninstall':
				mesabonnes_vider_tables();								
				break;
		}
}
?>