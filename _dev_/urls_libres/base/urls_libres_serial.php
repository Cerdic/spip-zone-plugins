<?php
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  as original founders of spip                                           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

include_spip('base/serial');
global $tables_principales,$table_primary, $table_des_tables, $table_date;

$spip_urls = array(
	// puisqu'il faut absoluement un id ...
	"id_url"			=> "bigint(21) NOT NULL",
	// comme url_propre
	"url"				=> "VARCHAR(255) NOT NULL",
	// la table cible
	"type"				=> "varchar(10) DEFAULT 'article' NOT NULL",
	// l'id dans la table
	"id_objet"			=> "bigint(21) NOT NULL",
	// reflète les changements, pas nécessaire
	"version"			=> "INTEGER DEFAULT '0' NOT NULL",
	// pourrait remplacer version
	"maj"				=> "TIMESTAMP");

$spip_urls_key = array(
	"PRIMARY KEY"		=> "id_url",
	"KEY url"			=> "url", // unique
	"KEY type"	=> "type, id_objet");

$tables_principales['spip_urls'] =
	array('field' => &$spip_urls, 'key' => &$spip_urls_key);

$table_primary['urls'] = "url";

// ??? $table_date['urls'] = 'maj';

$table_des_tables['urls'] = 'urls';

?>
