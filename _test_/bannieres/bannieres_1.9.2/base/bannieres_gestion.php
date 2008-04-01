<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008
	* François de Montlivault 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	//version actuelle du plugin à changer en cas de maj
	$GLOBALS['bannieres_version'] = 0.1;	
		
	function bannieres_verifier_base(){			
		$version_base = $GLOBALS['bannieres_version'];
		$current_version = 0.0;
		
		if (   (!isset($GLOBALS['meta']['ban_version']) )
		|| (($current_version = $GLOBALS['meta']['ban_base_version'])!=$version_base)) {
			
			include_spip('base/bannieres');
			
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('ban_version',$current_version=$version_base);
			}
			ecrire_metas();
		}
	}

	function bannieres_effacer_tables(){
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_bannieres");
		effacer_meta('ban_version');
		effacer_meta('bannieres');
		ecrire_metas();
	}	
	
	function bannieres_install($action){
		$version_base = $GLOBALS['bannieres_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['ban_version']) 
				AND ($GLOBALS['meta']['ban_version']>=$version_base));
				break;
			case 'install':
				bannieres_verifier_base();
				break;
			case 'uninstall':
				bannieres_effacer_tables();
				break;
		}
	}	
?>
