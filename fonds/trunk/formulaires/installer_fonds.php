<?php
/*
 * Plugin Recommander a un ami
 * (c) 2006-2010 Fil
 * Distribue sous licence GPL
 *
 */

function formulaires_installer_fonds_charger_dist($id_article){
	$valeurs = array(
		"id_article"=>"$id_article",
		"fond_couleur"=>"",
		"credit_haut"=>"",
		"credit_bas"=>"",
		"remplir_vertical"=>""
	);

	$query = sql_select("fond_couleur, credit_haut, credit_bas, remplir_vertical", "spip_articles", "id_article=$id_article");
	if ($row = sql_fetch($query)) {
		$valeurs["fond_couleur"] = $row["fond_couleur"];
		$valeurs["credit_haut"] = $row["credit_haut"];
		$valeurs["credit_bas"] = $row["credit_bas"];
		$valeurs["remplir_vertical"] = $row["remplir_vertical"];
	}
	
	
	return $valeurs;
}


function formulaires_installer_fonds_verifier_dist($id_article){
	$erreurs = array();

	$repertoire = sous_repertoire(sous_repertoire(_DIR_IMG, "fonds"), "article$id_article");
	if ($_FILES["img_haut"]) {
		$fichier = $_FILES['img_haut']['name'];
		
		
		if (preg_match(",\.jpg$,", $fichier)) {
			$dest = "img_haut$id_article.jpg";
			$fichier = $_FILES['img_haut']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
			
		}
	}
	if ($_POST["supprimer_img_haut"]){
		$dest = "img_haut$id_article.jpg";
		@unlink($repertoire.$dest);
	}


	if ($_FILES["img_fond"]) {
		$fichier = $_FILES['img_fond']['name'];
		if (preg_match(",\.jpg$,", $fichier)) {
			$dest = "img_fond$id_article.jpg";
			$fichier = $_FILES['img_fond']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
			$dest = "img_fond$id_article.svg";
			@unlink($repertoire.$dest);
		}
		else if (preg_match(",\.svg$,", $fichier)) {
			$dest = "img_fond$id_article.svg";
			$fichier = $_FILES['img_fond']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
			$dest = "img_fond$id_article.jpg";
			@unlink($repertoire.$dest);
		}
	}
	if ($_POST["supprimer_img_fond"]){
		$dest = "img_fond$id_article.jpg";
		@unlink($repertoire.$dest);
		$dest = "img_fond$id_article.svg";
		@unlink($repertoire.$dest);
	}
	
	if ($_FILES["fond_haut"]) {
		$fichier = $_FILES['fond_haut']['name'];
		if (preg_match(",\.jpg$,", $fichier)) {
			$dest = "fond_haut$id_article.jpg";
			$fichier = $_FILES['fond_haut']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
		}
	}
	if ($_POST["supprimer_fond_haut"]){
		$dest = "fond_haut$id_article.jpg";
		@unlink($repertoire.$dest);
	}
	

	if ($_FILES["img_bas"]) {
		$fichier = $_FILES['img_bas']['name'];
		if (preg_match(",\.jpg$,", $fichier)) {
			$dest = "img_bas$id_article.jpg";
			$fichier = $_FILES['img_bas']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
		}
	}
	if ($_POST["supprimer_img_bas"]){
		$dest = "img_bas$id_article.jpg";
		@unlink($repertoire.$dest);
	}
	
	if ($_FILES["fond_bas"]) {
		$fichier = $_FILES['fond_bas']['name'];
		if (preg_match(",\.jpg$,", $fichier)) {
			$dest = "fond_bas$id_article.jpg";
			$fichier = $_FILES['fond_bas']['tmp_name'];
			move_uploaded_file($fichier, $repertoire.$dest);
		}
	}
	if ($_POST["supprimer_fond_bas"]){
		$dest = "fond_bas$id_article.jpg";
		@unlink($repertoire.$dest);
	}
	
	if ($_POST["pipette_fond"]) {
		// Pipette:
		// prélever la couleur dans images présentes successives
		$dest = $repertoire."img_haut$id_article.jpg";
		if (file_exists($dest)) {
			include_spip('filtres/couleurs');
			$couleur = couleur_extraire($dest, 10, 19);
			$_POST["fond_couleur"] = $couleur;
		}
		$dest = $repertoire."img_bas$id_article.jpg";
		if (file_exists($dest)) {
			include_spip('filtres/couleurs');
			$couleur = couleur_extraire($dest, 10, 0);
			$_POST["fond_couleur"] = $couleur;
		}
		$dest = $repertoire."fond_haut$id_article.jpg";
		if (file_exists($dest)) {
			include_spip('filtres/couleurs');
			$couleur = couleur_extraire($dest, 10, 19);
			
			
			$_POST["fond_couleur"] = $couleur;
		}

		$dest = $repertoire."fond_bas$id_article.jpg";
		if (file_exists($dest)) {
			include_spip('filtres/couleurs');
			$couleur = couleur_extraire($dest, 10, 0);
			$_POST["fond_couleur"] = $couleur;
		}
		$dest = $repertoire."img_fond$id_article.jpg";
		if (file_exists($dest)) {
			include_spip('filtres/couleurs');
			$couleur = couleur_extraire($dest, 10, 10);
			$_POST["fond_couleur"] = $couleur;
		}
	}


	return $erreurs;
}



function formulaires_installer_fonds_traiter_dist($id_article){

	$fond_couleur = _request("fond_couleur");
	$credit_haut = _request("credit_haut");
	$credit_bas = _request("credit_bas");
	$remplir_vertical = _request("remplir_vertical");

	if (strlen($fond_couleur) == 3) $fond_couleur = substr($fond_couleur,0,1).substr($fond_couleur,0,1).substr($fond_couleur,1,1).substr($fond_couleur,1,1).substr($fond_couleur,2,1).substr($fond_couleur,2,1);

	
	sql_updateq("spip_articles",
		array(
			"fond_couleur" => $fond_couleur,
			"credit_haut" => $credit_haut,
			"credit_bas" => $credit_bas,
			"remplir_vertical" => $remplir_vertical
		),
		"id_article=$id_article"
	);


/*
	$long_form = _request("long_form");

	if (!autoriser('modifier', 'rubrique', $id_rubrique)) return false;


	sql_updateq("spip_rubriques",
		array(
			"long_form" => $long_form
		),
		"id_rubrique=$id_rubrique"
	);
	sql_updateq("spip_articles",
		array(
			"long_form" => $long_form
		),
		"id_rubrique=$id_rubrique"
	);
*/	
}
