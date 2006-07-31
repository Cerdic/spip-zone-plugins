<?php

 //	  inc_formulaire_document.php
  //    Librairies pour gérerer un formulaire d'édition d'un document
  //    Distribué sans garantie sous licence GPL.
  //
  //    Author  BoOz
  
  //    Bricolage (sale) à partir du code SPIP
  
  /* utiliser : <?php 
  include_spip('formulaires/inc_formulaire_document.php');
  afficher_formulaire_document_tag('[(#ENV{id_document})]','nom_groupe'); 
  ?>  dans un squelette */
  /* ou bien  
  include_spip('formulaires/inc_formulaire_document.php');
  afficher_formulaire_document_tag('$id_document','nom_groupe');  
  
  dans une page php de spip */
  
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
if (defined("_LIB_FORMULAIRE_DOCUMENT")) return;
define("_LIB_FORMULAIRE_DOCUMENT", "1");

if(@file_exists('ecrire/inc_version.php')){
include('ecrire/inc_version.php');
include_spip('urls/standard');
}elseif(@file_exists('ecrire/inc_version.php3')){
include('ecrire/inc_version.php3');
include_spip('urls/standard');
}elseif(@file_exists('inc_version.php')){
include('inc_version.php');
include_spip('urls/standard');
}elseif(@file_exists('inc_version.php3')){
include('inc_version.php3');
include_spip('urls/standard');
}
include_spip ('base/abstract_sql');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/documents');
include_spip('inc/texte');
include_spip('base/db_mysql');

include_spip('inc/tag-machine');

function maj_tags (  $groupe_defaut='',
					  $nom_objet='documents',
					  $id_objet='id_document') {
	global $_POST;

	if ($liste_tags=$_POST['liste_tags'] 
	AND $id_document = intval($_POST['id_document'])
	AND $_POST['modif_tags'] == 'oui') {
	spip_query("DELETE FROM spip_mots_documents WHERE id_document=$id_document");
	ajouter_mots($liste_tags,$id_document,$groupe_defaut, $nom_objet,$id_objet) ;
		if($taguer_freres=$_POST['taguer_freres']
		AND $id_parent=$_POST['id_parent'] ){
		
		$result_freres=spip_query("SELECT id_document FROM spip_documents_syndic WHERE id_syndic_article = '$id_parent' ");
			while($row_freres=spip_fetch_array($result_freres)){
			$id_frere=$row_freres['id_document'];
			spip_query("DELETE FROM spip_mots_documents WHERE id_document=$id_frere");
			ajouter_mots($liste_tags,$id_frere,$groupe_defaut, $nom_objet,$id_objet) ;
			}
		
		}


	}elseif($liste_tags=$_POST['liste_tags'] 
	AND $id_document = intval($_POST['id_document'])
	AND $_POST['retirer_tags'] == 'oui'){
	retirer_mots($liste_tags,$id_document,$groupe_defaut, $nom_objet,$id_objet) ;
	}	

	$actif= $_POST['actif'] ;
	$id_doc = intval($_POST['id_doc']) ;
	$bannir = $_POST['bannir'] ;

	if($actif && $id_doc){
	spip_query("UPDATE spip_documents SET actif='$actif' WHERE id_document = $id_doc");		
	}
	if($bannir && $id_doc){
	spip_query("UPDATE spip_documents SET banni='$bannir' WHERE id_document = $id_doc");
	}


}


