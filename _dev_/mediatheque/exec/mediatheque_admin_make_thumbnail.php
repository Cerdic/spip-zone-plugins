<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_mediatheque_admin_make_thumbnail() {

	global $Submit_confirm, $Submit_cancel; $thumb_size;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	
	// init
	$user_display = true;
	$display = false;
	$writedb = false;
	$imgrub_id = _request('id');
	
	
	$id_auteur = $auteur_session['id_auteur'];
	
	$site_root 		= $_SERVER['DOCUMENT_ROOT'];
	$web_root 		= _DIR_RACINE;
	
	// check dir img (maj or min)
	$dir_img = ereg_replace('\.|/', '', _DIR_IMG);
	
	$ikono_site_path = "$site_root/drass/$dir_img";
	$ikono_web_path  = "$web_root/$dir_img";
			
	// general spip includes
		include_spip('inc/presentation');
		include_spip('inc/article_select');
		include_spip('inc/rubriques');
		include_spip('inc/actions');
		include_spip('inc/documents');
		include_spip('inc/barre');
		include_spip('inc/logos');
	
	// specific plugin includes
		include_spip('inc/mediatheque_tools');
	
	
		
	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	print "<H3>Vignette de la phototek</H3>";
	
	
	if ($Submit_confirm) {
	
		$thumb_dir = "$ikono_site_path/cache-mediatheque-vignette";
		if (!file_exists($thumb_dir)) {mkdir($thumb_dir);}
	
		// ---------------------------------------------
		//	SCAN TABLE DOCUMENTS
		//	
		// ---------------------------------------------
		$query = "select * from spip_documents
				where (id_type= 1 or id_type = 2 or id_type = 3)
				order by id_document
				";
		$result = spip_query($query);
		
		while ($obj = mysql_fetch_object($result)) {
			$fichier 		= $obj ->fichier;
			$titre 			= $obj ->titre;
			$url_fichier 	= $obj ->url_fichier;
			$id_document	= $obj ->id_document;
			$id_vignette	= $obj ->id_vignette;
			$file_size		= $obj -> taille;
			$width			= $obj -> largeur;
			$height			= $obj -> hauteur;
			
			
			// prepare thumb file name
			list($img_dir, $img_type, $img_name) = explode('/', $fichier);
			
			$old_img_name = "$ikono_site_path/$img_type/$img_name";
			
			//print "$fichier - $old_img_name<br/><br/>";
			
			
			
			// new name
			$new_thumb_name = "$thumb_dir/vgn_$img_name";
			
		 print "$old_img_name => $new_thumb_name<br/><br/>";
			
			if (file_exists($old_img_name)) {
				print "WORK on $old_file_name<br/><br/>";
				create_thumbnail($old_img_name, $new_thumb_name, 100, 100, $img_type);
			}
			else {
				print "L'image <em>$fichier</em> (No $id_document) n'a pas ete trouve dans le serveur<br/>"; 
			
			}
		
			// ---------------------------------------------
			//		put thumbnail in spip_document table
			// ---------------------------------------------
			if ($id_vignette == 0) {
				$qadd = "INSERT INTO spip_documents (
					 id_type,	
					 titre,
					 date,
					 descriptif,
					 fichier,
					 taille,
					 largeur,
					 hauteur,
					 mode,
					 distant,
					 idx,
					 maj,
					 id_tek_rub,
					 tek_type,
					 id_owner,
					 photo_credit
					)
					VALUES (
					'$img_type',
					'$photo_name',
					'$date',
					'$photo_desc',
					'$fichier',
					'$photo_to_load_size',
					'$largeur',
					'$hauteur',
					'vignette',
					'non',
					'oui',
					'$timestamp',
					'$photo_rubrique',
					'img',
					'$id_auteur',
					'$photo_credit'
				)";
				
	
	if ($writedb) {
		$radd = mysql_query($qadd);
	}
	
	if ($display) {print "$qadd -> $tadd<br><br>";}
			
			
			}
			else {
			
			}
		
		
		} // end scan spip_documents
		
		
		
		
		
		// ---------------------------------------------
		//	SCAN ALL SPIP RUBRIQUES AND DUPLICATE IN IMGRUB & DOCRUB 
		// ---------------------------------------------
		if ($user_display) {print "<p><strong>Copie des rubriques</strong><br/>";}
		
		
		
		
		
		// ---------------------------------------------
		//		SCAN SPIP DOCS AND DISPATCH
		// ---------------------------------------------
		if ($user_display) {print "</p><p><strong>R&eacute;partition des images et documents</strong><br/>";}
		
		
		
		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		//print "<script>opener.location.reload(true);</script>"; 
		//print "<script>window.close();</script>";
		
		$link_browse = "<a href=\"?exec=mediatheque_admin_start\">Retour &agrave; la phototh&egrave;que</a>";
		print "<p>Initialisation termin&eacute;e. [$link_browse]</p>";
	}
	
	
	
	
	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------
			
	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	if (!$Submit_confirm) {	
		print "<form name=\"form\" action=\"\" method=\"POST\">\r\n";
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print "Confirmer la (re)génération des vignettes des images en stock";
		print "</legend>\r\n";
		
		print "<p>Taille (entre 50 et 300)<br/>";
		print "<input name = \"thumb_size\" type=\"text\" value=\"$thumb_size\" size=\"5\"> pixels</p>\r\n";
		
		$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";

		$action1 = "<input type=\"submit\" name=\"Submit_confirm\" value=\"Confirmer\" class=\"boutonBOform\">\r\n";
		
		print "<br>$action2 &nbsp;&nbsp;&nbsp; $action1 ";
		
		print "</fieldset>\r\n";
		print "</form>\r\n";
		
	}
	
	
	

	
		
	  
	//  fin_page();
}


?>