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

// Ce fichier ne sera execute qu'une fois
if (defined("_ECRIRE_INC_CARTE")) return;
define("_ECRIRE_INC_CARTE", "1");

function worldfileWidth($callage){	
	$tabcallage=worldfile2array($callage);
	return intval($tabcallage['bottom_right']['x']-$tabcallage['top_left']['x']);
}
function worldfileHeight($callage){	
	$tabcallage=worldfile2array($callage);
	return intval($tabcallage['bottom_right']['y']-$tabcallage['top_left']['y']);
}
function worldfileULX($callage){
	$tabcallage=worldfile2array($callage);
	return $tabcallage['bottom_right']['x'];
}
function worldfileULY($callage){
	$tabcallage=worldfile2array($callage);
	return $tabcallage['bottom_right']['y']*-1;
}
function worldfileLRX($callage){
	$tabcallage=worldfile2array($callage);
	return $tabcallage['top_left']['x'];
}
function worldfileLRY($callage){
	$tabcallage=worldfile2array($callage);
	return $tabcallage['top_left']['y'];
}
function getPropMapHeight($callage, $width){
	$tabcallage=worldfile2array($callage);
	// Calcul d'une hauteur relative à un callage (proportion)
	return $width*($tabcallage['bottom_right']['y']-$tabcallage['top_left']['y'])/($tabcallage['bottom_right']['x']-$tabcallage['top_left']['x']);
}


// Définition du callage à partir de la dimension de l'image
// Option par défaut / le callage est défini à la main si une projection est utilisée
function array2worldfile($mArray){	
	return "polygon(0 0,".$mArray['largeur']." ".$mArray['hauteur'].")";
}

function worldfile2array($callage){	
	$tabcallage['shape']=substr($callage,0,strpos($callage,"("));
	
	$coordpair = explode(',',substr($callage,strpos($callage,"(")+1,-1));
	$UL = explode (' ', $coordpair[0]);
	$LR = explode (' ', $coordpair[1]);
	
	$tabcallage['top_left']['x']=$UL[0];
	$tabcallage['top_left']['y']=$UL[1];
	$tabcallage['bottom_right']['x']=$LR[0];
	$tabcallage['bottom_right']['y']=$LR[1];
	
	return $tabcallage;
}



function getImgWidth ($url_carte)
{
	$image = spip_fetch_array(spip_query("SELECT largeur FROM spip_documents WHERE id_document = $url_carte"));
	return $image['largeur'];
}
function getImgHeight ($url_carte)
{
	$image = spip_fetch_array(spip_query("SELECT hauteur FROM spip_documents WHERE id_document = $url_carte"));
	return $image['hauteur'];
}


function getArg($texte,$idArg){
	$args=explode(",",$texte);
	return $args[$idArg];
}
function wkt2imgheight($args, $callage = "",$url_carte){
	$tab=wkt2imgsize($args, $callage,$url_carte);
	return $tab[0];
}
function wkt2imgwidth($args, $callage = "",$url_carte){
	$tab=wkt2imgsize($args, $callage,$url_carte);
	return $tab[1];
}
function wkt2imgsize($args="", $callage = "",$url_carte){
	//recuperer taille reelle carte
	$callage 	= worldfile2array ($callage);
	
	$GeoWidth 	= ($callage['bottom_right']['x']-$callage['top_left']['x']);
	$GeoHeight 	= ($callage['bottom_right']['y']-$callage['top_left']['y']);
	
	
	//recuperer taille de l'image
	$ImgRealWidth = getImgWidth ($url_carte);
	$ImgRealHeight = getImgHeight ($url_carte);
	
	//ou la taille passée, ou à defaut celle de la carte ???
	if ($ImgRealWidth) $ImgWidth = $ImgRealWidth;
	else $ImgWidth = $GeoWidth;
	if ($ImgRealHeight) $ImgHeight = $ImgRealHeight;
	else $ImgHeight = $GeoHeight;
	$ratio=$ImgHeight/$ImgWidth;
	
	//tailles maximum parametrées ?
	//TODO : optimiser un peu tout ca ...
	$ImgMaxWidth = getArg($args,1);
	$ImgMaxHeight = getArg($args,0);
	if ($ImgMaxWidth==0) $ImgMaxWidth=$ImgWidth;
	if ($ImgMaxHeight==0) $ImgMaxHeight=$ImgHeight;
	
	if (($ImgMaxWidth)&&($ImgMaxWidth<$ImgWidth))
	{
		$ImgWidth=$ImgMaxWidth;
		$ImgHeight=round($ratio*$ImgMaxWidth);
	}
	if (($ImgMaxHeight)&&($ImgMaxHeight<$ImgHeight))
	{
		$ImgHeight=$ImgMaxHeight;
		$ImgWidth=round($ImgMaxHeight/$ratio);
	}
	return array($ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth);
}
/*
 *   +---------------------------------------------+
 *    Nom du Filtre : WKT to SHAPE 
 *   +---------------------------------------------+
 *    Date : mercredi 09 avril 2003
 *    Auteur : 
 *    site :  
 *   +---------------------------------------------+
 *    Fonctions de ce filtre :
 *     Appelez le dans vos squellette tout simplement
 *     par : [(#GEOMETRIE|wkt2shape)]
 *   +---------------------------------------------+
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/
 *
 */
