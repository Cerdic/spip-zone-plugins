<?php
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d'elaboration d'information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005-2006
*
* Stephane Laurent, Franeois-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Leger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jegou - Mathieu Gehin - Michel Briand - 
* Mose - Olivier Frerot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.
* 
e -
This program is free software ; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation ; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY ; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program (COPYING.txt) ; if not, write to
the Free Software Foundation, Inc.,
59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
or check http://www.gnu.org/copyleft/gpl.html
e -
*
\***************************************************************************/
$GLOBALS['rep_cartes']="modeles";
//////////////////////////////////////////////////
//////////////////////////////////////////////////
// PARAMETRAGE
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//////////////////////////////////////////////////
// CARTO_CARTES
//////////////////////////////////////////////////
$spip_carto_cartes = array(
	"id_carto_carte" => "bigint(21) NOT NULL",
	"titre" => "VARCHAR(255) BINARY NOT NULL",
	"texte" => "TEXT BINARY NOT NULL",
	"url_carte" => "TEXT BINARY NOT NULL",
	"callage" => "TEXT BINARY NOT NULL",
	"id_srs" => "bigint(21) NOT NULL",
	"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL");

$spip_carto_cartes_key = array(
	"PRIMARY KEY" => "id_carto_carte");


//////////////////////////////////////////////////
// CARTO_OBJETS
//////////////////////////////////////////////////

$spip_carto_objets = array(
	"id_carto_objet" => "bigint(21) NOT NULL",
	"id_carto_carte" => "bigint(21) NOT NULL",
	"titre" => "VARCHAR(255) BINARY NOT NULL",
	"texte" => "TEXT BINARY NOT NULL",
	"url_objet" => "TEXT BINARY NOT NULL",
	"url_logo" => "TEXT BINARY NOT NULL",
	"geometrie" => "TEXT BINARY NOT NULL",
	"statut"	=> "VARCHAR(8) NOT NULL default 'publie'",
	"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
	);
	
$spip_carto_objets_key = array(
	"PRIMARY KEY" => "id_carto_objet",
	"KEY id_carto_carte" => "id_carto_carte",
	"KEY titre" => "titre",
	"KEY statut" => "statut"
	);

//////////////////////////////////////////////////
// CARTO_CARTES_ARTICLES
//////////////////////////////////////////////////

$spip_carto_cartes_articles = array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_carto_cartes_articles_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_article" => "id_article");


//////////////////////////////////////////////////
// CARTO_CARTES_ARTICLES
//////////////////////////////////////////////////

$spip_carto_cartes_articles = array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_carto_cartes_articles_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_article" => "id_article");


//////////////////////////////////////////////////
// MOTS_CARTO_OBJETS
//////////////////////////////////////////////////

$spip_mots_carto_objets= array(
	"id_carto_objet" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_mot" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_carto_objets_key = array(
	"KEY id_carto_objet" 	=> "id_carto_objet",
	"KEY id_mot" => "id_mot");

//////////////////////////////////////////////////
// DOCUMENTS_CARTO_OBJETS
//////////////////////////////////////////////////

$spip_documents_carto_cartes= array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_document" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_documents_carto_cartes_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_document" => "id_document");


//////////////////////////////////////////////////
//////////////////////////////////////////////////
// DECLARATION
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//global $tables_principales,$table_primary,$tables_auxiliaires,$tables_relations;


$GLOBALS['tables_principales']['spip_carto_cartes'] =
	array('field' => &$spip_carto_cartes, 'key' => &$spip_carto_cartes_key);

$GLOBALS['tables_principales']['spip_carto_objets'] =
	array('field' => &$spip_carto_objets, 'key' => &$spip_carto_objets_key);

//Relation avec les articles
$GLOBALS['tables_auxiliaires']['spip_carto_cartes_articles'] = array(
	'field' => &$spip_carto_cartes_articles,
	'key' => &$spip_carto_cartes_articles_key);

$GLOBALS['tables_auxiliaires']['spip_mots_carto_objets'] = array(
	'field' => &$spip_mots_carto_objets,
	'key' => &$spip_mots_carto_objets_key);
	
$GLOBALS['tables_auxiliaires']['spip_documents_carto_cartes'] = array(
	'field' => &$spip_documents_carto_cartes,
	'key' => &$spip_documents_carto_cartes_key);
	

$GLOBALS['table_primary']['carto_objets']="id_carto_objet";
$GLOBALS['table_primary']['carto_cartes']="id_carto_carte";

$GLOBALS['table_des_tables']['carto_objets']="carto_objets";
$GLOBALS['table_des_tables']['carto_cartes']="carto_cartes";

$GLOBALS['tables_jointures']['spip_mots'][]= 'mots_carto_objets';
$GLOBALS['tables_jointures']['spip_carto_objets'][]='mots_carto_objets';
$GLOBALS['tables_jointures']['spip_documents'][]='documents_carto_cartes';
$GLOBALS['tables_jointures']['spip_carto_cartes'][]='documents_carto_cartes';
$GLOBALS['tables_jointures']['spip_articles'][]='carto_cartes_articles';
$GLOBALS['tables_jointures']['spip_carto_cartes'][]='carto_cartes_articles';


?>