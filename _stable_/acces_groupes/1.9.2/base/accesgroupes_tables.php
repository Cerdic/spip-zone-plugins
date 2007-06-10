<?php
// définition des tables utilisées par accesgroupes

    global $tables_principales;
    global $tables_auxiliaires;
		
    $spip_accesgroupes_groupes = array(
          "id_grpacces" => "bigint(20) NOT NULL auto_increment",
          "nom" => "varchar(30) NOT NULL default ''",
          "description" => "varchar(250) default NULL",
          "actif" => "smallint(1) NOT NULL default '0'",
          "proprio" => "bigint(21) NOT NULL default '0'",
					"demande_acces" => "tinyint(4) NOT NULL default '0'"
    );
		$spip_accesgroupes_groupes_key = array(
          "PRIMARY KEY" => "id_grpacces",
          "UNIQUE KEY nom" => "nom"
		);
    $tables_principales['spip_accesgroupes_groupes'] = array(
    	'field' => &$spip_accesgroupes_groupes,
    	'key' => &$spip_accesgroupes_groupes_key
		 );
		
		$spip_accesgroupes_auteurs = array(
          "id_grpacces" => "bigint(21) NOT NULL default '0'",
          "id_auteur" => "bigint(21) NOT NULL default '0'",
          "id_ss_groupe" => "bigint(21) NOT NULL default '0'",
          "sp_statut" => "varchar(255) NOT NULL default ''",
          "dde_acces" => "bigint(21) NOT NULL default '0'",
          "proprio" => "bigint(21) NOT NULL default '0'"
		);
    $spip_accesgroupes_auteurs_key = array(
		      "UNIQUE KEY id_grp" => "id_grpacces,id_auteur,id_ss_groupe,sp_statut"
    );
    $tables_auxiliaires['spip_accesgroupes_auteurs'] = array(
    	'field' => &$spip_accesgroupes_auteurs,
    	'key' => &$spip_accesgroupes_auteurs_key
    );
		$spip_accesgroupes_acces = array(
          "id_grpacces" => "bigint(21) NOT NULL default '0'",
          "id_rubrique" => "bigint(21) NOT NULL default '0'",
          "id_article" => "bigint(21) default NULL",
          "dtdb" => "date default NULL",
          "dtfn" => "date default NULL",
          "proprio" => "bigint(21) NOT NULL default '0'",
					"prive_public" => "SMALLINT(6) NOT NULL default '0'"
		);
		$spip_accesgroupes_acces_key = array(
          "KEY id_grpacces" => "id_grpacces",
          "KEY id_rubrique" => "id_rubrique",
          "KEY id_article" => "id_article"
		);
    $tables_auxiliaires['spip_accesgroupes_acces'] = array(
    	'field' => &$spip_accesgroupes_acces,
    	'key' => &$spip_accesgroupes_acces_key
    );
		
// relations entre les tables
		global $tables_jointures;
		$tables_jointures['spip_auteurs'][] = 'accesgroupes_auteurs';
		$tables_jointures['spip_accesgroupes_groupes'][] = 'accesgroupes_auteurs';
		
		$tables_jointures['spip_rubriques'][] = 'accesgroupes_acces';
		$tables_jointures['spip_accesgroupes_groupes'][] = 'accesgroupes_acces';
		
// table des tables
	  global $table_des_tables;
		$table_des_tables['accesgroupes_groupes'] = 'accesgroupes_groupes';
		$table_des_tables['accesgroupes_acces'] = 'accesgroupes_acces';
		$table_des_tables['accesgroupes_auteurs'] = 'accesgroupes_auteurs';
		
		
		
?>