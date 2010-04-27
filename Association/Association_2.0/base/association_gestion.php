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

	//version actuelle du plugin à changer en cas de maj
	$GLOBALS['association_version'] = 0.64;	
		
function association_verifier_base(){			
		$version_base = $GLOBALS['association_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['asso_base_version']) )
		|| (($current_version = $GLOBALS['meta']['asso_base_version'])!=$version_base)) {
			
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
				spip_query("RENAME TABLE spip_asso_banques TO spip_asso_plan");
				spip_query("DROP TABLE spip_asso_livres ");
				ecrire_meta('asso_base_version',$current_version=0.61);
			}	
			
			if ($current_version<0.62){
				spip_query("ALTER TABLE spip_asso_plan ADD actif TEXT NOT NULL AFTER commentaires");
				ecrire_meta('asso_base_version',$current_version=0.62);
			}
			
			if ($current_version<0.63){
				spip_query("ALTER TABLE spip_asso_ventes ADD id_acheteur BINGINT(20) NOT NULL AFTER acheteur");
				ecrire_meta('asso_base_version',$current_version=0.63);
			}
			
			if ($current_version<0.64){

				if(_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') {

				  sql_alter("TABLE spip_auteurs_elargis ADD validite date NOT NULL default '0000-00-00'");
				  sql_alter("TABLE spip_auteurs_elargis ADD montant float NOT NULL default '0'");
				  sql_alter("TABLE spip_auteurs_elargis ADD date date NOT NULL default '0000-00-00' ");
				  ecrire_meta('asso_base_version',$current_version=0.64);
				} else {
					if (_ASSOCIATION_INSCRIPTION2) return false;
					// Simulation provisoire
					// Pas de chgt de numero 
					// tant pis pour les fausses erreurs SQL
					@sql_alter("TABLE spip_asso_adherents ADD commentaire text NOT NULL default ''");
					@sql_alter("TABLE spip_asso_adherents ADD statut_interne text NOT NULL default '' ");
					@sql_alter("TABLE spip_asso_adherents CHANGE COLUMN nom nom_famille text DEFAULT '' NOT NULL");
				}
			}
					
		}
		return true;
	}

function association_effacer_tables(){
		include_spip('base/abstract_sql');
		sql_drop_table("spip_asso_adherents");
		sql_drop_table("spip_asso_activites");
		sql_drop_table("spip_asso_categories");
		sql_drop_table("spip_asso_comptes");
		sql_drop_table("spip_asso_dons");
		sql_drop_table("spip_asso_plan");
		sql_drop_table("spip_asso_prets");
		sql_drop_table("spip_asso_ressources");
		sql_drop_table("spip_asso_ventes");
		effacer_meta('asso_base_version');
		effacer_meta('association');
		spip_log("plugin assoc desinstallee");
	}	
	
function association_install($action){
	$version_base = $GLOBALS['association_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['asso_base_version']) 
				AND ($GLOBALS['meta']['asso_base_version']>=$version_base));
			break;
		case 'install':
			if (!association_verifier_base()) {
				unset($GLOBALS['meta']['asso_base_version']);
				echo debut_cadre_enfonce('',true);
				echo _L('Installer les plugins cfg et Inscription2 avant d\'installer ce plugin!!!'); 
				echo fin_cadre_enfonce(true);
			}
			break;
	case 'uninstall':
			association_effacer_tables();
			break;
	}
}
?>
