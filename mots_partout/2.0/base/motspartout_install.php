<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by *
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function motspartout_upgrade($tables_possibles){

		include_spip('base/abstract_sql');

		//installation du champ id_parent et du meta concernant l'installation, gestion arborescente
		sql_alter("TABLE `spip_groupes_mots` ADD `id_parent` BIGINT(20) NOT NULL ");
		//peut être préféré creer_base() à sql_create()
		foreach($tables_possibles as $table) {

		    //determine l'id de la table (supprimé le s de la table, ajout du préfixe id_)
		    preg_match('/(.*)s/i',$table,$nom);
		    $id_table = "id_".$nom[1];

    		//permettre l'affectation d'un groupe à un objet spip
    		//sql_alter("TABLE `spip_groupes_mots` ADD $table VARCHAR(3) NOT NULL DEFAULT 'non' ");
    		//sql_replace("spip_groupes_mots",array());
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
		ecrire_meta('MotsPartout:tables_installees',serialize($tables_possibles));
		ecrire_meta('MotsPartout:mots-partout-arbo-installe','oui');
		ecrire_meta('motspartout_version','0.5');
	}

	function motspartout_modifier_tables($tables_possibles) {

		include_spip('base/abstract_sql');

		//desinstallation du champ et du meta
		//sql_alter("TABLE `spip_groupes_mots` DROP `id_parent`");

		foreach($tables_possibles as $clef => $table) {
    		//permettre l'affectation d'un groupe à un objet spip
        //sql_alter("TABLE `spip_groupes_mots` DROP $table");
    		//permettre les relations entre mots et objets
    		//sql_drop_table("spip_mots_$table");
		}

		effacer_meta('MotsPartout:tables_installees');
		effacer_meta('MotsPartout:mots-partout-arbo-installe');
		effacer_meta('motspartout_version');
	}

	function motspartout_install($action){

    //par defaut
    // articles, rubriques, syndic sont traités par spip
    //les mots clefs partouts

    include_spip('base/abstract_sql');

	  //TODO : Gestion de ce tableau (par un formulaire cfg ?)
	  $tables_possibles = array('documents','auteurs','syndic_articles','evenements');

		switch ($action){
			case 'install':
				motspartout_upgrade($tables_possibles);
				break;
			case 'uninstall':
				motspartout_modifier_tables($tables_possibles);
			break;
			case 'test':
			  return (isset($GLOBALS['meta']['motspartout_version']) && $GLOBALS['meta']['motspartout_version']>='0.5.1' );
		  break;
		}
	}

?>