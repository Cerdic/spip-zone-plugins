<?php

include_spip("inc/assoc_static_type");

$a = new assoc_static_type();
//print_r($_POST);

if ($_POST["choix"]=="creer") creer_assoc($a);
if ($_POST["choix"]=="titre") modif_titre_assoc();
if ($_POST["choix"]=="texte") modif_texte_assoc();
if ($_POST["choix"]=="supprimer") supprimer_assoc();


function creer_assoc($a){

	$tab = $a->type();
	$id = $_POST["id"];
	$id_lien = $_POST["id_lien"];
	$type_id = $tab[$_POST["type_id"]];
	$type_lien = $tab[$_POST["type_lien"]];
	$titre = addslashes($_POST["titre"]);
	$texte = addslashes($_POST["texte"]);
	$type = $_POST["type_lien"];
	
	
	
	$sql = "INSERT INTO `association` ( `keys` , `id` , `id_lien` , `type_id` , `type_lien` , `titre` , `descriptif` ,
			 `obj_option` , `type` ) VALUES (NULL , '$id' , '$id_lien' , '$type_id' , '$type_lien' , '$titre' , '$texte' ,'' , '$type')";
	$res = spip_query($sql);
	
	
	/*
	
	$obj = spip_insert_id();
	
	$titre = $_POST["titre"];
	$texte = $_POST["texte"];
	$retour = "<p class='titre-lien-assoc-admin' id='lien$obj' onclick='lien$obj.deplier()'>
							<img src='../plugins/assoc/img/$type"."_mini.png' class='align-middle' />&nbsp;&nbsp;
							<span class='titre_aff'>$titre</span>
						</p>";
	$modif ="<div class='display-none contour' id='modif$obj'>
					<p ondblclick='lien$obj.titre()' class='le_titre'>$titre 
						<img src='../plugins/assoc/img/crayon.png' class='align-middle'>
						<input type='text' class='invisible letitre' />
					</p>
					<p ondblclick='lien$obj.texte()' class='le_texte'>$texte
						<img src='../plugins/assoc/img/crayon.png' class='align-middle'>
						<textarea rows='3' cols='15' class='invisible letexte' ></textarea>
					</p>
					<input type='button' value='Supprimer' onclick='lien$obj.supprimer()' />
				</div>
				
				";
			
	$script ="<script>lien$obj = new obj_assoc($obj); </script>";
	echo $retour.$modif.$script;

	*/
	
	$tab = array("type_id"=>1 ,"id" => $id , "type_lien"=>4  );
	$element = recuperer_fond("fonds/element_associe_article",$tab);
	echo $element;
}

function modif_titre_assoc(){
	$titre = addslashes($_POST["titre"]);
	$id = $_POST["id"];
	$sql="UPDATE `association` SET `titre` = '$titre' WHERE `association`.`keys` =$id ";
	$res = spip_query($sql);
}

function modif_texte_assoc(){
	$texte = addslashes($_POST["texte"]);
	$id = $_POST["id"];
	$sql="UPDATE `association` SET `descriptif` = '$texte' WHERE `association`.`keys` =$id ";
	$res = spip_query($sql);
}


function supprimer_assoc(){
	$id = $_POST["id"];
	$sql="DELETE FROM `association` WHERE `association`.`keys` = $id";
	$res = spip_query($sql);
	echo $sql;
}









?>