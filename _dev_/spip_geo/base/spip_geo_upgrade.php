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
			$table_pays = "spip_geo_pays";
			$descpays = sql_showtable($table_pays, '', false);
			if(isset($descpays['field']['pays'])){
				echo 'virage de la table spip_geo_pays';
				sql_drop_table("spip_geo_pays");
			}
			include_spip('base/spip_geo');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('inc/import_origine');
			creer_base();
			echo "SPIP_Geo installed @ 0.01 <br/>";
			import_origine_continents();
			import_origine_pays();
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