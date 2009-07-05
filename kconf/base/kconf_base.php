<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function kconf_declarer_tables_principales($tables_principales) {
  // table pour les squelettes
	$spip_kconfs = array(
		"fichier" => "VARCHAR (255) DEFAULT '' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"type"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"mtime" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"valeur"	=> "longtext DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP",
		);
	$spip_kconfs_key = array(
		"PRIMARY KEY" => "fichier,id_objet,objet",
		);
	$tables_principales['spip_kconfs'] =
		array('field' => &$spip_kconfs,  'key' => &$spip_kconfs_key);

	// tables pour les kconf_articles
	$spip_kconf_articles = array(
		"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"valeur"	=> "longtext DEFAULT '' NOT NULL",
		);
	$spip_kconf_articles_key = array(
		"KEY id_article"		=> "id_article",
		);
	$tables_principales['spip_kconf_articles'] =
		array('field' => &$spip_kconf_articles,  'key' => &$spip_kconf_articles_key);

	$spip_kconf_article_rubriques = array(
		"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"valeur"	=> "longtext DEFAULT '' NOT NULL",
		);
	$spip_kconf_article_rubriques_key = array(
			"KEY id_rubrique"		=> "id_rubrique",
		);
	$tables_principales['spip_kconf_article_rubriques'] =
		array('field' => &$spip_kconf_article_rubriques,  'key' => &$spip_kconf_article_rubriques_key);

	// tables pour les kconf_rubriques
	$spip_kconf_rubriques = array(
		"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"valeur"	=> "longtext DEFAULT '' NOT NULL",
		);
	$spip_kconf_rubriques_key = array(
		"KEY id_rubrique"		=> "id_rubrique",
		);
	$tables_principales['spip_kconf_rubriques'] =
		array('field' => &$spip_kconf_rubriques,  'key' => &$spip_kconf_rubriques_key);

	return $tables_principales;
}

function kconf_declarer_tables_auxiliaires($tables_auxiliaires) {
return $tables_auxiliaires;
}
function kconf_declarer_tables_interfaces($interfaces) {
return $interfaces;
}




?>