function afficher_liste_mot($id_document){

$query = "SELECT id_mot FROM spip_mots_documents WHERE id_document='$id_document'";
$result = spip_query($query);
if (spip_num_rows($result) == 0){
echo "pas de tag pour le moment";
}else{
	echo"<ul>";
	while ($row = spip_fetch_array($result)) {
		$id_mot=$row['id_mot'];
		$query2 = "SELECT titre FROM spip_mots WHERE id_mot='$id_mot'";
		$result2 = spip_query($query2);
		while ($row2 = spip_fetch_array($result2)) {
		$titre_tag = $row2['titre'];
		echo"<form action=\"$PHP_SELF\" method=\"post\">";
		echo"<input type=\"hidden\"  name=\"retirer_tags\" value=\"oui\">";
		echo"<input type=\"hidden\"  name=\"id_document\" value=\"$id_document\">";
		echo"<input type=\"hidden\"  name=\"liste_tags\" value=\"$titre_tag\">";
		echo "<li>$titre_tag&nbsp;<input type=\"submit\"  name=\"suppr\" value=\"supprimer\"></li>";
		echo "</form>";
		}
	}
	echo"</ul>";
}

}

function form_tag($id_document){
$result = spip_query("SELECT * FROM spip_documents WHERE id_document = '$id_document'") ;
$row_doc = spip_fetch_array($result);
$result2 = spip_query("SELECT * FROM spip_documents_syndic WHERE id_document = '$id_document'") ;
$row_parent = spip_fetch_array($result2);

$query = "SELECT id_mot FROM spip_mots_documents WHERE id_document='$id_document'";
$result3 = spip_query($query);

$tags="";

	
	while ($row = spip_fetch_array($result3)) {
		$id_mot=$row['id_mot'];
		$query2 = "SELECT titre FROM spip_mots WHERE id_mot='$id_mot'";
		$result2 = spip_query($query2);
		while ($row2 = spip_fetch_array($result2)) {
		$titre=$row2['titre'];
		if(ereg(" ",$titre)) $titre="\"$titre\"";
		$tags .= $titre."&nbsp;";
		
		}
	}
	


echo"<div style='float:right;width:200px;font-size:xx-small;margin:10px 0px'>
Vous pouvez ajouter un ou plusieurs tags séparés par un espace. Utiliser des guillemets pour les tags 
composés de plusieurs mots</div>";
echo"<div><form action=\"$PHP_SELF\" method=\"post\" name=\"formulaire\">";
echo"<div style='float:left;width:200px'>";
echo'<input type=\'text\'  name=\'liste_tags\' value=\''.$tags.'\' size=\"50\">';
echo"<input type=\"hidden\"  name=\"modif_tags\" value=\"oui\">";
echo"<input type=\"hidden\"  name=\"id_document\" value=\"$id_document\">";
echo"<input type=\"hidden\"  name=\"id_parent\" value=\"".$row_parent['id_syndic_article']."\">";
echo"<div><input type=\"checkbox\"  name=\"taguer_freres\" value=\"freres\" id=\"freres\">";
echo "<label for='freres'>Taguer les documents frères</label></div>";
echo"<input type=\"submit\"  name=\"valider_tag\" value=\"ok\">";
echo"</div>"; 

echo"</form></div>";

}

function form_gestion_fine($id_document){
$result = spip_query("SELECT * FROM spip_documents WHERE id_document = '$id_document'") ;
$row_doc = spip_fetch_array($result);
$result2 = spip_query("SELECT * FROM spip_documents_syndic WHERE id_document = '$id_document'") ;
$row_parent = spip_fetch_array($result2);

if ($row_doc['banni'] OR $row_doc['actif']){
	
	echo"<h2 style='margin-top:20px'>Gestion fine</h2>";
	
	if($row_doc['banni'] == "non"){
	echo"<form action='$PHP_SELF' method='post'>";
	echo"<input type='hidden' value='$id_document' name='id_doc'>";
	echo"<input type='hidden' value='oui' name='bannir'>";
	echo"<input type='submit'  name='valider' value='Bannir'>";
	echo"</form>";
	}elseif($row_doc['banni'] == "oui"){
	echo"<form action='$PHP_SELF' method='post'>";
	echo"<input type='hidden' value='$id_document' name='id_doc'>";
	echo"<input type='hidden' value='non' name='bannir'>";
	echo"<input type='submit'  name='valider' value='Re-intégrer'>";
	echo"</form>";
	}
	
	if($row_doc['actif'] == "oui"){
	echo"<form action='$PHP_SELF' method='post'>";
	echo"<input type='hidden' value='$id_document' name='id_doc'>";
	echo"<input type='hidden' value='non' name='actif'>";
	echo"<input type='submit'  name='valider' value='Marquer le lien mort'>";
	echo"</form>";
	}elseif($row_doc['actif'] == "non"){
	echo"<form action='$PHP_SELF' method='post'>";
	echo"<input type='hidden' value='$id_document' name='id_doc'>";
	echo"<input type='hidden' value='oui' name='actif'>";
	echo"<input type='submit'  name='valider' value='Marquer le lien actif'>";
	echo"</form>";
	}
	
	}
}

