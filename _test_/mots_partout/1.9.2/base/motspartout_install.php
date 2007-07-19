<?php
/*
 * (c) 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
	
	

	function motspartout_upgrade(){
		//installation du champ id_parent et du meta concernant l'installation
		spip_query("ALTER TABLE spip_forms_donnees_champs ADD id_parent bigint(20) "); 
		
		ecrire_meta('MotsPartout:mots-partout-arbo-installe','oui');
		
		
	}
	
	function motspartout_modifier_tables() {
		//desinstallation du champ et du meta
		spip_query("ALTER TABLE spip_groupes_mots DROP id_parent");
		effacer_meta('MotsPartout:mots-partout-arbo-installe');
	}
	
	function motspartout_install($action){
		global $forms_base_version;
		switch ($action){
			case 'install':
				motspartout_upgrade();
				break;
			case 'uninstall':
				motspartout_modifier_tables();
				break;
		}
	}	
?>