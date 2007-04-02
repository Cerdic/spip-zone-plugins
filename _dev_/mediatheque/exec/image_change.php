<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_image_change() {

	global $Submit_photo, $Submit_cancel;
	global $photo_name, $photo_rubrique, $photo_desc, $photo_credit;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	
	// init
	$display = false;
	$writedb = true;
	$id_image = _request('id_image');
	
	
	$id_auteur = $auteur_session['id_auteur'];
	
	$site_root 		= $_SERVER['DOCUMENT_ROOT'];
	$web_root 		= _DIR_RACINE;
	
	$ikono_site_path = "$site_root/drass/IMG";
	$ikono_web_path  = "$web_root" . "IMG";
			
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
	//		put header
	// ---------------------------------------------	
	print display_popup_header("Modifier la rubrique");
		
	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	print "<H3>Modifier une image de la phototh&egrave;que</H3>";
	
	// ---------------------------------------------
	//	CHECK DATA	
	// ---------------------------------------------
	$formcorrect = true;
	$erreur='';
	
	if ($Submit_photo) {
		//
	}
	
	if ($erreur != '') {print "<p>$erreur</p>";}
	
	// ---------------------------------------------
	//	TREATMENT 
	// ---------------------------------------------
	if ($Submit_photo AND $formcorrect) {
		
	
	


		// ---------------------------------------------
		//		update in spip_documents
		// ---------------------------------------------
		$photo_desc = addslashes(stripslashes($photo_desc));
		$photo_name = addslashes(stripslashes($photo_name));
		$date = date("Y-m-d H:i:s");
		$timestamp = date("YmdHis");
		
		$fichier = "IMG/$type_lib/$photo_to_load_name";
		
		
		$qupd = "UPDATE spip_documents 
					SET	
					 titre 		= '$photo_name',
					 date 		= '$date',
					 descriptif = '$photo_desc',
					 maj 		= '$timestamp',
					 id_tek_rub = '$photo_rubrique',
					 photo_credit = '$photo_credit'
				WHERE
					id_document = '$id_image'					
				";
				
	
	if ($writedb) {
		$rupd = mysql_query($qupd);
	}
	
	if ($display) {print "$qupd -> $tupd<br><br>";}
	
	
	
		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>";
		print "<script>window.close();</script>";
	}
	
	
	if ($Submit_cancel) {
			$link_back = "<a href=\"?exec=mediatheque_img_browser\">Retourner &agrave; la phototh&egrave;que</a>";
			print "<p>$link_back</p>";
	}
	
	
	
	
	  
	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------
	
	if ($Submit_photo and $formcorrect) {
		$link_browse = "<a href=\"?exec=mediatheque_img_browser\">Retour &agrave; la phototh&egrave;que</a>";
		print "<p>Mise &agrave; jour effectu&eacute;e. [$link_browse]</p>";
	}
	

	
		// ---------------------------------------------
		//		get data
		// ---------------------------------------------
		$qim = "select * from spip_documents
				where id_document = '$id_image'
				";
				
		$rim = spip_query($qim);
		
		while ($obj = mysql_fetch_object($rim)) {
			$photo_name			= $obj -> titre;
			$fichier			= $obj -> fichier;
			$photo_desc			= $obj -> descriptif;
			$id_tek_rub			= $obj -> id_tek_rub;
			$photo_credit		= $obj -> photo_credit;
			$file_size		= $obj -> taille;
			$width			= $obj -> largeur;
			$height			= $obj -> hauteur;
		}
		
		$max_width = 200; // to adjust if needed ***
		//$width = ($width > $max_width) ? $max_width : $width;
		
		if ($width > $height) {$width_disp = min($max_width, $width);}
		else {$width_disp = min(ceil($width*($max_width/$height)), $width);}
		
		
		print "<form name=\"form\" action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\r\n";
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print "Modifier l'image";
		print "</legend>\r\n";
		
		print "<img src=\"../$fichier\" width=\"$width_disp\">";
				
		print "\r\n\r\n<p>Dans la rubrique<br/><select name=\"photo_rubrique\" size=\"$h_size\">\r\n";
		// forcing root value
		if ($id_tek_rub == 0) {$is_selected = "selected";}
		else {$is_selected = "";}
				
			print "<option $is_selected value=\"0\">";
			print "/Racine ---";
			print "</option>\r";
			
		new_arbo_lib(0, 0, $id_tek_rub);
		print "</select></p>";
					
		print "<p>Nom<br/><input name = \"photo_name\" type=\"text\" value=\"$photo_name\" size=\"45\"></p>\r\n";
		print "<p>Cr&eacute;dit<br/><input name = \"photo_credit\" type=\"text\" value=\"$photo_credit\" size=\"35\"></p>\r\n";
		print "<p>Descriptif<br/><textarea name=\"photo_desc\" cols=\"50\" rows=\"5\">$photo_desc</textarea></p>\r\n";
		
		//$close_link = "<a href=\"javascript:window.close();\" class=\"boutonBOformLarge\">Fermer cette fen&ecirc;tre</a>\r\n";
		
		$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";

		$action1 = "<input type=\"submit\" name=\"Submit_photo\" value=\"Enregistrer\" class=\"boutonBOform\">\r\n";
		
		print "$action2 &nbsp;&nbsp;&nbsp; $action1 ";
		
		print "</fieldset>\r\n";
		print "</form>\r\n";
		
	

	
	  
	print display_popup_footer();
}


?>