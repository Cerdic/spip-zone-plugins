<?php

function _remplacer_images_texte_liees($matches) {
	$id_article = $GLOBALS["new_article"];
	// Recuperer l'alignement	
	$complet = $matches[0];

	
	preg_match(",align=[\'\"]?([A-Z]*)[\'\"]?,", $complet, $preg);
	$align = strtolower($preg[1]);
	if ($align == "bottom") $align= "center";
	if (!$align) $align = "center";

	// Recuperer le nom du fichier
	$fichier = $matches[3];
	
	if (preg_match(",^https?\:\/\/,", $fichier)) {
		$fichier = copie_locale($fichier);
		// Recuperer la terminaison
		preg_match(",\.([a-z]+)$,", $fichier, $preg);
		$terminaison = $preg[1];
		$url_fichier = preg_replace(",^IMG/,","", $fichier);
		
		$dest = _DIR_IMG.$url_fichier;
		
		
	} else {
		// Recuperer la terminaison
		preg_match(",\.([a-z]+)$,", $fichier, $preg);
		$terminaison = $preg[1];
		
		$source = sous_repertoire(_DIR_TMP, 'upload_office').$fichier;
		$dest = sous_repertoire(_DIR_IMG, $terminaison).$fichier;
		$url_fichier = "$terminaison/$fichier";
		
		@copy($source, $dest);
	}
	
	if ($taille = @filesize($dest)) {
		include_spip("inc/filtres");
		$largeur = largeur($dest);
		$hauteur = hauteur($dest);
	
		$id_document = sql_insertq("spip_documents", array(
			"extension" => $terminaison,
			"date" => "NOW()",
			"fichier" => $url_fichier,
			"mode" => "document",
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
			return "<doc$id_document|$align>";
		}
	}
}

function _remplacer_images_texte($matches) {
	$id_article = $GLOBALS["new_article"];

	// Recuperer l'alignement	
	$complet = $matches[1];

	
	preg_match(",align=[\'\"]?([A-Z]*)[\'\"]?,", $complet, $preg);
	$align = strtolower($preg[1]);
	if ($align == "bottom") $align= "center";
	if (!$align) $align = "center";

	// Recuperer le nom du fichier
	$fichier = $matches[2];
	
	if (preg_match(",^https?\:\/\/,", $fichier)) {
		$fichier = copie_locale($fichier);
		// Recuperer la terminaison
		preg_match(",\.([a-z]+)$,", $fichier, $preg);
		$terminaison = $preg[1];
		$url_fichier = preg_replace(",^IMG/,","", $fichier);
		
		$dest = _DIR_IMG.$url_fichier;
		
		
	} else {
		// Recuperer la terminaison
		preg_match(",\.([a-z]+)$,", $fichier, $preg);
		$terminaison = $preg[1];
		
		$source = sous_repertoire(_DIR_TMP, 'upload_office').$fichier;
		$dest = sous_repertoire(_DIR_IMG, $terminaison).$fichier;
		$url_fichier = "$terminaison/$fichier";
		
		@copy($source, $dest);
	}
	
	if ($taille = @filesize($dest)) {
		include_spip("inc/filtres");
		$largeur = largeur($dest);
		$hauteur = hauteur($dest);

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
			
			return "<doc$id_document|$align>";
		}
	}
}


function exec_traiter_office () {
	$id_rubrique = _request("id_rubrique");
	$id_auteur = $GLOBALS["connect_id_auteur"];

	$flag_editable = autoriser('publierdans','rubrique',$id_rubrique);
	if ($flag_editable) {
		
		# demander un PDF
		$pdf = _request("pdf");

		# joindre l'original
		$original = _request("original");	

		$distant = _request("distant");
		$GLOBALS["distant"] = $distant; 
		
		
		if (strlen($distant) > 0) {
			include_spip("inc/distant");
			$fichier = copie_locale($distant);
			$fichier = _DIR_IMG.preg_replace(",^IMG/,","", $fichier);

			// Si fichier HTML, detecter le charset de la page et convertir si necessaire
			if (preg_match(",\.html$,",$fichier)) {

				// Trouver le chemin de la page et passer les URL en URL absolues.
				$path_parts = pathinfo($distant);
				$racine = $path_parts["dirname"];
				$texte_source = join(file($fichier), "");
				if (preg_match(",<base href=[\'\"]([^\'\"]*)[\'\"],", $texte_source, $preg)) {
					$racine = $preg[1];
				}
				include_spip("inc/filtres_mini");
				$texte_source = liens_absolus($texte_source, $racine);
				
				
	
				if (preg_match(",<meta [^>]*charset=\"?([a-zA-Z0-9\-]*),", $texte_source, $preg )) {
					$charset = strtolower($preg[1]);
					
					if ($charset != "utf-8") {
						$texte_source = unicode_to_utf_8(charset2unicode($texte_source, $charset));
						$texte_source = preg_replace(",<meta [^>]*charset=([a-zA-Z0-9\-]*)[^>]*>,", "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />", $texte_source);
					}
				}
				
				ecrire_fichier($fichier, $texte_source);
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
		
		if ($terminaison == "html") {
			$nom_html = $nom_dest;
		}
		else if ($terminaison == "pdf") {
			$doc = basename($nom_dest);
			$dir = dirname($nom_dest);
			$html = preg_replace(',\.pdf$,', '.html', $doc);
			$pwd = getcwd();
			chdir($dir);
			exec("pdftohtml -noframes -nodrm $doc $html");
			chdir($pwd);
		}
		else {
		
			exec("soffice");
			exec("unoconv --format=html $nom_dest", $retour, $err);
		}
		
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
			
				$texte = $preg[1];
			
				if ($GLOBALS["distant"]) {
					include_spip("inc/Readability");
					$r = new Readability($resultat);
					$r->init();
					$texte = $r->articleContent->innerHTML;
				}
			
				include_spip("inc/fonctionsale");
				$texte = sale($texte);
				
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
					

					if (_request('ignorer_images') == 'oui') {
						$texte = preg_replace(
						",\[(<img [^>]*src=[\"\']([^\"\']+)[\"\'][^>]*>)\-\>([^\]]*\.(jpg|gif|png))\],i", '<!-- image \2 -->',
						$texte);
						$texte = preg_replace(",(<img [^>]*src=[\"\']([^\"\']+)[\"\'][^>]*>),i", '<!-- image \2 -->', $texte);
						sql_updateq("spip_articles", array(
							"texte" => $texte
						), "id_article=$id_article");
					}

					// Traiter les vignettes clicables menant à des images
					if ($texte = preg_replace_callback(",\[(<img [^>]*src=[\"\']([^\"\']+)[\"\'][^>]*>)\-\>([^\]]*\.(jpg|gif|png))\],i", "_remplacer_images_texte_liees", $texte) ) {
						sql_updateq("spip_articles", array(
							"texte" => $texte
						), "id_article=$id_article");
					}
					
					// Traiter les images insérées dans le texte
					if ($texte = preg_replace_callback(",(<img [^>]*src=[\"\']([^\"\']+)[\"\'][^>]*>),i", "_remplacer_images_texte", $texte) ) {
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
							$dest = sous_repertoire(_DIR_IMG, $terminaison).$nom_original;
							copy($nom_dest, $dest);
							$taille = filesize($dest);

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