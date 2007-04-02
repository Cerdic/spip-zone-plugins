<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_image_add() {

	global $Submit_photo, $Submit_cancel, $photo_to_load, $photo_to_load_name, $photo_to_load_size;
	global $photo_name, $photo_rubrique, $photo_desc;
	global $connect_statut; // string
	global $auteur_session, $prefs; // assoc array

	// *****************
	$max_image_size = 204800;
	// *****************


	// init
	$display = false;
	$writedb = true;


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
	print display_popup_header("Choisissez votre image");


	// ---------------------------------------------
	//		Build pathes
	// ---------------------------------------------
	if (!$Submit_photo) {$photo_rubrique = _request('id');}
	$id_auteur 			= $auteur_session['id_auteur'];

	$site_root 			= $_SERVER['DOCUMENT_ROOT'];
	$web_root 			= _DIR_RACINE;
	$dir_img 			= get_dir_img();
	

	$ikono_site_path 	= "$site_root/votresite/$dir_img"; // real version for use
	$ikono_web_path  	= "$web_root" . "$dir_img";
	
	if ($display) {
		print "ikono_site_path = $ikono_site_path<br/>";
		print "ikono_web_path = $ikono_web_path<br/><br/>";
	}

	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	print "<H3>" . _T('mediatheque:ima_form_title') . "</H3>";
	
	if ($display) {
		print "<b>Check in #57</b><br/><br/>";
		if ($Submit_photo) {print "submit photo<br/><br/>";}
		else {print "NOT submit photo<br/><br/>";}
	
		if (is_uploaded_file($photo_to_load)) {print "uploaded file<br/><br/>";}
		else {print "NOT uploaded file<br/><br/>";}
	
	}

	// ---------------------------------------------
	//	CHECK DATA
	// ---------------------------------------------
	$formcorrect = true;
	$erreur='';
	$accepted_file = array('image/gif',	'image/jpeg', 'image/png');

	if ($Submit_photo) {
	
		// ---------------------------------------------
		//	1. check if any	
		// ---------------------------------------------
		if ($photo_to_load=='') {
			$formcorrect = false;
			$erreur .= "<p class=\"erreur\"><br>" . _T('mediatheque:ima_err_empty') . "</p>";
		}
		else {
			if (is_uploaded_file($photo_to_load)) { // double check
				if ($display) {print "<b>Start image check for $photo_to_load<br/><br/></b>";}
				
				// ---------------------------------------------
				//		2. if any, check right Ko size
				// ---------------------------------------------
				if ($photo_to_load_size > $max_image_size) {
					$size_ko = $photo_to_load_size / 1024;
					$size_ko = round($size_ko, 1);
					$formcorrect = false ;
					$erreur .= _T('mediatheque:ima_err_too_big');
				}
				else {
					if ($display) {print ".....file size OK<br/>";}
				}				
				
				// ---------------------------------------------
				//		3. if any, check if image suffix
				// ---------------------------------------------
				$size = @getimagesize($photo_to_load);
				$largeur 	= $size[0];
				$hauteur 	= $size[1];
				$type 		= $size[2];
				if ($display) {print ".....type = $type => ";}

				// type codes : 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP,
			
				if ($type > 3){
					$formcorrect= false ;
					$erreur .= "<br><strong>" . _T('mediatheque:ima_err_not_image') . "</strong>";
				}
				else {
					if ($display) {print " image file type OK<br/>";}
				}
				
			} // end if file loaded
		} // end else if no file
		
		
		// ---------------------------------------------
		//		4. if any and all passe so far, check double
		// ---------------------------------------------
		if ($formcorrect) {
		
			// ---------------------------------------------
			//  decode file type to build path
			// ---------------------------------------------
			switch ($type) {
				case 1:$type_lib = 'gif';break;
				case 2:$type_lib = 'jpg';break;
				case 3:$type_lib = 'png';break;
			}
			$ikono_site_path_typed = "$ikono_site_path/$type_lib";
			
			// ---------------------------------------------
			// 4.1 check if dir exists in IMG, and create if not
			// ---------------------------------------------
			if (!is_dir($ikono_site_path_typed)) {
				if ($display) {print "make dir $ikono_site_path_typed<br/><br/>";}
				mkdir($ikono_site_path_typed, 0777);
			}
			
			// ---------------------------------------------
			// 4.2 check if this dir is writable
			// ---------------------------------------------
			if (!is_writable($ikono_site_path_typed)) {
				$erreur .= "<p class=\"erreur\">/$dir_img/$type_lib" . _T('mediatheque:ima_err_not_writable') . "</p>";
				$formcorrect = false;
			}
			
			// ---------------------------------------------
			//	4.3 check if exists a previous image...
			// ---------------------------------------------
			if (file_exists("$ikono_site_path_typed/$photo_to_load_name")) {
				$formcorrect = false;
				
				$previous_size 		= getimagesize("$ikono_site_path_typed/$photo_to_load_name");
				$previous_largeur 	= $previous_size[0];
				$previous_hauteur 	= $previous_size[1];
				$previous_type 		= $previous_size[2];

				if ($display) {
					print "$previous_largeur / $largeur<br/>
							$previous_hauteur / $hauteur<br/>
							$previous_type / $type<br/>";
				}

				if ($previous_largeur == $largeur
				and $previous_hauteur == $hauteur
				and $previous_type == $type) {
					$erreur .= _T('mediatheque:ima_err_exact_double');
				}
				else {
					$erreur .= _T('mediatheque:ima_err_double');
				}
				$erreur .= _T('mediatheque:ima_err_reload');
			}
		}
		// end of check points
		if($display) {print "END OF CHECK<br/><br/>";}
	} // end if submit photo

	if ($erreur != '') {print "<p>$erreur</p>";}
	else {if($display) {print "NO ERROR, CHECK OK<br/><br/>";}}

	// ---------------------------------------------
	//	TREATMENT
	// ---------------------------------------------
	if ($Submit_photo AND $formcorrect) {

		// add new
		// check type and create dir if needed

		if (is_uploaded_file($photo_to_load)) {
			if ($writedb) {
				move_uploaded_file($photo_to_load, "$ikono_site_path_typed/$photo_to_load_name");
				chmod("$ikono_site_path_typed/$photo_to_load_name", 0755);
			}
			if ($display) {print "Moved : $photo_to_load => $ikono_site_path_typed/$photo_to_load_name<br><br/>";}
		}

		// ---------------------------------------------
		//		insert in spip_documents
		// ---------------------------------------------
		$photo_desc = addslashes($photo_desc);
		$photo_name = addslashes($photo_name);
		$date = date("Y-m-d H:i:s");
		$timestamp = date("YmdHis");

		$fichier = "$dir_img/$type_lib/$photo_to_load_name";


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
					'$type',
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


		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>";
		if (!$display) {print "<script>window.close();</script>";}

	}

	if ($Submit_cancel) {
		$link_back = "<a href=\"?exec=mediatheque_img_browser\">" . _T('mediatheque:com_li_back') . "</a>";
		print "<p>$link_back</p>";
	}


	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------


	// ---------------------------------------------
	//		before exit
	// ---------------------------------------------
	if ($Submit_photo and $formcorrect) {
		$image_to_show = "<img src=\"$ikono_web_path/$type_lib/$photo_to_load_name\">";
		$image_path = "$ikono_web_path/$type_lib/$photo_to_load_name";

		$link_placer = "<a href=\"?exec=image_add\">" . _T('mediatheque:ima_li_put') . "</a>";
		$link_ajouter = "<a href=\"?exec=image_add\">" . _T('mediatheque:ima_li_add_more') . "</a>";
		$link_back = "<a href=\"?exec=mediatheque_img_browser\">" . _T('mediatheque:ima_li_back') . "</a>";

		print "<p>$image_to_show <br/><br/>" . _T('mediatheque:ima_msg_confirm') . "</p>";
		print "<p>" . _T('mediatheque:ima_msg_would_you') . "<ul>";
		print "<li>$link_placer";
		print "<li>$link_ajouter";
		print "<li>$link_back";
		print "</li></ul></p>";

	}

	// ---------------------------------------------
	//		form
	// ---------------------------------------------
	else {
		print "<form name=\"form\" action=\"\" method=\"POST\" enctype=\"multipart/form-data\">\r\n";
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print _T('mediatheque:ima_legend');
		print "</legend>\r\n";
		print "<p><em>" . _T('mediatheque:ima_explain') . "</em></p>";
		print "<p>" . _T('mediatheque:ima_fi_browse') . "<br/><input name=\"photo_to_load\" type=\"file\" class=\"small\" size=\"20\" value=\"$photo_to_load\"></p>\r\n";

		//print_select_arbo_lib($type_of_doc = 'img');

		print "\r\n\r\n<p>" . _T('mediatheque:ima_fi_rub') . "<br/><select name=\"photo_rubrique\" size=\"$h_size\">\r\n";
		print "<option value=\"0\">";
		print "/Racine ---";
		print "</option>\r";
		new_arbo_lib(0, 0, $photo_rubrique);
		print "</select></p>";

		print "<p>". _T('mediatheque:ima_fi_name') . "<br/><input name = \"photo_name\" type=\"text\" value=\"$photo_name\"></p>\r\n";
		print "<p>". _T('mediatheque:ima_fi_credit') . "<br/><input name = \"photo_credit\" type=\"text\" value=\"$photo_credit\"></p>\r\n";

		print "<p>". _T('mediatheque:ima_fi_desc') . "<br/><textarea name=\"photo_desc\" cols=\"50\" rows=\"5\">$photo_desc</textarea></p>\r\n";

		$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"". _T('mediatheque:ima_bu_cancel') . "\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";

		$action1 = "<input type=\"submit\" name=\"Submit_photo\" value=\"". _T('mediatheque:ima_bu_ok') . "\" class=\"boutonBOform\">\r\n";

		print "<br>$action2 &nbsp;&nbsp;&nbsp; $action1 ";

		print "</fieldset>\r\n";
		print "</form>\r\n";


	}


	print display_popup_footer();
}


?>