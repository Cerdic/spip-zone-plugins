<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/
	
	$GLOBALS['abomailmans_base_version'] = 0.10;
	function abomailmans_upgrade(){
		$version_base = $GLOBALS['abomailmans_base_version'];
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['abomailmans_base_version']) )
				&& (($current_version = $GLOBALS['meta']['abomailmans_base_version'])==$version_base))
			return;

		include_spip('base/abomailmans');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('abomailmans_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
	
	function abomailmans_vider_tables() {
		spip_query("DROP TABLE spip_abomailmans");

		effacer_meta('abomailmans_base_version');
		ecrire_metas();
	}
	
	function abomailmans_install($action){
		global $forms_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['abomailmans_base_version']) AND ($GLOBALS['meta']['abomailmans_base_version']==$GLOBALS['abomailmans_base_version']));
				break;
			case 'install':
			
				abomailmans_upgrade();
				break;
			case 'uninstall':
				abomailmans_vider_tables();
				break;
		}
	}	
?>