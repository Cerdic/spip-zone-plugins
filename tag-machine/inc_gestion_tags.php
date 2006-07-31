<?php

//	  inc_gestion_tag.php
  //    Librairies pour gérerer un formulaire de gestion des tags
  //    Distribué sans garantie sous licence GPL.
  //
  //    Author  BoOz
  
  //    Bricolage (sale) à partir du code SPIP
   
  //    This program is free software; you can redistribute it and/or modify
  //    it under the terms of the GNU General Public License as published by
  //    the Free Software Foundation; either version 2 of the License, or any later version.
  //
  //    This program is distributed in the hope that it will be useful,
  //    but WITHOUT ANY WARRANTY; without even the implied warranty of
  //    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  //    GNU General Public License for more details.
  //
  //    You should have received a copy of the GNU General Public License
  //    along with this program; if not, write to the Free Software
 //    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

//
// Ce fichier ne sera execute qu'une fois
if (defined("_GESTION_TAG")) return;
define("_GESTION_TAG", "1");

if(@file_exists('ecrire/inc_version.php')){
include('ecrire/inc_version.php');
}elseif(@file_exists('ecrire/inc_version.php3')){
include('ecrire/inc_version.php3');
}elseif(@file_exists('inc_version.php')){
include('inc_version.php');
}elseif(@file_exists('inc_version.php3')){
include('inc_version.php3');
}
$path_inc_abstract_sql="inc_abstract_sql". _EXTENSION_PHP ;
include_ecrire ($path_inc_abstract_sql);

include('inc_tag-machine.php');

//effacer un tags et toutes ses affiliations 

function effacer_tag(){
global $_POST;
$effacer_tag = $_POST['effacer_tag'] ;
$effacer_valid	= $_POST['effacer_valid'] ;
$id_tag = addslashes($_POST['id_tag']);

if($effacer_tag AND ($effacer_valid AND $id_tag)){
	//echo $id_tag;
	$query2 = "DELETE FROM spip_mots_documents WHERE id_mot='$id_tag'";
	//echo $query;
	$result = spip_query($query2);
	
	echo "<br>tag supprimé des documents";
		
	$query3 = "DELETE FROM spip_mots WHERE id_mot='$id_tag'";
	$result = spip_query($query3);
	echo "<br>tag supprimé";

	}elseif($effacer_tag AND $id_tag){
	
	$query = "SELECT titre FROM spip_mots WHERE id_mot='$id_tag'";
		$result = spip_query($query);
		while ($row = spip_fetch_array($result)) {
		$titre_mot=$row['titre'];
		}
	
			if($titre_mot==''){}else{echo "<h3>Effacer $titre_mot </h3>";
		
			$query = "SELECT id_document FROM spip_mots_documents WHERE id_mot='$id_tag'";
				$result = spip_query($query);
				while ($row = spip_fetch_array($result)) {
				$id_doc=$row['id_document'];
				echo "<br />$id_doc";
				}
			
				echo '<form action="'.$PHP_SELF.'" method="post">';
				echo '<input type="hidden"  name="effacer_tag" value="ok">';
				echo '<input type="hidden"  name="id_tag" value="'.$id_tag.'">';
				echo '<input type="submit"  name="effacer_valid" value="Effacer">';
				echo '</form>';
				}
		
	}else{
	
	echo "<h2>Effacer un  tag</h2>";


	echo '<form action="'.$PHP_SELF.'" method="post">';
	echo '<input type="text"  name="id_tag" size="20"> <label for="nom_tag"> id_tag (existant) </label> <br>';
	echo '<input type="submit"  name="effacer_tag" value="ok">';
	echo '</form>';
	
	}

}


//Taguer les documents d'un site

function taguer_documents_site(){

global $_POST;
$id_site = $_POST['id_site'] ;
$id_tag = $_POST['id_tag'] ;

echo "<h2>Taguer les documents d'un site</h2>";

echo '<form action="'.$PHP_SELF.'" method="post">';
echo '<input type="text"  name="id_tag" size="20"> <label for="nom_tag"> id_tag (existant) </label> <br>';
echo '<input type="text"  name="id_site" size="20"> <label for="id_site"> id_site (existant) </label>';
echo '<input type="submit"  name="valider_tag" value="ok">';
echo '</form>';

if($id_site AND $id_tag){

$query = "SELECT id_syndic FROM spip_syndic WHERE id_syndic='$id_site'";
$result = spip_query($query);
while ($row1 = spip_fetch_array($result)) {
	$id_syndic = $row1['id_syndic'];
	$query2 = "SELECT id_document FROM spip_documents_syndic WHERE id_syndic='$id_syndic'";
	$result2 = spip_query($query2);
	while ($row2 = spip_fetch_array($result2)) {
	$id_doc = $row2['id_document'];
	echo $id_doc."->";
	
	$query3 = "SELECT id_mot FROM spip_mots_documents WHERE id_document='$id_doc' AND id_mot='$id_tag'";
	$result3 = spip_query($query3);
	
	if (spip_num_rows($result3) == 0){
	$query4 = "INSERT into spip_mots_documents (id_mot, id_document) VALUES ('$id_tag','$id_doc')";
	$result4 = spip_query($query4);
	}else{
	echo "oui";
	}
	echo "<br>";
	

	
	}
	}
	
}	


}


function basenametotitre(){

echo "<h2>renommer les documents sans titre</h2>";
$query = "SELECT id_document, fichier FROM spip_documents WHERE titre=''";
$result = spip_query($query);
while ($row9 = spip_fetch_array($result)) {
	$id_document = $row9['id_document'];
	$url_document = $row9['fichier'];
	$pathparts = pathinfo($url_document);
	//print_r($pathparts);
	$titre = $pathparts[basename] ;
	$titre=ereg_replace("(.mp3|.ogg|.ram)","",$titre);
	$titre=ereg_replace("_"," ",$titre);
	$titre = addslashes($titre);
	echo $titre."<br>";
	spip_query("UPDATE spip_documents SET titre='$titre' WHERE id_document='$id_document'");
	
	}
}

echo "<div style='float:right;width:33%'>";
nuage_tags();
echo "</div>";

if($id_mot){
$row_titre = spip_fetch_array(spip_query("SELECT titre FROM spip_mots WHERE id_mot='$id_mot'"));
echo "<h1>".$row_titre['titre']."</h1>";
$query = "SELECT id_document FROM spip_mots_documents WHERE id_mot='$id_mot'";
$result = spip_query($query);
echo "<ul>";
while ($row = spip_fetch_array($result)) {
$query2 = "SELECT titre FROM spip_documents WHERE id_document=".$row['id_document'];
$result2 = spip_fetch_array(spip_query($query2));
echo "<li><a href='gestion.php3?id_document=".$row['id_document']."'>".$result2['titre']."</a></li>";
}
echo "</ul>";

}else{
effacer_tag();
taguer_documents_site();
basenametotitre();
}

?>