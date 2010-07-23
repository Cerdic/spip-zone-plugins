<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

	$GLOBALS['canevas_base_version'] = 0.1;
	function canevas_verifier_base(){
		$version_base = $GLOBALS['canevas_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['canevas_base_version'])) ||
			(($current_version = $GLOBALS['meta']['canevas_base_version'])!=$version_base)){
			include_spip('base/canevas');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				maj_tables('spip_rubriques'); 
				ecrire_meta('canevas_base_version',$current_version=$version_base,'non');
			}
		}
	}
	
	function canevas_vider_tables() {
		include_spip('base/canevas');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_canevas");
		//sql_alter("TABLE spip_rubriques DROP COLUMN agenda");
		effacer_meta('canevas_base_version');
	}
	
	function canevas_install($action){
		$version_base = $GLOBALS['canevas_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['canevas_base_version']) AND ($GLOBALS['meta']['canevas_base_version']>=$version_base));
				break;
			case 'install':
				canevas_verifier_base();
				break;
			case 'uninstall':
				canevas_vider_tables();
				break;
		}
	}
?>
