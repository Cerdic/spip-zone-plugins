<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/
global $tables_principales;
	global $tables_auxiliaires;

	$spip_kaye = array(
						"id_kaye" => "int NOT NULL AUTO_INCREMENT",
						"id_classe" => "int",
						"id_auteur" => "int",
						"titre" => "varchar(100)",
						"discipline" => "tinytext",
						"descriptif" => "TEXT",
						"date_jour" => "DATE NOT NULL DEFAULT '0000-00-00'",
						"date_echeance" => "DATE NOT NULL DEFAULT '0000-00-00'",
						"statut" => "TINYTEXT");
										
	$spip_kaye_key = array(
						"PRIMARY KEY" => "id_kaye");
											
							
	$spip_documents_kaye = array(
						"id_document" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"id_kaye" => "BIGINT(21) NOT NULL");
					
	$spip_documents_kaye_key = array(
						"PRIMARY KEY" => "id_document");
						
	$spip_classekaye = array(
						"id_classe" => "int NOT NULL AUTO_INCREMENT",
						"id_auteur" => "int",
						"id_zone" => "int",
						"id_parent" => "int NOT NULL DEFAULT '0'",
						"titre" => "varchar(100)",
						"niveau" => "varchar(100)",
						"descriptif" => "TEXT");
										
	$spip_classekaye_key = array(
						"PRIMARY KEY" => "id_classe");

					
											
global $table_des_tables;
	$table_des_tables['kaye'] = 'kaye';
	$table_des_tables['documents_kaye'] = 'documents_kaye';
	$table_des_tables['classekaye'] = 'classekaye';
	
	$tables_principales['spip_kaye'] =
		array('field' => &$spip_kaye, 'key' => &$spip_kaye_key);
	$tables_principales['spip_documents_kaye'] =
		array('field' => &$spip_documents_kaye, 'key' => &$spip_documents_kaye_key);
	$tables_principales['spip_classekaye'] =
		array('field' => &$spip_classekaye, 'key' => &$spip_classekaye_key);
			
	
$tables_jointures['spip_documents_kaye'][]= 'kaye';

	



	//
	// <BOUCLE(KAYE)>
	//
	function boucle_KAYE_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_kaye";  

			if (!$GLOBALS['var_preview']) {
				if (!$boucle->statut) {
					$boucle->where[]= array("'IN'", "'$id_table.statut'", "'(\"publie\",\"envoi_en_cours\")'");
				}
			}
	        return calculer_boucle($id_boucle, $boucles); 
	}
	
	//
	// <BOUCLE(CLASSEKAYE)>
	//
	function boucle_CLASSEKAYE_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_classekaye";  

			if (!$GLOBALS['var_preview']) {
				if (!$boucle->statut) {
					$boucle->where[]= array("'IN'", "'$id_table.statut'", "'(\"publie\",\"envoi_en_cours\")'");
				}
			}
	        return calculer_boucle($id_boucle, $boucles); 
	}
?>