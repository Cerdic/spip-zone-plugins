<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	Updated Alain 9/01/07
// ---------------------------------------------

function exec_imgrub_delete() {

	global $Submit_confirm_rub, $Submit_confirm_branch, $Submit_cancel;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	global $content_scanned, $cs_ctr;
	
	// init
	$display = false;
	$writedb = true;
	$imgrub_id = _request('id');
	
	
	$id_auteur = $auteur_session['id_auteur'];
	
	$site_root 		= $_SERVER['DOCUMENT_ROOT'];
	$web_root 		= _DIR_RACINE;
	
	$ikono_site_path = "$site_root/drass";
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
	//		GET DATA
	// ---------------------------------------------
		$qim = "select * from spip_xtra_imgrub
				where imgrub_id = '$imgrub_id'
				";
				
		$rim = spip_query($qim);
		
		$obj = mysql_fetch_object($rim);
			$imgrub_id_parent			= $obj -> imgrub_id_parent;
			$imgrub_titre				= $obj -> imgrub_titre;
			$imgrub_descriptif			= $obj -> imgrub_descriptif;
			
			
		
	// ---------------------------------------------
	//		PAGE TITLE
	// ---------------------------------------------
	$link_back = "<a href=\"?exec=mediatheque_img_browser\">Retourner &agrave; la phototh&egrave;que</a>";
	
	print "<H3>Supprimer la rubrique &laquo; $imgrub_titre &raquo; de la phototh&egrave;que</H3>";
	print "<p>$link_back</p>";
	
	
	
	// ---------------------------------------------
	//	CHECK DATA	
	// ---------------------------------------------
	$formcorrect = true;
	$erreur='';
	
					
		
		
		// ---------------------------------------------
		//		2. check if any child rub
		// ---------------------------------------------
		$cs_ctr = 0;
		
		scan_for_content($imgrub_id, 'img');
		// treat array
		
		// is this rub full
		$this_rub_nb_img = $content_scanned[0][1];
		if ($this_rub_nb_img > 0) {$this_rub_empty = false;}
		else {$this_rub_empty = true;}
		
		// is_any child_rub
		$nb_child_rub = count($content_scanned) - 1;
		if ($nb_child_rub > 0) {$is_any_child = true;}
		else {$is_any_child = false;}
		
		// get sum of child rub
		$img_sum = 0;
		for ($i = 1; $i < count($content_scanned); $i++) {
			$img_sum = $img_sum + $content_scanned[$i][1];
		}
		
		if ($img_sum > 0) {$child_rub_empty = false;}
		else {$child_rub_empty = true;}
		
		if ($display) {
				print "nbchild rub = $nb_child_rub<br/>img sum $img_sum<br/>";
		}
			
	
	
	// ---------------------------------------------
	//	TREATMENT 1 => DELETE RUB
	// ---------------------------------------------
	if ($Submit_confirm_rub) {
	
		// ---------------------------------------------
		//		if child rub => compact tree
		// ---------------------------------------------
		if ($is_any_child) {
			if ($writedb) {
				compact_tree($imgrub_id);
			}
		}
		
		// ---------------------------------------------
		//		no child rub => simple remove
		// ---------------------------------------------

		else {
			if ($writedb) {
				delete_rub($imgrub_id);
			}
		}
		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>"; 
		print "<script>window.close();</script>";
	}
	
	
	// ---------------------------------------------
	//	TREATMENT 2 => DELETE BRANCH
	// ---------------------------------------------
	if ($Submit_confirm_branch) {
		if ($writedb) {
			recursive_delete_rub($imgrub_id);
		}
		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>"; 
		print "<script>window.close();</script>";
	}
	
	if ($Submit_cancel) {
		print "operation annulée";
	}

	
	
	 
	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------
	
	if (!$Submit_confirm_rub and !$Submit_confirm_branch) {
		
		// is superadmin
		if ($auteur_session['statut'] == '0minirezo') {
		
			$del_rub = "<input type=\"submit\" name=\"Submit_confirm_rub\" value=\"Supprimer la rubrique\" class=\"boutonBOform\">\r\n";
			$del_branch = "<input type=\"submit\" name=\"Submit_confirm_branch\" value=\"Supprimer la branche\" class=\"boutonBOform\">\r\n";
			
			
			print "<form name=\"form\" action=\"\" method=\"POST\">\r\n";
			print "<fieldset>\r\n";
			print "<legend>\r\n";
			print "V&eacute;rification";
			print "</legend>\r\n";
			
			print "<H3>Rubrique : $imgrub_titre</H3>";
			// print "<p>Descriptif : $imgrub_descriptif</p>";
			
			// ---------------------------------------------
			//		images in this rub
			// ---------------------------------------------
			if (!$this_rub_empty) {
				
				if ($this_rub_nb_img > 1) {
					$nb_image_lib = "$this_rub_nb_img images";
					$del_img_lib = "toutes les images";
				}
				else {
					$nb_image_lib = "$this_rub_nb_img image";
					$del_img_lib = "l'image";
				}
				
				$user_msg = "Cette rubrique contient $nb_image_lib. <br/>Vous ne pouvez pas la supprimer avant
d'avoir retir&eacute; $del_img_lib qu'elle contient.";
				$form_action = "";
			
			}
			
			// ---------------------------------------------
			//		no images in this rub, but images in child rub
			// ---------------------------------------------
			elseif (!$child_rub_empty) {
				if ($nb_child_rub > 1) {
					$nb_child_rub_lib = "ses sous-rubriques en contiennent";
					$what_child_rub_lib = "les sous-rubriques remonteront d'un niveau";
				}
				else {
					$nb_child_rub_lib = "sa sous-rubrique en contient";
					$what_child_rub_lib = "la sous-rubrique remontera d'un niveau";
				}
				
				$user_msg = "Cette rubrique ne contient pas d'image, mais $nb_child_rub_lib.<br/>
Vous pouvez supprimer la rubrique et $what_child_rub_lib.";
				
				$form_action = "$del_rub";
			}
			
			// ---------------------------------------------
			//		no images in this rub, neither in child rub
			// ---------------------------------------------
			else {
				switch ($nb_child_rub) {
	
					case 0:
						$nb_child_rub_lib = "ni de sous-rubrique";
						$what_child_rub_lib = "";
						$form_action = "$del_rub";
					break;
					
					case 1:
						$nb_child_rub_lib = "ni sa sous-rubrique";
						$what_child_rub_lib = "(la sous-rubrique remontera d'un niveau) <li>supprimer la branche complète
						 (rubrique et sous-rubrique)";
						 $form_action = "$del_rub &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $del_branch";
					break;
				
					default:
						$nb_child_rub_lib = "ni ses $nb_child_rub sous-rubriques";
						$what_child_rub_lib = "(les sous-rubriques remonteront d'un niveau) <li>supprimer la branche complète
						 (rubrique et sous-rubriques)";
						 $form_action = "$del_rub &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $del_branch";
				}
				
				
				$user_msg = "Cette rubrique ne contient pas d'images, $nb_child_rub_lib. <br/><br/>Vous pouvez : <ul>
				<li>supprimer la rubrique $what_child_rub_lib"; 
				
				
			}
			$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";
			print "<p>$user_msg</ul></p>";
			print "<p>$action2 &nbsp;&nbsp;&nbsp; $form_action</p>";
			
			print "</fieldset>\r\n";
			print "</form>\r\n";
			
		}
		else {
			print "D&eacute;sol&eacute;, seul un superadmin peut intervenir<br/>";
			print "Retour &agrave; la phototh&egrave;que";
		}
	
	}
	  
	print display_popup_footer();
}


