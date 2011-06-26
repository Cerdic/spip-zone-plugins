<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Declarer dans la table des tables pour sauvegarde
function doc2article_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['doc2article'] = 'doc2article';
	return $interfaces;
}

function doc2article_declarer_tables_principales($tables_principales){
	
	$spip_doc2article = array(
		"id_doc2article" => "bigint(21) NOT NULL",
		"id_auteur"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"id_rubrique" => "bigint(21) NOT NULL DEFAULT '0'",
		"fichier" => "text NOT NULL",
		"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
	);
	
	$spip_doc2article_key = array(
		"PRIMARY KEY" => "id_doc2article",
		"KEY id_auteur" => "id_auteur",
		"KEY id_rubrique" => "id_rubrique"
	);

	$tables_principales['spip_doc2article'] = array(
		'field' => &$spip_doc2article,
		'key' => &$spip_doc2article_key
	);

	return $tables_principales;
}

?>