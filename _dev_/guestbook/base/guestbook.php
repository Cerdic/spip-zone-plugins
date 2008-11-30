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
	if (!defined("_ECRIRE_INC_VERSION")) return;
	function guestbook_declarer_tables_principales($tables_principales){
		// La table pricipale où sont stockés les messages
		$spip_guestbook = array(
			"id_message" => "BIGINT(21) NOT NULL auto_increment",
			"message" =>  "TEXT",
			"email"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"nom" => "VARCHAR(255) NOT NULL DEFAULT '0'",
			"ville" => "BIGINT(21) NOT NULL",
			"statut" => "VARCHAR(8) NOT NULL",
			"ip" => "VARCHAR(255) NOT NULL",
			"note" => "VARCHAR(255) NOT NULL",
			"date"	=> "DATETIME"
		);
		$spip_guestbook_key = array(
			"PRIMARY KEY" => "id_message",
			"KEY message" => "message",
			"KEY email" => "email",
			"KEY nom"	=> "nom",
			"KEY ville" => "ville",
			"KEY statut" => "statut",
			"KEY ip" => "ip",
			"KEY note" => "note"
		);

		$tables_principales['spip_guestbook'] = array(
			'field' => &$spip_guestbook,
			'key' => &$spip_guestbook_key
		);
		return $tables_principales;
	}
	
	function guestbook_declarer_tables_auxiliaires($tables_auxiliaires){	
		$spip_guestbook_reponses = array(
			"id_reponse" => "BIGINT(21) NOT NULL auto_increment",
			"id_message" => "BIGINT(21) NOT NULL",
			"id_auteur" => "BIGINT(21) NOT NULL",
			"message" =>  "TEXT",
			"statut" => "VARCHAR(8) NOT NULL",
			"date"	=> "DATETIME",
		);
		$spip_guestbook_reponses_key = array(
			"PRIMARY KEY" => "id_reponse",
			"KEY id_message" => "id_message",
			"KEY id_auteur" => "id_auteur",
			"KEY message"	=> "message",
			"KEY statut" => "statut"
		);

		$tables_auxiliaires['spip_guestbook_reponses'] = array(
			'field' => &$spip_guestbook_reponses,
			'key' => &$spip_guestbook_reponses_key
		);
		return $tables_auxiliaires;
	}
	
	function notation_declarer_tables_interfaces($interface){
		// definir les jointures possibles
		$interface['table_des_tables']['guestbook'] = 'guestbook';
		$interface['table_des_tables']['guestbook_reponses']  = 'reponses';
		$interface['tables_jointures']['spip_guestbook'][] = 'guestbook';
		$interface['tables_jointures']['spip_guestbook_reponses'][] = 'reponses';
		return $interface;
	}
	
//
// <BOUCLE(LIVRE)>
//
	function boucle_LIVRE_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_guestbook";

			if (!$boucle->statut) {
				$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
			}
			
	        return calculer_boucle($id_boucle, $boucles); 
	}

	
?>