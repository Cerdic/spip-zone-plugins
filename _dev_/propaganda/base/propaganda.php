<?php
/*
 * Spip Propaganda
 * Carte postale numŽrique via spip
 *
 * Autores :
 * kent1, Dani
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

global $tables_principales;
global $tables_auxiliaires;

//table propaganda  ------------------------------------------
$spip_propaganda = array(
	"id_propaganda" 	=> "bigint(21) NOT NULL",
	"id_auteur" => "bigint(21) NOT NULL",
	"id_document" => "bigint(21) NOT NULL",
	"titre" => "varchar(255) NOT NULL",
	"texte" => "text",
	"email_destinataire" => "text",
	"hash" => "varchar(255) NOT NULL",
	"confidentiel" => "varchar(255) NOT NULL",
	"maj"	=> "TIMESTAMP"
	);
	
$spip_propaganda_key = array(
	"PRIMARY KEY" => "id_propaganda",
	"KEY id_article" => "id_auteur",
	"KEY id_document" => "id_document",
	);

$spip_propaganda_join = array(
	"id_auteur"=>"id_auteur",
	"id_document"=>"id_document",
	);

$tables_principales['spip_propaganda'] = array(
	'field' => &$spip_propaganda,
	'key' => &$spip_propaganda_key,
	'joint' => &$spip_propaganda_join
	);

//-- Relaci—ns ----------------------------------------------------
global $table_des_tables;
$table_des_tables['propaganda']='propaganda';

//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_auteurs'][]= 'propaganda';
$tables_jointures['spip_documents'][]= 'propaganda';
?>