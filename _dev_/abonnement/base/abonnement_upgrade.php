<?php
/**
* Plugin Abonnement
*
* Copyright (c) 2007
* BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

//version actuelle du plugin à changer en cas de maj
	$GLOBALS['abonnement_base_version'] = 0.3;
	
	function abonnement_upgrade(){
		$version_base = $GLOBALS['abonnement_base_version'];
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['abonnement_base_version']) )
				&& (($current_version = $GLOBALS['meta']['abonnement_base_version'])==$version_base))
			return;

		include_spip('base/abonnement');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "creation des tables spip_abonnements";

			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();
		
		if ($current_version < 0.2){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "Maj 0.2 des tables spip_abonnements";
			
			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();
		
		if ($current_version < 0.3){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			echo "Maj 0.3 des tables spip_auteurs_elargis_articles";
			
			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}
	
	function abonnement_vider_tables() {
		spip_query("DROP TABLE spip_abonnements");
		effacer_meta('abonnement_base_version');
		ecrire_metas();
	}
	
	function abonnement_install($action){
		global $abonnement_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['abonnement_base_version']) AND ($GLOBALS['meta']['abonnement_base_version']>=$abonnement_base_version));
				break;
			case 'install':
				abonnement_upgrade();
				break;
			case 'uninstall':
				abonnement_vider_tables();
				break;
		}
	}
		

?>