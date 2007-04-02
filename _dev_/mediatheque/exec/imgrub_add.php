<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_imgrub_add() {

	global $Submit_photo, $Submit_cancel;
	global $imgrub_titre, $parent_rubrique, $imgrub_descriptif;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	
	// init
	$display = false;
	$writedb = true;
	$parent_rubrique = _request('id');
	
	
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
	print "<H3>Ajouter une rubrique &agrave; la phototh&egrave;que</H3>";
	
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
		$imgrub_descriptif 	= addslashes(stripslashes($imgrub_descriptif));
		$imgrub_titre 		= addslashes(stripslashes($imgrub_titre));
		$date 				= date("Y-m-d H:i:s");
		$timestamp 			= date("YmdHis");
		$imgrub_id_parent 	= $parent_rubrique; // from function name
		
		
		$qupd = "INSERT into spip_xtra_imgrub (
					imgrub_titre,
					imgrub_descriptif,
					imgrub_id_parent
					)
					VALUES (
					'$imgrub_titre',
					'$imgrub_descriptif',
					'$imgrub_id_parent'
					)				
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
	
	/*
		// ---------------------------------------------
		//		get data
		// ---------------------------------------------
		$qim = "select * from spip_xtra_imgrub
				where imgrub_id = '$imgrub_id'
				";
				
		$rim = spip_query($qim);
		
		while ($obj = mysql_fetch_object($rim)) {
			$imgrub_id_parent			= $obj -> imgrub_id_parent;
			$imgrub_titre				= $obj -> imgrub_titre;
			$imgrub_descriptif			= $obj -> imgrub_descriptif;
			
		}
	*/
		
		print "<form name=\"form\" action=\"\" method=\"POST\">\r\n";
		print "<fieldset class=\"spip_encadrer\">\r\n";
		print "<legend>\r\n";
		print "Ajouter la rubrique";
		print "</legend>\r\n";
		
		
		print "<div class=\"row\">";
		print "<span class=\"libr\">";
		print "<label for=\"imgrub_titre\">Nom</label><br/>";
		print "</span>";
		
		print "<span class=\"fieldr\">";
		print "<input name = \"imgrub_titre\" type=\"text\" value=\"$imgrub_titre\">\r\n";
		print "</span>";
		print "</div>";
				
		print "\r\n\r\n<p>Dans la rubrique<br/><select name=\"parent_rubrique\" size=\"$h_size\">\r\n";
		print "<option value=\"0\">";
		print "/Racine ---";
		print "</option>\r";
		new_arbo_lib(0, 0, $parent_rubrique);
		print "</select></p>";
		
		
		print "<p>Descriptif<br/><textarea name=\"imgrub_descriptif\" cols=\"50\" rows=\"5\">$imgrub_descriptif</textarea></p>\r\n";
		
		$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";
		
		$action1 = "<input type=\"submit\" name=\"Submit_photo\" value=\"Enregistrer\" class=\"boutonBOform\">\r\n";
		
		print "<br>$action2 &nbsp;&nbsp;&nbsp; $action1 ";
		
		print "</fieldset>\r\n";
		print "</form>\r\n";
		
	  
		print display_popup_footer();
}


?>