function maj_documents2 ($id_objet, $type) {
	global $_POST;

	if ($id_objet
	AND $id_doc = intval($_POST['id_document'])
	AND $_POST['modif_document'] == 'oui') {

		
		
		// "securite" : verifier que le document est bien lie a l'objet
		if($type=='syndic'){
		//echo "coucou";
		$result_doc = spip_query("SELECT * FROM spip_documents_".$type." WHERE id_document=".$id_doc."
		AND id_syndic_article = $id_objet");
		}else{		
		$result_doc = spip_query("SELECT * FROM spip_documents_".$type."s WHERE id_document=".$id_doc."
		AND id_".$type." = $id_objet");
		}
		
		if (spip_num_rows($result_doc) > 0) {
			$titre_document = addslashes(corriger_caracteres(
				$_POST['titre_document']));
			$descriptif_document = addslashes(corriger_caracteres(
				$_POST['descriptif_document']));
			$query = "UPDATE spip_documents
			SET titre='$titre_document', descriptif='$descriptif_document'";

			// taille du document (cas des embed)
			if ($largeur_document = intval($_POST['largeur_document'])
			AND $hauteur_document = intval($_POST['hauteur_document']))
				$query .= ", largeur='$largeur_document',
					hauteur='$hauteur_document'";

			$query .= " WHERE id_document=".$_POST['id_document'];
			spip_query($query);


			// Date du document (uniquement dans les rubriques)
			if ($_POST['jour_doc']) {
				if ($_POST['annee_doc'] == "0000")
					$_POST['mois_doc'] = "00";
				if ($_POST['mois_doc'] == "00")
					$_POST['jour_doc'] = "00";
				$date = $_POST['annee_doc'].'-'
				.$_POST['mois_doc'].'-'.$_POST['jour_doc'];

				if (preg_match('/^[0-9-]+$/', $date)) {
					spip_query("UPDATE spip_documents
						SET date='$date'
						WHERE id_document=$id_document");

					// Changement de date, ce qui nous oblige a :
					calculer_rubriques();
				}
			}

		}

		// Demander l'indexation du document
		include_spip('inc/indexation');
		marquer_indexer('document', $id_doc);

	}
}




//
// Afficher un document sous forme de ligne depliable
//

function afficher_case_document2($id_document, $image_url, $redirect_url = "", $deplier = false) {
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire;
	global $clean_link;
	global $options;
	global $id_doublons;
	global $spip_lang_left, $spip_lang_right;
	
	$doublons=',$id_document,';
	$options='avancees';


	$flag_deplie = teste_doc_deplie($id_document);

 	$doublons = $id_doublons['documents'].",";

	if (!$redirect_url) $redirect_url = $clean_link->getUrl();

	$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));

	$id_vignette = $document['id_vignette'];
	$id_type = $document['id_type'];
	$titre = $document['titre'];
	$descriptif = $document['descriptif'];
	$url = generer_url_document($id_document);
	$fichier = $document['fichier'];
	$largeur = $document['largeur'];
	$hauteur = $document['hauteur'];
	$taille = $document['taille'];
	$mode = $document['mode'];
	if (!$titre) {
		$titre_fichier = _T('info_sans_titre_2');
		$titre_fichier .= " <small>(".ereg_replace("^[^\/]*\/[^\/]*\/","",$fichier).")</small>";
	}

	$result = spip_query("SELECT * FROM spip_types_documents WHERE id_type=$id_type");
	if ($type = @spip_fetch_array($result))	{
		$type_extension = $type['extension'];
		$type_inclus = $type['inclus'];
		$type_titre = $type['titre'];
	}

	//
	// Afficher un document
	//

	if ($mode == 'document') {
		$titre_cadre = lignes_longues(typo($titre).typo($titre_fichier), 30);
		debut_cadre_enfonce("doc-24.gif", false, "", $titre_cadre);

		echo "<div style='float: $spip_lang_left;'>";
		$block = "document $id_document";
		if ($flag_deplie) echo bouton_block_visible($block);
		else echo bouton_block_invisible($block);
		echo "</div>";


		//
		// Affichage de la vignette
		//
		echo "<div align='center'>\n";
		echo document_et_vignette($document, $url, true); 
		echo "</div>\n";

/*
		// Affichage du raccourci <doc...> correspondant
		if (!ereg(",$id_document,", $doublons)) {
			echo "<div style='padding:2px;'><font size='1' face='arial,helvetica,sans-serif'>";
			if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
				echo "<b>"._T('info_inclusion_vignette')."</b><br />";
			}
			echo "<font color='333333'>"
			. affiche_raccourci_doc('doc', $id_document, 'left')
			. affiche_raccourci_doc('doc', $id_document, 'center')
			. affiche_raccourci_doc('doc', $id_document, 'right')
			. "</font>\n";
			echo "</font></div>";

			if ($options == "avancees" AND ($type_inclus == "embed" OR $type_inclus == "image") AND $largeur > 0 AND $hauteur > 0) {
				echo "<div style='padding:2px;'><font size='1' face='arial,helvetica,sans-serif'>";
				echo "<b>"._T('info_inclusion_directe')."</b></br>";
				echo "<font color='333333'>"
				. affiche_raccourci_doc('emb', $id_document, 'left')
				. affiche_raccourci_doc('emb', $id_document, 'center')
				. affiche_raccourci_doc('emb', $id_document, 'right')
				. "</font>\n";
				echo "</font></div>";
			}
		}*/

		//
		// Edition des champs
		//

		if ($flag_deplie)
			echo debut_block_visible($block);
		else
			echo debut_block_invisible($block);

		if (ereg(",$id_document,", $doublons)) {
			echo "<div style='padding:2px;'><font size='1' face='arial,helvetica,sans-serif'>";
			echo affiche_raccourci_doc('doc', $id_document, '');
			echo "</font></div>";
		}

		echo "<div class='verdana1' style='color: $couleur_foncee; border: 1px solid $couleur_foncee; padding: 5px; margin-top: 3px; text-align: left; background-color: white;'>";
		if (strlen($descriptif) > 0) echo propre($descriptif)."<br />";


		if ($options == "avancees") {
			echo "<div style='color: black;'>";
			if ($type_titre){
				echo "$type_titre";
			} else {
				echo _T('info_document').' '.majuscules($type_extension);
			}

			if ($largeur * $hauteur)
				echo ", "._T('info_largeur_vignette',
					array('largeur_vignette' => $largeur,
					'hauteur_vignette' => $hauteur));

			echo ', '.taille_en_octets($taille);
			echo "</div>";
		}

		$link = new Link($redirect_url);
		$link->addVar('modif_document', 'oui');
		$link->addVar('id_document', $id_document);
		$link->addVar('show_docs', $id_document);
		echo $link->getForm('POST');

		echo "<b>"._T('entree_titre_document')."</b><br />\n";
		echo "<input type='text' name='titre_document' class='formo' value=\"".entites_html($titre)."\" size='40'><br />\n";

		if ($descriptif OR $options == "avancees") {
			echo "<b>"._T('info_description_2')."</b><br />\n";
			echo "<textarea name='descriptif_document' rows='4' class='formo' cols='20' wrap='soft'>";
			echo entites_html($descriptif);
			echo "</textarea>\n";
		}

		if ($options == "avancees")
			//afficher_formulaire_taille($document, $type_inclus);

		echo "<div align='".$GLOBALS['spip_lang_right']."'>";
		echo "<input TYPE='submit' class='fondo' style='font-size:9px;' NAME='Valider' VALUE='"._T('bouton_enregistrer')."'>";
		echo "</div>";
		echo "</form>";

		$link_supp = new Link ($image_url);
		$link_supp->addVar('redirect', $redirect_url);
		$link_supp->addVar('hash', calculer_action_auteur("supp_doc ".$id_document));
		$link_supp->addVar('hash_id_auteur', $connect_id_auteur);
		$link_supp->addVar('doc_supp', $id_document);
		$link_supp->addVar('ancre', 'documents');

		echo "</div>";
		echo fin_block();
		// Fin edition des champs

		echo "<p /><div align='center'>";
		icone_horizontale(_T('icone_supprimer_document'), $link_supp->getUrl(), "doc-24.gif", "supprimer.gif");
		echo "</div>";


		// Bloc edition de la vignette
		if ($options == 'avancees') {
			echo "<div class='verdana1' style='color: $couleur_foncee; border: 1px solid $couleur_foncee; padding: 5px; margin-top: 3px;'>";
			# 'extension', a ajouter dans la base quand on supprimera spip_types_documents
			switch ($id_type) {
				case 1:
					$document['extension'] = "jpg";
					break;
				case 2:
					$document['extension'] = "png";
					break;
				case 3:
					$document['extension'] = "gif";
					break;
			}
			bloc_gerer_vignette($document, $image_url, $redirect_url, 'documents');
			echo "</div>\n";
		}

		fin_cadre_enfonce();
	}
}

function recuperer_info_doc($id_document){
$doc=array();
$result = spip_query("SELECT * FROM spip_documents_syndic WHERE id_document = " . intval($id_document));

if(spip_num_rows($result)>0){
$document=spip_fetch_array($result);
$doc['id_parent'] = $document['id_syndic_article'];
$doc['type_parent'] ="syndic";
$parent = spip_fetch_array(spip_query("SELECT * FROM spip_syndic_articles WHERE id_syndic_article = " . $doc['id_parent']));
//print_r($parent);
$doc['titre_parent'] = $parent['titre'];
$doc['url_parent'] = $parent['url'];
$doc['source_parent'] = $parent['id_syndic'];
$doc['intro_parent'] = $parent['descriptif'];
$source = spip_fetch_array(spip_query("SELECT * FROM spip_syndic WHERE id_syndic = " . $doc['source_parent']));
$doc['source_titre'] = $source['nom_site'];
}else{
$result = spip_query("SELECT * FROM spip_documents_articles WHERE id_document = " . intval($id_document));
if(spip_num_rows($result) > 0){
$document=spip_fetch_array($result);
$doc['id_parent'] = $document['id_article'];
$doc['type_parent'] ="article";
$parent = spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article = " . $doc['id_parent']));
$doc['titre_parent'] = $parent['titre'];
$doc['url_parent'] = "article.php3?id_article=".$parent['id_article'];
$doc['source_parent'] = $parent['id_rubrique'];
$doc['intro_parent'] = $parent['descriptif'];
}

}

return $doc ;

}

function recuperer_id3_doc($id_document){
$result = spip_query("SELECT fichier FROM spip_documents WHERE id_document = " . intval($id_document));

if(spip_num_rows($result)>0){
$document=spip_fetch_array($result);
$fichier = $document['fichier'];
$fichier =ereg_replace(" ","%20",$fichier);

// Copy remote file locally to scan with getID3()
include_spip('inc/getid3/getid3.php');
$getID3 = new getID3;	
$remotefilename = $fichier ;
if ($fp_remote = @fopen($remotefilename, 'rb')) {
    $localtempfilename = tempnam('tmp', 'getID3');
    if ($fp_local = @fopen($localtempfilename, 'wb')) {
        // Do this to copy the entire file:
        //while ($buffer = fread($fp_remote, 16384)) {
        //    fwrite($fp_local, $buffer);
        //}
        
        // Do this to only work on the first 10kB of the file (good enough for most formats)
        $buffer = fread($fp_remote, 10240);
        fwrite($fp_local, $buffer);
        
        fclose($fp_local);
        
        // Scan file - should parse correctly if file is not corrupted
        $ThisFileInfo = $getID3->analyze($localtempfilename);
        // re-scan file more aggressively if file is corrupted somehow and first scan did not correctly identify
        /*if (empty($ThisFileInfo['fileformat']) || ($ThisFileInfo['fileformat'] == 'id3')) {
            $ThisFileInfo = GetAllFileInfo($localtempfilename, strtolower(fileextension($localtempfilename)));
        }*/
        
        // Delete temporary file
        unlink($localtempfilename);
    }
    fclose($fp_remote);
}
	
	if(sizeof($ThisFileInfo)>0){
	
			$id3_title = ($ThisFileInfo['tags']['id3v2']['title']['0']) ? $ThisFileInfo['tags']['id3v2']['title']['0'] : $ThisFileInfo['id3v2']['comments']['title']['0'] ;
			$id3_artist = ($ThisFileInfo['tags']['id3v2']['artist']['0']) ? $ThisFileInfo['tags']['id3v2']['artist']['0'] : $ThisFileInfo['id3v2']['comments']['artist']['0'] ;
			$id3_album  = ($ThisFileInfo['tags']['id3v2']['album']['0']) ? $ThisFileInfo['tags']['id3v2']['album']['0'] : $ThisFileInfo['id3v2']['comments']['album']['0'] ;
			$id3_genre = ($ThisFileInfo['tags']['id3v2']['genre']['0']) ? $ThisFileInfo['tags']['id3v2']['genre']['0'] : $ThisFileInfo['id3v2']['comments']['genre']['0'] ;
			$id3_comment = ($ThisFileInfo['tags']['id3v2']['comment']['0']) ? $ThisFileInfo['tags']['id3v2']['comment']['0'] : $ThisFileInfo['id3v2']['comments']['comment']['0'] ;
			$id3_sample_rate = $ThisFileInfo['audio']['sample_rate'] ;
			$id3_track = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
			$id3_encoded_by = $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
			$id3_totaltracks = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			$id3_tracknum = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			
			echo "<ul>";
		
			echo ($id3_title) ? "<li>Titre : ".$id3_title."</li>" : "" ;
			echo ($id3_artist) ? "<li>Artiste : ".$id3_artist."</li>" : "" ;
			echo ($id3_album) ? "<li>Album : ".$id3_album."</li>" : "" ;
			echo ($id3_genre) ? "<li>Genre : ".$id3_genre."</li>" : "" ;
			echo ($id3_comment) ? "<li>Descriptif : ".$id3_comment."</li>" : "" ;
			echo ($id3_comment) ? "<li>Echantillonage : ".$id3_sample_rate."</li>" : "" ;
			echo ($id3_track) ? "<li>Piste : ".$id3_track."</li>" : "" ;
			echo ($id3_encoded_by) ? "<li>Encodeur : ".$id3_encoded_by."</li>" : "" ;
			echo ($id3_totaltracks) ? "<li>Nombre de pistes : ".$id3_totaltracks."</li>" : "" ;
			echo ($id3_tracknum) ? "<li>Piste : ".$id3_tracknum."</li>" : "" ;
			echo "</ul>";
			
			/*echo "<textarea cols='100' rows='15'>";
			print_r($ThisFileInfo);
			echo"</textarea>";
			*/
		}	
			
	}
	
}


function afficher_formulaire_document_tag($id_document,$groupe_defaut='',
					  $nom_objet='documents',
					  $id_objet='id_document'){

$info = recuperer_info_doc($id_document) ;
$id_parent = $info['id_parent'] ;
$type_parent = $info['type_parent'] ;
					  
maj_documents2 ($id_parent, $type_parent) ;	
maj_tags ( $groupe_defaut,$nom_objet,$id_objet) ;
				  
// affichage
					  
echo "<div>";				  
echo "<div style='float:left;width:50%;margin-right:5%'>";	
echo "<h1><a href='ecouter.php3?id_document=$id_document'>Ecouter</a></h1>";



afficher_case_document2($id_document, '', '', true);
echo "</div>";				  

form_gestion_fine($id_document);

echo "<h2>Informations sur le fichier distant (ID3 Tags)</h2>";
recuperer_id3_doc($id_document);

echo "<h2>Site : <a href='inc_gestion_tags.php?id_syndic=".$info['source_parent']."'>".substr($info['source_titre'],0,25)."</a></h2>";

echo "<h2>Article : <a href='".$info['url_parent']."'>".$info['titre_parent']."</a></h2>";
echo "<p>".$info['intro_parent']."</p>";

$obj=$info['type_parent'] ;
$result = spip_query("SELECT * FROM spip_documents_".$obj." WHERE id_syndic_article = " . $info['id_parent']);
	if(spip_num_rows($result) > 0){
	
	echo "<h2>Documents frères</h2>";
	echo "<ul>";
		while($docu=spip_fetch_array($result)){
		$row_doc = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . $docu['id_document']));
		echo "<li><a href='".$PHP_SELF."?id_document=".$row_doc['id_document']."'>".$row_doc['titre']."&nbsp;</a></li>";
		}
	echo "</ul>";
	}


