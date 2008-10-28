<?php
/*
 * SPIP_Geo
 * Avoir a disposition dans spip une liste de continent / pays / ville utilisable par les autres plugins facilement...
 *
 * Auteurs :
 * Quentin Drouet
 *
 * (c) 2007-2008 - Distribue sous licence GNU/GPL
 *
 */
	
	$GLOBALS['spip_geo_base_version'] = 0.01;

	function spip_geo_upgrade(){
		$version_base = $GLOBALS['spip_geo_base_version'];
		$current_version = 0.0;
		if ((isset($GLOBALS['meta']['spip_geo_base_version']) )
				&& (($current_version = $GLOBALS['meta']['spip_geo_base_version'])==$version_base))
			return;
			
		if ($current_version==0.0){
			$descpays = sql_showtable('spip_geo_pays', '', false);
			if(isset($descpays['field']['pays'])){
				sql_drop_table("spip_geo_pays");
				echo 'virage de la table spip_geo_pays<br />';
			}
			include_spip('base/spip_geo');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			include_spip('inc/import_origine');
			import_origine_continents();
			import_origine_pays();
			echo "SPIP_Geo installed @ 0.01 <br/>";
			ecrire_meta('spip_geo_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
	
	function spip_geo_vider_tables() {
		sql_drop_table("spip_geo_continent");
		if(!$GLOBALS['meta']['inscription2_version']){
			sql_drop_table("spip_geo_pays");
		}
		sql_drop_table("spip_geo_ville");
		effacer_meta('spip_geo_base_version');
		ecrire_metas();
	}
	
	function spip_geo_install($action){
		global $spip_geo_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spip_geo_base_version']) AND ($GLOBALS['meta']['spip_geo_base_version']>=$spip_geo_base_version));
				break;
			case 'install':
				spip_geo_upgrade();
				break;
			case 'uninstall':
				spip_geo_vider_tables();
				break;
		}
	}	
?>