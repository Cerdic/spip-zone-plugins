<?php
function _remplacer_images_texte($matches) {
	$id_article = $GLOBALS["new_article"];
	$distant = $GLOBALS["distant"];
	if ($distant) return "";

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
			
		$pdf = _request("pdf");	
		$original = _request("original");	
		$distant = _request("distant");
		$GLOBALS["distant"] = $distant; 
		if (strlen($distant) > 0) {
			include_spip("inc/distant");
			$fichier = copie_locale($distant);
			$fichier = _DIR_IMG.preg_replace(",^IMG/,","", $fichier);

			$texte_source = join(file($fichier), "");
			
			// Detecter le charset de la page et convertir si necessaire
			if (preg_match(",<meta [^>]*charset=([a-zA-Z0-9\-]*),", $texte_source, $preg )) {
				$charset = strtolower($preg[1]);
				
				if ($charset != "utf-8") {
					$texte_source = unicode_to_utf_8(charset2unicode($texte_source, $charset));
					$texte_source = preg_replace(",<meta [^>]*charset=([a-zA-Z0-9\-]*)[^>]*>,", "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />", $texte_source);
					ecrire_fichier($fichier, $texte_source);
				}
			}



			if (preg_match(",\/([^\/]+)$,", $fichier, $preg)) {
				include_spip("inc/filtres");
		
				$nom = $preg[1];
				$nom_dest = sous_repertoire(_DIR_TMP, 'upload_office').$nom;
				copy($fichier, $nom_dest);		
				preg_match(",\.([a-zA-Z]*)$,", $nom, $reg);
				$terminaison = $reg[1];
				$nom = preg_replace(",\.[a-zA-Z]*$,", "", $nom);
				$nom_titre = $nom;
				$nom_html = sous_repertoire(_DIR_TMP, 'upload_office').$nom.".html";
				$nom_pdf = sous_repertoire(_DIR_TMP, 'upload_office').$nom.".pdf";
				$nom_original = $nom.".$terminaison";
			}
		} else {
			$GLOBALS["distant"] = false;
		
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
			$nom_pdf = sous_repertoire(_DIR_TMP, 'upload_office').$nom.".pdf";
			$nom_original = $nom.".$terminaison";
			
			$i = 0;
			while (file_exists($nom_dest)) {
				$i++;
				$nom_dest = sous_repertoire(_DIR_TMP, 'upload_office').$nom."-$i.".$terminaison;
				$nom_html = sous_repertoire(_DIR_TMP, 'upload_office').$nom."-$i.html";
				$nom_pdf = sous_repertoire(_DIR_TMP, 'upload_office').$nom."-$i.pdf";
				$nom_original = $nom."-i.$terminaison";
			}
			
			@move_uploaded_file($_FILES['fichier']['tmp_name'], $nom_dest);	

		}
		
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
					
					if ($pdf) {
						$erreur = @exec("unoconv --format=pdf $nom_dest");
						
						if (file_exists($nom_pdf)) {
							$dest_pdf = sous_repertoire(_DIR_IMG, 'pdf').$nom."-$i.pdf";
							copy($nom_pdf, $dest_pdf);
							$taille = filesize($dest_pdf);
							$id_document = sql_insertq("spip_documents", array(
								"titre" => "Le document au format PDF",
								"extension" => "pdf",
								"date" => "NOW()",
								"fichier" => "pdf/$nom-$i.pdf",
								"mode" => "document",
								"distant" => "non",
								"maj" => "NOW()",
								"taille" => $taille
							));
							if ($id_document > 0) {
								sql_insertq("spip_documents_liens", array(
									"id_document" => $id_document,
									"id_objet" => $id_article,
									"objet" => "article",
									"vu" => "non"
								));
							}
						}

					}
					if ($original) {
						// Verifier que c'est un format accepte
						include_spip("base/typedoc");
						if ($GLOBALS["tables_mime"]["$terminaison"]) {
							copy($nom_dest, sous_repertoire(_DIR_IMG, $terminaison).$nom_original);
							$taille = filesize($nom_original);
							
							
							$id_document = sql_insertq("spip_documents", array(
								"titre" => "Le document ".$GLOBALS["tables_documents"]["$terminaison"],
								"extension" => "$terminaison",
								"date" => "NOW()",
								"fichier" => "$terminaison/$nom_original",
								"mode" => "document",
								"distant" => "non",
								"maj" => "NOW()",
								"taille" => $taille
							));
							if ($id_document > 0) {
								sql_insertq("spip_documents_liens", array(
									"id_document" => $id_document,
									"id_objet" => $id_article,
									"objet" => "article",
									"vu" => "non"
								));
							}
						}
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