function wkt2shape($geometrie, $format = "HTML"){
	//TODO : securiser format
	$format="wkt2shape_".strtolower($format);//preg_match(",([A-Z]+),", $format, $format);

	$wktGeomType = substr($geometrie,0,strpos($geometrie,"("));

	if (function_exists($format)) return $format($wktGeomType);
	else return "";
}
/*
 *   +---------------------------------------------+
 *    Nom du Filtre : WKT to COORDS
 *   +---------------------------------------------+
 *    Date : mercredi 09 avril 2003
 *    Auteur : 
 *    site :  
 *   +---------------------------------------------+
 *    Fonctions de ce filtre :
 *     Appelez le dans vos squellette tout simplement
 *     par : [(#GEOMETRIE|wkt2coords)]
 *   +---------------------------------------------+
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/
 *
 */
function wkt2coords($geometrie, $format = "HTML", $callage = "",$url_carte, $mot_desc = "", $args = ""){
	//TODO : securiser format
	$format="wkt2coords_".strtolower($format);//preg_match(",([A-Z]+),", $format, $format);
	//recuperer geometrie
	$wktGeomType = substr($geometrie,0,strpos($geometrie,"("));
	$wktGeom 	= substr($geometrie,strpos($geometrie,"(")+1,-1);
	
	$tab=wkt2imgsize($args, $callage,$url_carte);
	//recuperer taille affichée 
	$ImgHeight=$tab[0];
	$ImgWidth=$tab[1];
	//recuperer taille reelle carte
	$GeoHeight 	= $tab[2];
	$GeoWidth 	= $tab[3];

	$radius	= 6;	// TODO : Paramètre (ou fonction du callage ?) préférable car fonction des projections 5 peut être bcp trop petit
	$rgeo 		= round($radius*$GeoWidth/$ImgWidth);
	$callage 	= worldfile2array ($callage);
	
	if (function_exists($format)) return $format($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage,$mot_desc);
	else return "";
}
///////////////////////////////////////////////
// JSDHTML
///////////////////////////////////////////////
function wkt2coords_jsdhtml($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage,$mot_desc){	

			$coordpair = explode (',',$wktGeom);
			
			for ($i=0; $i<sizeof($coordpair); $i++)
			{		
				$coord[$i] 		= explode(' ',$coordpair[$i]);
				$coord[$i][0] 	= ($coord[$i][0]-$callage['top_left']['x'])/$GeoWidth*$ImgWidth;
				$coord[$i][1] 	= ($callage['bottom_right']['y']-$coord[$i][1])/$GeoHeight*$ImgHeight;		// Calcul de l'Y par rapport au coin inférieur
 				$coord[$i][1]   = $ImgHeight/2-($coord[$i][1]-$ImgHeight/2);								// Pour que le dessin DHTML soit ok 
 				$coordpair[$i] 	= implode(',',$coord[$i]);
			}
			$wktGeom = implode(',', $coordpair);

			return $wktGeom;
}
///////////////////////////////////////////////
// HTML
///////////////////////////////////////////////
function wkt2shape_html($wktGeomType){
			if (strcasecmp($wktGeomType,"POLYGON")==0)
				return "POLY"; 
			if (strcasecmp($wktGeomType,"POINT")==0)
				return "CIRCLE"; 
			// HTML Image map ne supporte pas les lignes
}
function wkt2coords_html($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage,$mot_desc){	

			// Pour obtenir des objets HTML/MAP ...
			// 	Séparateur coordonnées ',' / Séparateur paires de coordonnées ','
			$coordpair = explode (',',$wktGeom);
			
			for ($i=0; $i<sizeof($coordpair); $i++)
			{		
				$coord[$i] 		= explode(' ',$coordpair[$i]);
				$coord[$i][0] 	= ($coord[$i][0]-$callage['top_left']['x'])/$GeoWidth*$ImgWidth;
				$coord[$i][1] 	= ($callage['bottom_right']['y']-$coord[$i][1])/$GeoHeight*$ImgHeight;		// Calcul de l'Y par rapport au coin inférieur
 				$coordpair[$i] 	= implode(',',$coord[$i]);
			}
			$wktGeom = implode(',', $coordpair);

			if (strcasecmp($wktGeomType,"POLYGON")==0)
				return $wktGeom;
			else if (strcasecmp($wktGeomType,"POINT")==0)
				return $wktGeom.",6";//.$radius;
			else return "";
}
function wkt2coords_htmldiv($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage,$mot_desc){	
			// Pour obtenir des objets HTML/DIV ...
			// Pour les polygones, la moyenne des valeurs
			// Pour les points, la valeur
			$coordpair = explode (',',$wktGeom);
			
			for ($i=0; $i<sizeof($coordpair); $i++)
			{		
				$coord[$i] 		= explode(' ',$coordpair[$i]);
				$coord[$i][0] 	= ($coord[$i][0]-$callage['top_left']['x'])/$GeoWidth*$ImgWidth;
				$coord[$i][1] 	= ($callage['bottom_right']['y']-$coord[$i][1])/$GeoHeight*$ImgHeight;		// Calcul de l'Y par rapport au coin inférieur
 				$x 				+= $coord[$i][1];
				$y				+= $coord[$i][0];
			}
			return "left: ".$y/$i."px;top: ".$x/$i."px;";
}

