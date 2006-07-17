<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;



	$table_des_tables['sondages'] = 'sondages';
	$table_des_tables['choix'] = 'choix';
	$table_des_tables['sondes'] = 'sondes';
	$table_des_tables['avis'] = 'avis';



	$spip_sondages = array(
						"id_sondage"	=> "bigint(21) NOT NULL",
						"id_rubrique"	=> "bigint(21) NOT NULL",
						"titre"			=> "text NOT NULL",
						"texte"			=> "longblob NOT NULL",
						"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"date_fin"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"lang"			=> "varchar(10) NOT NULL",
						"maj"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"type"			=> "enum('simple','multiple') NOT NULL default 'simple'",
						"en_ligne"		=> "enum('oui','non') NOT NULL default 'non'",
						"statut"		=> "enum('en_attente','publie','termine') NOT NULL default 'en_attente'",
						"extra"			=> "longblob NULL"
					);
	$spip_sondages_key = array(
						"PRIMARY KEY" 	=> "id_sondage"
					);

	$spip_choix = array(
						"id_choix"		=> "bigint(21) NOT NULL",
						"id_sondage"	=> "bigint(21) NOT NULL",
						"ordre"			=> "bigint(21) NOT NULL default '0'",
						"titre"			=> "text NOT NULL"
					);
	$spip_choix_key = array(
						"PRIMARY KEY" => "id_choix"
					);

	$spip_sondes = array(
						"id_sonde"		=> "bigint(21) NOT NULL",
						"id_sondage" 	=> "bigint(21) NOT NULL",
						"ip"		 	=> "varchar(255) NOT NULL",
						"date"			=> "datetime NOT NULL default '0000-00-00 00:00:00'"
					);
	$spip_sondes_key = array(
						"PRIMARY KEY" => "id_sonde"
					);

	$spip_avis = array(
						"id_avis"		=> "bigint(21) NOT NULL",
						"id_sonde"	 	=> "bigint(21) NOT NULL",
						"id_choix" 		=> "bigint(21) NOT NULL"
					);
	$spip_avis_key = array(
						"PRIMARY KEY" => "id_avis"
					);

	$spip_auteurs_sondages = array(
						"id_auteur"		=> "bigint(21) NOT NULL",
						"id_sondage"	=> "bigint(21) NOT NULL"
					);
	$spip_auteurs_sondages_key = array(
						"PRIMARY KEY" 		=> "id_auteur, id_sondage",
						"KEY id_mot"		=> "id_auteur",
						"KEY id_sondage"	=> "id_sondage"
					);

	$spip_documents_sondages = array(
						"id_document"	=> "bigint(21) NOT NULL default '0'",
						"id_sondage"	=> "bigint(21) NOT NULL default '0'"
					);

	$spip_documents_sondages_key = array(
						"KEY id_document"	=> "id_document",
						"KEY id_sondage"	=> "id_sondage"
					);

	$spip_mots_sondages = array(
						"id_mot"		=> "bigint(21) NOT NULL",
						"id_sondage"	=> "bigint(21) NOT NULL"
					);
	$spip_mots_sondages_key = array(
						"PRIMARY KEY" 		=> "id_mot, id_sondage",
						"KEY id_mot"		=> "id_mot",
						"KEY id_sondage"	=> "id_sondage"
					);



	$tables_principales['spip_sondages'] =
		array('field' => &$spip_sondages, 'key' => &$spip_sondages_key);
	$tables_principales['spip_choix'] =
		array('field' => &$spip_choix, 'key' => &$spip_choix_key);
	$tables_principales['spip_sondes'] =
		array('field' => &$spip_sondes, 'key' => &$spip_sondes_key);
	$tables_principales['spip_avis'] =
		array('field' => &$spip_avis, 'key' => &$spip_avis_key);



	$tables_auxiliaires['spip_auteurs_sondages'] = 
		array('field' => &$spip_auteurs_sondages, 'key' => &$spip_auteurs_sondages_key);
	$tables_auxiliaires['spip_documents_sondages'] = 
		array('field' => &$spip_documents_sondages, 'key' => &$spip_documents_sondages_key);
	$tables_auxiliaires['spip_mots_sondages'] = 
		array('field' => &$spip_mots_sondages, 'key' => &$spip_mots_sondages_key);



	$tables_jointures['spip_sondages'][]= 'rubriques';
	$tables_jointures['spip_sondages'][]= 'choix';
	$tables_jointures['spip_sondages'][]= 'sondes';
	$tables_jointures['spip_sondages'][]= 'avis';
	$tables_jointures['spip_sondages'][]= 'auteurs_sondages';
	$tables_jointures['spip_sondages'][]= 'auteurs';
	$tables_jointures['spip_sondages'][]= 'documents_sondages';
	$tables_jointures['spip_sondages'][]= 'documents';
	$tables_jointures['spip_sondages'][]= 'mots_sondages';
	$tables_jointures['spip_sondages'][]= 'mots';

	$tables_jointures['spip_rubriques'][]= 'sondages';

	$tables_jointures['spip_choix'][]= 'sondages';
	$tables_jointures['spip_choix'][]= 'sondes';
	$tables_jointures['spip_choix'][]= 'avis';

	$tables_jointures['spip_sondes'][]= 'sondages';
	$tables_jointures['spip_sondes'][]= 'choix';
	$tables_jointures['spip_sondes'][]= 'avis';

	$tables_jointures['spip_avis'][]= 'sondages';
	$tables_jointures['spip_avis'][]= 'choix';
	$tables_jointures['spip_avis'][]= 'sondes';

	$tables_jointures['spip_mots'][]= 'mots_sondages';
	$tables_jointures['spip_mots'][]= 'sondages';

	$tables_jointures['spip_auteurs'][]= 'auteurs_sondages';
	$tables_jointures['spip_auteurs'][]= 'sondages';

	$tables_jointures['spip_documents'][]= 'documents_sondages';
	$tables_jointures['spip_documents'][]= 'sondages';



	//
	// <BOUCLE(SONDAGES)>
	//
	function boucle_SONDAGES_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_sondages";  

			if (!$GLOBALS['var_preview']) {
				if (!$boucle->statut) {
					$boucle->where[]= array("'='", "'$id_table.en_ligne'", "'\"oui\"'");
					$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
				}
			}
			
	        return calculer_boucle($id_boucle, $boucles); 
	}


	//
	// <BOUCLE(CHOIX)>
	//
	function boucle_CHOIX_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_choix";  

			$boucle->default_order[] = "'$id_table.ordre ASC'" ;

	        return calculer_boucle($id_boucle, $boucles); 
	}


?>