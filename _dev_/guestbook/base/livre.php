<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
	global $tables_principales;
	global $tables_auxiliaires;

	$spip_livre = array(
						"id_messages" => "BIGINT(21) NOT NULL auto_increment",
						"email"	=> "VARCHAR(255) NOT NULL",
						"nom"	=> "VARCHAR(255) NOT NULL",
						"ville"	=> "VARCHAR(255) NOT NULL",
						"maj"	=> "DATETIME NOT NULL default '0000-00-00 00:00:00'",
						"note"	=> "VARCHAR(255) NOT NULL",
						"texte"	=> "TEXT NOT NULL",
					);
	$spip_livre_key = array(
						"PRIMARY KEY" => "id_messages"
					);

	$spip_reponses_livre = array(
						"id_reponses" => "BIGINT(21) NOT NULL auto_increment",
						"id_messages" => "BIGINT(21) NOT NULL",
						"date" => "DATETIME NOT NULL default '0000-00-00 00:00:00'",
						"reponses" => "TEXT NOT NULL",
						"nom" => "VARCHAR(255)  NOT NULL",
						
					);
	$spip_reponses_livre_key = array(
						"PRIMARY KEY" => "id_reponses"
					);


	$tables_principales['spip_livre'] = array('field' => &$spip_livre, 'key' => &$spip_livre_key);
		
	$tables_principales['spip_reponses_livre'] = array('field' => &$spip_reponses_livre, 'key' => &$spip_reponses_livre_key);
		
		
global $table_des_tables;
$table_des_tables['livre'] = 'livre';
$table_des_tables['reponses_livre'] = 'reponses_livre';


$tables_jointures['spip_livre'][]= 'reponses_livres';
$tables_jointures['spip_reponses_livres'][]= 'livre';
//
// <BOUCLE(LIVRE)>
//
	function boucle_LIVRE_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_livre";  

			if (!$boucle->statut) {
				$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
			}
			
	        return calculer_boucle($id_boucle, $boucles); 
	}

	
?>