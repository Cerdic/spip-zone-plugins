<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * (c) 2006 - Distribue sous licence GNU/GPL
 *
 */
	
	$GLOBALS['charts_base_version'] = 0.10;
	function charts_upgrade(){
		$version_base = $GLOBALS['charts_base_version'];
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['charts_base_version']) )
				&& (($current_version = $GLOBALS['meta']['charts_base_version'])==$version_base))
			return;

		include_spip('base/charts');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('charts_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
	
	function charts_vider_tables() {
		spip_query("DROP TABLE spip_charts");
		spip_query("DROP TABLE spip_charts_articles");
		effacer_meta('charts_base_version');
		ecrire_metas();
	}
	
	function charts_install($action){
		global $forms_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['charts_base_version']) AND ($GLOBALS['meta']['charts_base_version']==$GLOBALS['charts_base_version']));
				break;
			case 'install':
				charts_upgrade();
				break;
			case 'uninstall':
				charts_vider_tables();
				break;
		}
	}	
?>