<?php

	$GLOBALS['ga_base'] = 0.1;
	function ga_verifier_base(){
		$version_base = $GLOBALS['ga_base'];
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('ga_base',$version_base,'non');
	}
	
	function ga_vider_tables() {
		include_spip('base/ga');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_ga");
		effacer_meta('ga_base');
	}
	
	function ga_install($action){
		switch ($action){
			case 'install':
				ga_verifier_base();
				break;
			case 'uninstall':
				ga_vider_tables();
				break;
		}
	}
?>