///////////////////////////////////////////////
// SVG
///////////////////////////////////////////////

function wkt2shape_svg($wktGeomType){
			if (strcasecmp($wktGeomType,"POLYGON")==0)
				return "polygon"; 
			else if (strcasecmp($wktGeomType,"POINT")==0)
				return "use"; 
			else if (strcasecmp($wktGeomType,"LINE")==0)
				return "polyline"; 
			else return "";
}

function wkt2coords_svg($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage,$mot_desc){	
			// Pour obtenir des objets SVG ...
			// Conservation des Y selon le format WKT car en coordonnées géographique
			// Séparateur coordonnées ',' / Séparateur paires de coordonnées ' '
			$coordpair = explode (',',$wktGeom);

			for ($i=0; $i<sizeof($coordpair); $i++)
			{		
				$coord[$i] 		= explode(' ',$coordpair[$i]);
				$coord[$i][0] 	= $coord[$i][0];
				$coord[$i][1] 	= $coord[$i][1]*-1;
 				$coordpair[$i] 	= implode(',' ,$coord[$i]);
			}
			$wktGeom = implode(' ', $coordpair);

			if (strcasecmp($wktGeomType,"POLYGON")==0 || strcasecmp($wktGeomType,"LINE")==0){  	
				return "points=\"".$wktGeom."\"";		
			} elseif (strcasecmp($wktGeomType,"POINT")==0){	
				$coordTab = explode (",", $wktGeom);
				return " x=\"".$coordTab[0]."\" y=\"".$coordTab[1]."\" width=\"".$rgeo."\" height=\"".$rgeo."\" xlink:href=\"#use".$mot_desc."\"";
			}
}
///////////////////////////////////////////////
// GOOGLE
///////////////////////////////////////////////
function wkt2coords_google($wktGeomType,$wktGeom,$ImgHeight,$ImgWidth,$GeoHeight,$GeoWidth,$rgeo,$callage){	
// Conservation des Y selon le format WKT car en coordonnées géographique
			$coordpair = explode (',',$wktGeom);
			
			for ($i=0; $i<sizeof($coordpair); $i++)
			{		
				$coord[$i] 		= explode(' ',$coordpair[$i]);
				$coord[$i][0] 	= $coord[$i][0];
				$coord[$i][1] 	= $coord[$i][1]*-1;
 				$wktGoogle[$i]="GPoint(".intval($coord[$i][0]*$rgeo).",".intval($coord[$i][1]*$rgeo).")";
			}
			$wktGoogle = implode(',', $wktGoogle);

			if (strcasecmp($wktGeomType,"POLYGON")==0)
			{  	
				return "GPolyline=([".$wktGoogle."])";		
			}
			elseif (strcasecmp($wktGeomType,"LINE")==0)
			{  	
				return "GPolyline=([".$wktGoogle."])";		
			}
			elseif (strcasecmp($wktGeomType,"POINT")==0)
			{	
				$coordTab = explode (" ", $wktGeom);
				return "GPoint(".intval($coordTab[0]*$rgeo).",".intval($coordTab[1]*$rgeo).")";
			}
}
function worldfile2google($callage){	
	$tabcallage=worldfile2array($callage);
	$x=intval($tabcallage['top_left']['x']-($tabcallage['bottom_right']['x']-$tabcallage['top_left']['x'])/2);
	$y=intval($tabcallage['top_left']['y']-($tabcallage['bottom_right']['y']-$tabcallage['top_left']['y'])/2);
	return "GPoint(".$x.",".$y.")";
}

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