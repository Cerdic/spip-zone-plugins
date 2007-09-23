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
	$GLOBALS['abonnement_base_version'] = 0.6;
	
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
		
		if ($current_version < 0.4){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// faudrait virer le autoincrement aussi
			spip_query("ALTER TABLE `spip_auteurs_elargis_articles` ADD INDEX id_auteur_elargi (id_auteur_elargi)");
			spip_query("ALTER TABLE `spip_auteurs_elargis_articles` DROP PRIMARY KEY");
			echo "Maj 0.4 des index `spip_auteurs_elargis_articles`";
			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();
		
		if ($current_version < 0.5){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			spip_query("ALTER TABLE `spip_abonnements` ADD periode text NOT NULL default '';");
			echo "Maj 0.5 de `spip_abonnements` (periode)";
			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();
		
		if ($current_version < 0.6){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			spip_query("ALTER TABLE `spip_auteurs_elargis_abonnements` ADD validite datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
			spip_query("ALTER TABLE `spip_auteurs_elargis_abonnements` ADD montant int(10) unsigned NOT NULL");
			echo "Maj 0.6 de `spip_auteurs_elargis_abonnements` (validite, montant)";
			ecrire_meta('abonnement_base_version',$current_version=$version_base);
		}
		ecrire_metas();


	}
	
	function abonnement_vider_tables() {
		spip_query("DROP TABLE spip_abonnements");
		spip_query("DROP TABLE spip_auteurs_elargis_abonnements");
		spip_query("DROP TABLE spip_auteurs_elargis_articles");
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