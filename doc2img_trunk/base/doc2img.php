<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function doc2img_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['doc2img'] = 'doc2img';
	$interface['tables_jointures']['spip_documents'][] = 'doc2img';

	return $interface;

}

function doc2img_declarer_tables_principales($tables_principales){

	$spip_doc2img_champs = array(
		'id_doc2img' => "bigint(21) NOT NULL",
		'id_document' => "bigint(21) NOT NULL",
		'fichier' => "varchar(255) NOT NULL",
		'page' => "int NOT NULL",
		"taille"	=> "integer",
		"largeur"	=> "integer",
		"hauteur"	=> "integer",
	);

    $spip_doc2img_key = array("PRIMARY KEY"	=> "id_doc2img", );
	$spip_doc2img_cles = array(
		"PRIMARY KEY" 	=> "id_doc2img",
		"KEY id_document" => "id_document",
		"UNIQUE KEY document" => "id_document,page"
	);

	$tables_principales['spip_doc2img'] = array(
		'field' => &$spip_doc2img_champs,
		'key' => &$spip_doc2img_cles
	);

	return $tables_principales;

}
?>