<?php
/*
 * SPIP Propaganda
 * 
 *
 * Auteurs :
 * kent1 - Dani
 *
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */
	
	$GLOBALS['propaganda_base_version'] = 0.01;

	function propaganda_upgrade(){
		$version_base = $GLOBALS['propaganda_base_version'];
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['propaganda_base_version']) )
				&& (($current_version = $GLOBALS['meta']['propaganda_base_version'])==$version_base))
			return;
			
		if ($current_version==0.0){
			include_spip('base/propaganda');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "spip Propaganda installed @ 0.01 <br/>";
			ecrire_meta('propaganda_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
	
	function propaganda_vider_tables() {
		spip_query("DROP TABLE propaganda");
		effacer_meta('propaganda_base_version');
		ecrire_metas();
	}
	
	function propaganda_install($action){
		global $propaganda_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['propaganda_base_version']) AND ($GLOBALS['meta']['propaganda_base_version']>=$propaganda_base_version));
				break;
			case 'install':
				propaganda_upgrade();
				break;
			case 'uninstall':
				propaganda_vider_tables();
				break;
		}
	}	
?>