<?php
	
	function dw2_verifier_base(){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
	}
	
	function dw2_vider_tables() {
		include_spip('base/abstract_sql');
		sql_drop_table("spip_dw2_doc");
		sql_drop_table("spip_dw2_serv_ftp");
		sql_drop_table("spip_dw2_acces_restreint");
		sql_drop_table("spip_dw2_triche");
		sql_drop_table("spip_dw2_stats");
		sql_drop_table("spip_dw2_config");
		sql_drop_table("spip_dw2_stats_auteurs");
		effacer_meta('agenda_base_version');
	}
	
	function dw2_install($action){
		$version_base = $GLOBALS['agenda_base_version'];
		switch ($action){
			case 'test':
				return true;
				break;
			case 'install':
				dw2_verifier_base();
				break;
			case 'uninstall':
				dw2_vider_tables();
				break;
		}
	}
?>