echo "</div>";



//info complementaires
echo "<div style='clear:both;margin-top:20px'>";				  

//valider les résulats



echo "<h2><a href='inc_gestion_tags.php'>Tags</a></h2>";
form_tag($id_document);
echo "<div style='clear:both'>";
echo "\n";
?>
<script language="JavaScript" type="text/javascript"> 

function emoticon(text) { var txtarea =
document.formulaire.liste_tags; 
text = text + ' '; 
if(txtarea.createTextRange && txtarea.caretPos) { var caretPos =
txtarea.caretPos; caretPos.text =
caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text +
text + ' ' : caretPos.text + text; txtarea.focus(); } else {
txtarea.value += text; txtarea.focus(); } } 

</script>
<?php

$query = "SELECT id_mot, titre FROM spip_mots WHERE type='tags' GROUP BY titre";
$result = spip_query($query);
while ($row = spip_fetch_array($result)) {
	$id_mot = $row['id_mot'];
	$titre_mot = $row['titre'];
	$titre[$id_mot]=$titre_mot;
	$url[$id_mot]="\"javascript:emoticon('$titre_mot')\"";
	/*
	$nb = spip_num_rows(spip_query("SELECT id_document FROM spip_mots_documents
	WHERE id_mot=$id_mot"));
	
	$pop[$id_mot]=$nb;*/
}

	
//$maxpop = max($pop); # Plus grand nombre de documents pour un mot

foreach ($titre as $id => $t) {
       //$score = $pop[$id]/$maxpop; # entre 0 et 1
       
         //$score = ceil(10*$score);
         $s = 10;
         $t = str_replace(' ', '&nbsp;', $t);
         $l = "<span style='font-size:".$s."px'>$t</span>";
         $l = '<a href='.$url[$id].'>'.$l.'</a>';
         echo "$l &nbsp; \n";
       
}
echo "</div>";
echo "</div>";	


}

?>
