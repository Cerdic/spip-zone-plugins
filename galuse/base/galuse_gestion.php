<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/
	
	//version actuelle du plugin à changer en cas de maj
	$GLOBALS['galuse_version'] = 1.0;	
		
	function galuse_verifier_base(){			
		$version_base = $GLOBALS['galuse_version'];
		$current_version = 0.0;
		
		if (   (!isset($GLOBALS['meta']['galuse_base_version']) )
		|| (($current_version = $GLOBALS['meta']['galuse_base_version'])!=$version_base)) {
			
			include_spip('base/galuse');
			
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('galuse_base_version',$current_version=$version_base);
			}

			ecrire_metas();
		}
	}

	function galuse_effacer_tables(){
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE ".$GLOBALS['table_prefix']."_galuse");
		effacer_meta('galuse_base_version');
		effacer_meta('galuse');
		ecrire_metas();
	}	
	
	function galuse_install($action){
		$version_base = $GLOBALS['galuse_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['galuse_base_version']) 
				AND ($GLOBALS['meta']['galuse_base_version']>=$version_base));
				break;
			case 'install':
				galuse_verifier_base();
				break;
			case 'uninstall':
				galuse_effacer_tables();
				break;
		}
	}	
?>
