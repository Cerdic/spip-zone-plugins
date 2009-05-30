<?php
	/**
	* Plugin Photos
	*
	* Copyright (c) 2009
	* Bernard Blazin 
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	//version actuelle du plugin à changer en cas de maj
	$GLOBALS['photos_version'] = 1.0;	
		
	function photos_verifier_base(){			
		$version_base = $GLOBALS['photos_version'];
		$current_version = 0.0;
		
		if (   (!isset($GLOBALS['meta']['photos_base_version']) )
		|| (($current_version = $GLOBALS['meta']['photos_base_version'])!=$version_base)) {
			
			include_spip('base/photos');
			
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('photos_base_version',$current_version=$version_base);
			}
			
			
					
			ecrire_metas();
		}
	}

	function photos_effacer_tables(){
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_photos");
		effacer_meta('photos_base_version');
		effacer_meta('photos');
		ecrire_metas();
	}	
	
	function photos_install($action){
		$version_base = $GLOBALS['photos_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['photos_base_version']) 
				AND ($GLOBALS['meta']['photos_base_version']>=$version_base));
				break;
			case 'install':
				photos_verifier_base();
				break;
			case 'uninstall':
				photos_effacer_tables();
				break;
		}
	}	
?>
