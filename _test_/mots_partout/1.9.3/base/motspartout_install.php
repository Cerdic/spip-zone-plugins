<?php
/*
 * (c) 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
		
	function motspartout_upgrade($tables_possibles){
	    
		//installation du champ id_parent et du meta concernant l'installation, gestion arborescente
		sql_alter("TABLE `spip_groupes_mots` ADD `id_parent` BIGINT(20) NOT NULL ");
					
		//peut être préféré creer_base() à sql_create()		
		foreach($tables_possibles as $table) {
		
		    //determine l'id de la table (supprimé le s de la table, ajout du préfixe id_)
		    preg_match('/(.*)s/i',$table,$nom);
		    $id_table = "id_".$nom[1];
		
    		//permettre l'affectation d'un groupe à un objet spip
    		sql_alter("TABLE `spip_groupes_mots` ADD $table VARCHAR(3) NOT NULL DEFAULT 'non' ");
    		//permettre les relations entre mots et objets
            sql_create("spip_mots_$table",
                array(
                    "id_mot" => "bigint(20) NOT NULL default '0'",
                    $id_table=> "bigint(20) NOT NULL default '0'"
                ),
                array(
                    'PRIMARY KEY' => $id_table.",id_mot"
                )
            );
		}				
		ecrire_meta('MotsPartout:mots-partout-arbo-installe','oui');		
	}
	
	function motspartout_modifier_tables($tables_possibles) {
		//desinstallation du champ et du meta
		sql_alter("TABLE `spip_groupes_mots` DROP `id_parent`");

		foreach($tables_possibles as $clef => $table) {
    		//permettre l'affectation d'un groupe à un objet spip
    		sql_alter("TABLE `spip_groupes_mots` DROP $table");
    		//permettre les relations entre mots et objets
    		sql_drop_table("spip_mots_$table");
		}						
		effacer_meta('MotsPartout:mots-partout-arbo-installe');
	}
	
	function motspartout_install($action){
		global $forms_base_version;
		
	    //par defaut
	    // articles, rubriques, syndic sont traités par spip
	    //les mots clefs partouts
	    $tables_possibles = array('documents','auteurs','evenements','syndic_articles');		
		
		switch ($action){	        
			case 'install':
				motspartout_upgrade($tables_possibles);
				break;
			case 'uninstall':
				motspartout_modifier_tables($tables_possibles);
				break;
		}
	}	
?>
