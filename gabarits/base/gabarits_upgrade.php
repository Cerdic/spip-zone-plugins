<?php
/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

	$GLOBALS['gabarits_base_version'] = 0.1;
	function gabarits_verifier_base(){
		$version_base = $GLOBALS['gabarits_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['gabarits_base_version'])) ||
			(($current_version = $GLOBALS['meta']['gabarits_base_version'])!=$version_base)){
			include_spip('base/gabarits');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				maj_tables('spip_rubriques'); 
				ecrire_meta('gabarits_base_version',$current_version=$version_base,'non');
			}
		}
	}
	
	function gabarits_vider_tables() {
		include_spip('base/gabarits');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_gabarits");
		effacer_meta('gabarits_base_version');
	}
	
	function gabarits_install($action){
		$version_base = $GLOBALS['gabarits_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['gabarits_base_version']) AND ($GLOBALS['meta']['gabarits_base_version']>=$version_base));
				break;
			case 'install':
				gabarits_verifier_base();
				break;
			case 'uninstall':
				gabarits_vider_tables();
				break;
		}
	}
?>
