<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/
	
	$GLOBALS['abomailmans_base_version'] = 0.32;
	function abomailmans_upgrade(){
		$version_base = $GLOBALS['abomailmans_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['abomailmans_base_version']))
			|| (($current_version = $GLOBALS['meta']['abomailmans_base_version'])!=$version_base)){

			include_spip('base/abomailmans');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('abomailmans_base_version',$current_version=$version_base);
			}
			if ($current_version==0.30){
				sql_alter("TABLE spip_abomailmans ADD `lang` varchar(10) DEFAULT ' ' NOT NULL AFTER `email_sympa`");
				ecrire_meta('abomailmans_base_version',$current_version=0.31,'non');
				echo 'Upgrade de la base abomailmans';
			}
			if ($current_version==0.31){
				sql_alter("TABLE spip_abomailmans ADD `email_unsubscribe` varchar(255) DEFAULT ' ' NOT NULL AFTER `email`");
				sql_alter("TABLE spip_abomailmans ADD `email_subscribe` varchar(255) DEFAULT ' ' NOT NULL AFTER `email`");
				ecrire_meta('abomailmans_base_version',$current_version=0.32,'non');
				echo 'Upgrade de la base abomailmans';
			}
			ecrire_metas();
		}
	}
	
	function abomailmans_vider_tables() {
		spip_query("DROP TABLE spip_abomailmans");
		effacer_meta('abomailmans_base_version');
		ecrire_metas();
	}
	
	function abomailmans_install($action){
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