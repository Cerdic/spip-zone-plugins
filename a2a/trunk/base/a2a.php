<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['articles_lies'] = 'articles_lies';
	return $interface;
}


/**
 * Table auxilaire spip_articles_lies
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function a2a_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_articles_lies = array(
		"id_article"	=> "bigint(21) NOT NULL",
		"id_article_lie"	=> "bigint(21) NOT NULL",
		"rang"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"type_liaison" => "varchar(25) DEFAULT ''",
	);
	
	$spip_articles_lies_key = array(
		"PRIMARY KEY"		=> "id_article, id_article_lie,type_liaison"
	);

	$spip_articles_lies_join = array(
		"id_article"		=> "id_article",
		"id_article_lie"		=> "id_article_lie"
	);
	
	$spip_mots_liens = array(
			"id_mot"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL");

	$spip_mots_liens_key = array(
			"PRIMARY KEY"		=> "id_mot,id_objet,objet",
			"KEY id_mot"	=> "id_mot",
			"KEY id_objet"	=> "id_objet",
			"KEY objet"	=> "objet",
	);

	$tables_auxiliaires['spip_articles_lies'] =
		array(
			'field' => &$spip_articles_lies,
			'key' => &$spip_articles_lies_key,
			'join' => &$spip_articles_lies_join
		);
		
	return $tables_auxiliaires;
}

?>