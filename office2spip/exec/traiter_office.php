<?php
function _remplacer_images_texte($matches) {
	$id_article = $GLOBALS["new_article"];

	// Recuperer l'alignement	
	$complet = $matches[1];
	preg_match(",ALIGN=([A-Z]*),", $complet, $preg);
	$align = strtolower($preg[1]);
	if ($align == "bottom") $align= "center";

	// Recuperer le nom du fichier
	$fichier = $matches[2];
	
	// Recuperer la terminaison
	preg_match(",\.([a-z]+)$,", $fichier, $preg);
	$terminaison = $preg[1];
	
	$source = sous_repertoire(_DIR_TMP, 'upload_office').$fichier;
	$dest = sous_repertoire(_DIR_IMG, $terminaison).$fichier;
	$url_fichier = "$terminaison/$fichier";
	
	@copy($source, $dest);
	
	include_spip("inc/filtres");
	$largeur = largeur($dest);
	$hauteur = hauteur($dest);
	$taille = filesize($dest);
	
	$id_document = sql_insertq("spip_documents", array(
		"extension" => $terminaison,
		"date" => "NOW()",
		"fichier" => $url_fichier,
		"mode" => "image",
		"distant" => "non",
		"maj" => "NOW()",
		"largeur" => $largeur,
		"hauteur" => $hauteur,
		"taille" => $taille
	));
	if ($id_document > 0) {
		sql_insertq("spip_documents_liens", array(
			"id_document" => $id_document,
			"id_objet" => $id_article,
			"objet" => "article",
			"vu" => "non"
		));
		
		return "<img$id_document|$align>";
	}
}


function exec_traiter_office () {
	$id_rubrique = _request("id_rubrique");
	$id_auteur = $GLOBALS["connect_id_auteur"];

	$flag_editable = autoriser('publierdans','rubrique',$id_rubrique);
	if ($flag_editable) {
			
		$nom = $_FILES['fichier']['name'];
		preg_match(",\.([a-zA-Z]*)$,", $nom, $reg);
		$terminaison = $reg[1];
		$nom = preg_replace(",\.[a-zA-Z]*$,", "", $nom);
		$nom_titre = $nom;
		
		include_spip("inc/filtres");
		
		
		$nom = str_replace("'", "", $nom);
		$nom = str_replace("\\", "", $nom);
		$nom = str_replace("&", "", $nom);
		$nom = supprimer_tags(supprimer_numero(extraire_multi($nom)));
		$nom = corriger_caracteres($nom);
	
	
		$nom = translitteration($nom);
		$nom = preg_replace(",[[:blank:]]+,","_", $nom);
		
		$nom_dest = sous_repertoire(_DIR_TMP, 'upload_office').$nom.".".$terminaison;
		$nom_html = sous_repertoire(_DIR_TMP, 'upload_office').$nom.".html";
		
		$i = 0;
		while (file_exists($nom_dest)) {
			$i++;
			$nom_dest = sous_repertoire(_DIR_TMP, 'upload_office').$nom."-$i.".$terminaison;
			$nom_html = sous_repertoire(_DIR_TMP, 'upload_office').$nom."-$i.html";
		}
		
		@move_uploaded_file($_FILES['fichier']['tmp_name'], $nom_dest);	
		$erreur = @exec("unoconv --format=html $nom_dest");
		
		
		if (file_exists($nom_html)) {
			$resultat = join(file($nom_html), "");

			if (preg_match(",<title[^\>]*>(.*)<\/title>,Uims", $resultat, $preg)) {
				if (strlen(trim($preg[1])) > 0) {	
					$titre = trim($preg[1]);
				} else {
					$titre = "$nom_titre";
				}
			}
			
			if (preg_match(",<body[^\>]*>(.*)<\/body>,ims", $resultat, $preg)) {
				include_spip("inc/fonctionsale");
				$texte = sale($preg[1]);
				
				include_spip("base/abstract_sql");
				$id_article = sql_insertq("spip_articles", array(
					"id_rubrique" => $id_rubrique,
					"titre" => "$titre",
					"texte" => "$texte",
					"date" => "NOW()",
					"statut" => "prepa"
				));
				if ($id_article > 0) {
					sql_insertq("spip_auteurs_articles", array(
						"id_auteur" => $id_auteur,
						"id_article" => $id_article
					));
					
					$GLOBALS["new_article"] = $id_article;
					
					if ($texte = preg_replace_callback(",(<IMG SRC=\"([^\"]+)\"[^>]*>),i", "_remplacer_images_texte", $texte) ) {
						sql_updateq("spip_articles", array(
							"texte" => $texte
						), "id_article=$id_article");
					}
					
					
					header("location:index.php?exec=articles&id_article=$id_article");
				} else {
					header("location:index.php?exec=naviguer&id_rubrique=$id_rubrique");
				}
			} else {
				header("location:index.php?exec=naviguer&id_rubrique=$id_rubrique");
			}
		} else {
			header("location:index.php?exec=naviguer&id_rubrique=$id_rubrique");
		}

		
	} else {
		echo "Faut pas pousser.";
	}

}

?>