<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	function association_verifier_base(){
		$version_base = 0.50; //version actuelle
		$current_version = 0.0;
		
		if (   (!isset($GLOBALS['meta']['asso_base_version']) )
				|| (($current_version = $GLOBALS['meta']['asso_base_version'])!=$version_base)){
				
			include_spip('base/association');
			
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('asso_base_version',$current_version=$version_base);
			}
			
			if ($current_version<0.21){
				spip_query("ALTER TABLE spip_asso_adherents ADD publication text NOT NULL AFTER secteur");
				ecrire_meta('asso_base_version',$current_version=0.21);
			}		
			
			if ($current_version<0.30){
				spip_query("DROP TABLE spip_asso_bienfaiteurs");
				spip_query("DROP TABLE spip_asso_financiers");			
				ecrire_meta('asso_base_version',$current_version=0.30);
			}	
			
			if ($current_version<0.40){
				spip_query("ALTER TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL AFTER `id_journal` ");
				ecrire_meta('asso_base_version',$current_version=0.40);
			}
			
			if ($current_version<0.50){
				spip_query("ALTER TABLE spip_asso_activites ADD membres TEXT NOT NULL AFTER accompagne, ADD non_membres TEXT NOT NULL AFTER membres ");
				ecrire_meta('asso_base_version',$current_version=0.50);
			}
			
			if ($current_version<0.60){
				spip_query("DROP TABLE spip_asso_profil  ");
				ecrire_meta('asso_base_version',$current_version=0.60);
			}		
			
			if ($current_version<0.61){
				spip_query("DROP TABLE spip_asso_banques ");
				spip_query("DROP TABLE spip_asso_livres ");
				ecrire_meta('asso_base_version',$current_version=0.61);
			}	
			
			ecrire_metas();
		}
	}

	function asso_install(){
		association_verifier_base();
	}

	//function asso_uninstall(){
	//	include_spip('base/association');
	//	include_spip('base/abstract_sql');
	//}
?>
