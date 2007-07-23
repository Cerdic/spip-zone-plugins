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

if (!defined("_ECRIRE_INC_VERSION")) return;

	function association_install($action){
		global $asso_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['asso_base_version']) AND ($GLOBALS['meta']['asso_base_version']>=$asso_base_version));
				break;
			case 'install':
				association_verifier_base();
				break;
			case 'uninstall':
				association_effacer_tables();
				break;
		}
	}
	
	function association_verifier_base(){
		$version_base = 0.50; //version actuelle
		$current_version = 0.0;
		
		if (   (!isset($GLOBALS['meta']['asso_base_version']) )|| (($current_version = $GLOBALS['meta']['asso_base_version'])!=$version_base)){
			
			include_spip('base/association');
			
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				spip_query("INSERT INTO spip_asso_profil (nom) VALUES ('')");
				ecrire_meta('asso_base_version',$current_version=$version_base);
			}
			
			if ($current_version<0.21){
				spip_query("ALTER TABLE spip_asso_adherents ADD publication text NOT NULL AFTER secteur");
				ecrire_meta('asso_base_version',$current_version=0.21);
			}		
			
			if ($current_version<0.30){
				spip_query("CREATE TABLE spip_asso_dons (id_don bigint(21) NOT NULL auto_increment, date_don date NOT NULL default '0000-00-00',   bienfaiteur text NOT NULL, id_adherent int(11) NOT NULL default '0', argent tinytext, colis text, valeur text NOT NULL, contrepartie tinytext, commentaire text, maj timestamp(14) NOT NULL, PRIMARY KEY  (id_don) ) TYPE=MyISAM AUTO_INCREMENT=1");
				spip_query("DROP TABLE spip_asso_bienfaiteurs");
				spip_query("DROP TABLE spip_asso_financiers");			
//				spip_query("RENAME TABLE spip_asso_financiers TO spip_asso_banques");
//				spip_query("ALTER TABLE spip_asso_banques CHANGE id_financier id_banque, ADD date date NOT NULL AFTER solde");
				spip_query("INSERT INTO spip_asso_banques (code) VALUES ('caisse')");
				spip_query("CREATE TABLE spip_asso_livres (id_livre tinyint(4) NOT NULL auto_increment, valeur text NOT NULL, libelle text NOT NULL, maj timestamp(14) NOT NULL, PRIMARY KEY  (id_livre) ) TYPE=MyISAM AUTO_INCREMENT=1");
				spip_query("INSERT INTO spip_asso_livres (valeur, libelle) VALUES ('cotisation', 'Cotisations'), ('vente', 'Ventes'), ('don', 'Dons'), ('achat', 'Achats'), ('divers', 'Divers'), ('activite', 'Activités')");
				spip_query("ALTER TABLE spip_asso_profil ADD dons text NOT NULL AFTER mail, ADD ventes text NOT NULL, ADD comptes text NOT NULL, ADD activites text NOT NULL ");
				spip_query("UPDATE spip_asso_profil SET dons='oui', ventes='oui' ,comptes='oui' WHERE id_profil=1");
				ecrire_meta('asso_base_version',$current_version=0.30);
			}	
			
			if ($current_version<0.40){
				spip_query("ALTER TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL AFTER `id_journal` ");
				ecrire_meta('asso_base_version',$current_version=0.40);
			}
			
			if ($current_version<0.50){
				spip_query("ALTER TABLE spip_asso_profil ADD indexation TEXT NOT NULL AFTER mail ");
				spip_query("ALTER TABLE spip_asso_activites ADD membres TEXT NOT NULL AFTER accompagne, ADD non_membres TEXT NOT NULL AFTER membres ");
				ecrire_meta('asso_base_version',$current_version=0.50);
			}
			
			if ($current_version<0.60){
				spip_query("DROP TABLE spip_asso_profil  ");
				ecrire_meta('asso_base_version',$current_version=0.60);
			}		
			
			ecrire_metas();
		}
	}

	function association_effacer_tables() {
		spip_query("DROP TABLE spip_asso_adherents");
		spip_query("DROP TABLE spip_asso_dons");
		spip_query("DROP TABLE spip_asso_ventes");
		spip_query("DROP TABLE spip_asso_activites");
		spip_query("DROP TABLE spip_asso_comptes");
		spip_query("DROP TABLE spip_asso_categories");
		spip_query("DROP TABLE spip_asso_banques");
		effacer_meta('asso_base_version');
		ecrire_metas();
	}
	
?>
