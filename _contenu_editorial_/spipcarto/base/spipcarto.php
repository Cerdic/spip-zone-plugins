<?php
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d’élaboration d’information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005
*
* Stéphane Laurent, François-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Léger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jégou - Mathieu Géhin - Michel Briand - 
* Mose - Olivier Frérot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou l’aide en ligne.
* 
— -
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
— -
*
\***************************************************************************/
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
	"id_srs" => "bigint(21) NOT NULL");

$spip_carto_cartes_key = array(
	"PRIMARY KEY" => "id_carto_carte",
	"KEY id_carto_carte" => "id_carto_carte");


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
	"geometrie" => "TEXT BINARY NOT NULL");
	
$spip_carto_objets_key = array(
	"PRIMARY KEY" => "id_carto_objet",
	"KEY id_carto_carte" => "id_carto_carte",
	"KEY titre" => "titre");

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


$tables_auxiliaires['spip_carto_cartes_articles'] = array(
	'field' => &$spip_carto_cartes_articles,
	'key' => &$spip_carto_cartes_articles_key);

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


$tables_principales['spip_carto_cartes'] =
	array('field' => &$spip_carto_cartes, 'key' => &$spip_carto_cartes_key);

$tables_principales['spip_carto_objets'] =
	array('field' => &$spip_carto_objets, 'key' => &$spip_carto_objets_key);

//Relation avec les articles
$tables_auxiliaires['spip_carto_cartes_articles'] = array(
	'field' => &$spip_carto_cartes_articles,
	'key' => &$spip_carto_cartes_articles_key);

//La, ca se discute ...
//Ca devrait etre une table secondaire
//mais comme on n'utilise que l'id_mot dans la boucle
//on gagne une jointure dans la requete
//$tables_principales['spip_mots_carto_objets'] = array(
//	'field' => &$spip_mots_carto_objets,
//	'key' => &$spip_mots_carto_objets_key);
$tables_auxiliaires['spip_mots_carto_objets'] = array(
	'field' => &$spip_mots_carto_objets,
	'key' => &$spip_mots_carto_objets_key);
	

$table_primary['carto_objets']="id_carto_objet";
$table_primary['carto_cartes']="id_carto_carte";

$table_des_tables['carto_objets']="carto_objets";
$table_des_tables['carto_cartes']="carto_cartes";

$tables_relations['documents']['id_carto_carte']='documents_carto_cartes';
$tables_relations['mots']['id_carto_objet']='mots_carto_objets';
$tables_relations['carto_objets']['id_mot']='mots_carto_objets';
$tables_relations['carto_cartes']['id_document']='documents_carto_cartes';

function boucle_CARTO_CARTES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$boucle->from[] =  "spip_carto_cartes AS " . $boucle->type_requete;
	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_CARTO_OBJETS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$boucle->from[] =  "spip_carto_objets AS " . $boucle->type_requete;
	return calculer_boucle($id_boucle, $boucles); 
}


?>