// ---------------------------------------------
//		FUNCTION AREA
// ---------------------------------------------

// ---------------------------------------------
//		delete one rub
// ---------------------------------------------
function delete_rub($from_rub) {
	
	$display = false;
	$writedb = true;
	
	$qdel = "delete from spip_xtra_imgrub
				where imgrub_id = '$from_rub'
			";
	
	if ($writedb) {$rdel = mysql_query($qdel);}
	if ($display) {print "$qdel / <br/><br/>";}
	
}

// ---------------------------------------------
//		recursive delete from starting rub
// ---------------------------------------------
function recursive_delete_rub($from_rub) {
	$display = false;
	$writedb = true;
	
	// --------------------
	//	get child rubs
	// --------------------
	$qchild = "select * from spip_xtra_imgrub
			where imgrub_id_parent = '$from_rub'";		

	$rchild = mysql_query($qchild);
	$nb_child = mysql_num_rows($rchild);
	
	// --------------------
	//	delete current rub
	// --------------------
	if ($writedb) {delete_rub($from_rub);}
	
	// --------------------
	//	recursive call for each child rub
	// --------------------
	while ($obj = mysql_fetch_object($rchild)) {
		$imgrub_id 		= $obj->imgrub_id;	
		recursive_delete_rub($imgrub_id);
	}
}


// ---------------------------------------------
//		delete one rub and compact tree then
// ---------------------------------------------
function compact_tree($from_rub) {
	$display = false;
	$writedb = true;
	
	// --------------------
	//	get parent(rub)
	// --------------------
	$qim = "select * from spip_xtra_imgrub
				where imgrub_id = '$from_rub'
				";
			
	$rim = spip_query($qim);
	$obj = mysql_fetch_object($rim);
	$rub_parent	= $obj -> imgrub_id_parent;		
	
	// --------------------
	//	get 1 level child
	// --------------------
	$qchi = "select * from spip_xtra_imgrub
				where imgrub_id_parent = '$from_rub'
				";
			
	$rchi = spip_query($qchi);
		
	// --------------------
	//	replace parents
	// --------------------
	while ($obj = mysql_fetch_object($rchi)) {
		$child_rub_id			= $obj -> imgrub_id;
		
		$qre = "update spip_xtra_imgrub
				set
				imgrub_id_parent = '$rub_parent'
				where
				imgrub_id = '$child_rub_id'
				";
				
		if ($writedb) {$rre = mysql_query($qre);}
		if ($display) {print "$qre<br/><br/>";}
		
	}
	
	// --------------------
	//	delete rub now
	// --------------------
	if ($writedb) {delete_rub($from_rub);}
	
	
}

?>