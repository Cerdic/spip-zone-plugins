<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function photospip_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_documents_inters'][] = 'documents';
	$interface['tables_jointures']['spip_documents_inters'][] = 'auteurs';
	
	$interface['table_des_tables']['documents_inters']='documents_inters';
	
	return $interface;
}

function photospip_declarer_tables_principales($tables_principales){
	$documents_inters = array(
		"id_documents_inter"	=> "bigint(21) NOT NULL AUTO_INCREMENT",
		"id_document"	=> "bigint(21) NOT NULL", //document original
		"id_auteur"	=> "bigint(21) NOT NULL", //qui a modifié
		"extension"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"fichier"	=> "varchar(255) DEFAULT '' NOT NULL",
		"taille"	=> "integer",
		"largeur"	=> "integer",
		"hauteur"	=> "integer",
		"mode"	=> "ENUM('vignette', 'image', 'document') DEFAULT 'document' NOT NULL",
		"version" => "bigint(21) NOT NULL",
		"filtre" => "text",
		"param" => "text",
		"maj" 	=> "TIMESTAMP", //quand ca a eut lieu
	);
	  	
	$documents_inters_key = array(
		"PRIMARY KEY" => "id_documents_inter, id_document",
		"KEY id_document"	=> "id_document",
		"KEY id_auteur"	=> "id_auteur");
	
	$tables_principales['spip_documents_inters'] = array(
		'field' => &$documents_inters,
		'key' => &$documents_inters_key);
		
	return $tables_principales;
}

?>
