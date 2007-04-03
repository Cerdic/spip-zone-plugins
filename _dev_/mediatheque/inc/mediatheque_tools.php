<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	Updated Alain 17/01/07
// ---------------------------------------------

// ---------------------------------------------
//		disp header for pop up windows
// ---------------------------------------------
function display_popup_header() {
$str = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"fr\">
<head>
<title>le titre</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"screen,projection\" href=\"". (_DIR_PLUGIN_MEDIATHEQUE) ."/css/pop.css\" />
</head>
<body>
";

return $str;


}

function display_popup_footer() {
$str = "</body>\r\n</html>";
return $str;
}



// ==============================================================
//		mediatheque_get_children
//		for phtotheque browser and admin
// ==============================================================
function mediatheque_get_children($any_parent, $level, $is_light=false) {

	$margin_size = ($level) * 50;
	$margin = "$margin_size" . "px";
	
	$local_style = "margin-left:$margin;";
	$local_style2 = "margin-left:$margin;
					padding-top: 5px;
					position: left;
					font-family: arial;
					font-size: 18px;
					font-weight: bold;
					color: #555;";				
					
	$del_style = "font-size: 12px";
	
	// ---------------------------------------------
	//	to be inc elsewhere	
	// ---------------------------------------------
	print "<script type=\"text/javascript\">
	function PopupADroite(page,largeur,hauteur,options) {
	  var top=(screen.height-(hauteur+screen.height));
	  var left=(screen.width-(largeur+45));
	  window.open(page,\"\",\"top=\"+top+\",left=\"+left+\",width=\"+largeur+\",height=\"+hauteur+\",\"+options);
	}
	</script>
	";

					
	// ---------------------------------------------
	//		get current rub data & images
	// ---------------------------------------------
	if ($any_parent != 0) {
		$qru = "select * from spip_xtra_imgrub
			where imgrub_id = '$any_parent'
			";		

		$rru = mysql_query($qru);
		$obj = mysql_fetch_object($rru);
				$imgrub_id 		= $obj->imgrub_id;
				$imgrub_titre 	= $obj->imgrub_titre;
				
			
		$imgrub_titre_link = "?exec=imgrub_change&id=$imgrub_id";
		$imgrub_titre_link = tag_popup_window($imgrub_titre_link, "$imgrub_titre", 500, 450, "boutonBO", "Voir ou Modifier la rubrique");
		
							
		$imgrub_del_link = "?exec=imgrub_delete&id=$imgrub_id";
		$imgrub_del_link = tag_popup_window($imgrub_del_link, "<img src='img_pack/croix-rouge.gif' alt='X' align='top' height='7' width='7'>", 500, 450, "boutonBO", "Supprimer la rubrique");
							
							
		$imgrub_link = "$imgrub_titre_link $imgrub_del_link";
	}
	else {
		$imgrub_link = "/Racine de la phototh&egrave;que";
	}
	
	print "<div style='background:#C0CAD4 url(img_pack/secteur-24.gif) scroll no-repeat left center;padding-bottom:5px;padding-left:28px;padding-top:5px;$local_style'><b class='verdana2'>";
	print "$imgrub_link</b></div>";
	print "<div style=\"$local_style\">";
	mediatheque_diplay_images($any_parent, $margin,$is_light);
	print "</div>";
	
	// ---------------------------------------------
	//		Look for $any_parent's child
	// ---------------------------------------------
	$qchild = "select * from spip_xtra_imgrub
			where imgrub_id_parent = '$any_parent'
			order by imgrub_titre
			";		

	$rchild = mysql_query($qchild);
	$nb_child = mysql_num_rows($rchild);

	// ---------------------------------------------
	//		stop condition <=> nb_child = 0
	// ---------------------------------------------
	if ($nb_child == 0) {return;}

	// ---------------------------------------------
	//		display items in child rub
	// ---------------------------------------------
	else {
		$level++;
		// get items and list
		while ($obj = mysql_fetch_object($rchild)) {
			$imgrub_id 		= $obj->imgrub_id;
			$imgrub_titre 	= $obj->imgrub_titre;

			mediatheque_get_children ($imgrub_id, $level, $is_light);
		} // end of list items and display

		$level--;
	} // end of display

} // end of function get_child


// =============================================
// 		mediatheque_diplay_images
//		sub function display images from any imgrub
//      for phototheque browser and amdin
// =============================================
function mediatheque_diplay_images($any_imgrub, $margin, $is_light=false) {

	$web_root = _DIR_RACINE;
	$dir_img = get_dir_img();
	

	$query = "select distinct * from spip_documents 
				where id_tek_rub = '$any_imgrub'
				and (id_type= 1 or id_type = 2 or id_type = 3)
				order by fichier
				";
	$result = mysql_query($query);

	print "<span style='clear: left; margin-left: $margin;'>";
	
	$style_full_display = "<span style=\"
						float: left;
						margin: 3px;
						padding: 5px;
						border: 1px solid black;
						
						background-color:#DDD;
						text-align: center;
						font-family: arial;
						font-weight: normal;
						font-size: 11px;
						color: #000;
						\"
					>";
			
			$style_light_display = "<span style='
						font-family: arial;
						font-weight: normal;
						font-size: 11px;
						color: #000;
						'
					>";

	while ($obj = mysql_fetch_object($result)) {
		$fichier 		= $obj->fichier;
		$titre 			= $obj->titre;
		$url_fichier 	= $obj->url_fichier;
		$id_document	= $obj->id_document;
		$file_size		= $obj -> taille;
		$width			= $obj -> largeur;
		$height			= $obj -> hauteur;

		$url_fichier = generer_url_document($id_document);
		$titre_lib = ($titre != '') ? "<strong>$titre</strong>" : "<em> Sans nom</em>";
		
		list($img_dir, $img_type, $img_name) = explode('/', $fichier);
	
		
		
		$img_src = "$web_root/$dir_img/$img_type/$img_name"; 
		
		//$img_src = "$web_root/IMG/cache-mediatheque-vignette/vgn_$img_name"; 
		
		$max_width = 110; // to adjust if needed ***
		
		
		
		//if (file_exists($url_fichier)) {
			$size = @getimagesize($url_fichier);
			$file_size = @filesize($url_fichier);
			
			$height = $size[1];
			$width = $size[0];
			
			if ($width > $height) {$width_disp = min($max_width, $width);}
			else {$width_disp = min(@ceil($width*($max_width/$height)), $width);}

			$fichier_lib = eregi_replace('IMG/.*/', '', $fichier);

			 $widthpx = "$max_width" . "px";
			
			

			// infos
			$size_info = "L x H : $width x $height";
			$volume_info = "$file_size octets";
			$num_info = "N&ordm; $id_document";


			// full display
			$link_delete = "?exec=image_delete&id_image=$id_document";
			$link_delete = tag_popup_window($link_delete, "Supprimer", 500, 600, "boutonBO", "Supprimer une image");
			
			$link_change = "?exec=image_change&id_image=$id_document";
			$link_change = tag_popup_window($link_change, "Modifier", 500, 600, "boutonBO", "Voir ou modifier une image");
			
			$link_change_img = "?exec=image_change&id_image=$id_document";
			$link_change_img = tag_popup_window($link_change_img, "<img style='border: none' src=\"$img_src\" width=\"$width_disp\">", 500, 600, "boutonBO", "Voir ou modifier une image");

			// light display
			$link_delete_light = "?exec=image_delete&id_image=$id_document";
			$link_delete_light = tag_popup_window($link_delete_light, "Supprimer", 500, 600, "boutonBO", "Supprimer une image");
			
			$link_change_light = "?exec=image_change&id_image=$id_document";
			$link_change_light = tag_popup_window($link_change_light, "Modifier", 500, 600, "boutonBO", "Voir ou modifier une image");
			
			$link_change_img_light = "?exec=image_change&id_image=$id_document";
			$link_change_img_light = tag_popup_window($link_change_img_light, "$num_info - $titre_lib", 500, 600, "boutonBO", "Voir ou modifier une image");
			
			
			//print "<br/>";
			if (!$is_light) {
				print $style_full_display;
				print "$num_info<br/>";
				
				print "$link_change_img<br/>";
				
				print "$titre_lib<br/>";
				print "$size_info<br/>";
				print "$volume_info<br/>";
				print "<em>$img_name</em><br/><hr>";
				
				print "$link_change<br/>";
				print "$link_delete<br/>";
				//print "$link_insert<br/>";
				//print "Associer cette image (hors texte)<br/>";
			}
			else {
				print $style_light_display;
				print "<br/>";
				print "<strong>$link_change_img_light</strong><br/>";
				print "$size_info";
				print " ($volume_info)<br/>";
				print "<em>$fichier_lib</em><br/>";
				//print "$link_change | ";
				print "$link_delete<br/>";
			}
			print "</span>";

		//}
	}
	print "<br style='clear: left'/>";
}



// =============================================
// 		mediatheque_diplay_images_light
//		specific version of above to insert image in article
//		** todo ? mixed both ?
// =============================================
function mediatheque_diplay_images_light($any_imgrub, $from_article, $set_wysiwyg) {

	$max_width = $max_height = 130;
	$web_root = _DIR_RACINE;
	
// ---------------------------------------------
//	to be inc elsewhere	
// ---------------------------------------------
print "<script type=\"text/javascript\">
function PopupADroite(page,largeur,hauteur,options) {
  var top=(screen.height-(hauteur+screen.height));
  var left=(screen.width-(largeur+45));
  window.open(page,\"\",\"top=\"+top+\",left=\"+left+\",width=\"+largeur+\",height=\"+hauteur+\",\"+options);
}
</script>
";

	$query = "select * from spip_documents
				where id_tek_rub = '$any_imgrub'
				and (id_type= 1 or id_type = 2 or id_type = 3)
				order by fichier
				";
	$result = spip_query($query);

	
	
$nb_cell = 0;	
print "<table bgcolor=\"#FFF\" cellspacing=\"3\" cellpadding=\"3\">";
print "<tr>";

	while ($obj = mysql_fetch_object($result)) {
		$fichier 		= $obj->fichier;
		$titre 			= $obj->titre;
		$url_fichier 	= $obj->url_fichier;
		$id_document	= $obj->id_document;
		$file_size		= $obj -> taille;
		$width			= $obj -> largeur;
		$height			= $obj -> hauteur;

		$url_fichier = generer_url_document($id_document);

		$max_width = 130; // to adjust if needed ***
		if ($width > $height) {$width_disp = min($max_width, $width);}
		else {$width_disp = min(ceil($width*($max_width/$height)), $width);}
		
		$titre_lib = ($titre != '') ? "<strong>$titre</strong>" : "<em> Sans nom</em>";

		if (file_exists($url_fichier)) {
			//$size = @getimagesize($url_fichier);
			//$file_size = @filesize($url_fichier);
			//$height = $size[1];
			//$width = $size[0];

			$fichier_lib = eregi_replace('IMG/.*/', '', $fichier);

			$link_img = "$url_fichier";
			$link_img_zoom = tag_popup_window($link_img, 
			"<img style='border: none' src=\"$web_root/$fichier\" width=\"$width_disp\">", $width, $height, "boutonBO", "Voir en vraies grandeurs");
			
			$link_zoom = tag_popup_window($link_img,"Vue r&eacute;elle", $width, $height, "boutonBO", "Voir en vraies grandeurs");
			
			$link_insert = "<a href=\"?exec=image_insert_form&id_document=$id_document&id_article=$from_article&set_wysiwyg=$set_wysiwyg\">Choisir</a>";
			
			$size_info = "L x H : $width x $height";
			$volume_info = "$file_size octets";
			$num_info = "N&ordm; $id_document";
			$format_info = $size[3];
			
			if ($nb_cell >= 3) {
				print "</tr><tr>";
				$nb_cell = 0;
			}
			
			print "<td bgcolor=\"#DDD\"align=\"center\" valign=\"bottom\">";
				print $style_full_display;
				print "$num_info<br/>";
				print "$link_img_zoom<br/>";
				print "$titre_lib<br/>";
				print "$link_zoom<br/><br/>";
				print "$size_info<br/>";
				print "$volume_info<br/>";
				//print "Raccourci : &lt;img$id_document|center><br/>";
				print "<em>$fichier_lib</em><br/>";
				
				//print "$link_change<br/>";
				//print "$link_delete<br/>";
				print "<hr>";
				print "<strong>$link_insert</strong><br/>";
			
			print "</td>";
			$nb_cell++;
		}
	}
	print "<br style='clear: left' /></div>";
}


// ---------------------------------------------
//		sub function to indent
// ---------------------------------------------
function indent_right($amount = 4) {
	return str_repeat('&nbsp;', $amount);
}




// =============================================
//		new arbo lib
// =============================================
function new_arbo_lib ($any_parent, $level, $rub = '') {
	
	$qchild = "select * from spip_xtra_imgrub
				where imgrub_id_parent = '$any_parent'
				order by imgrub_titre
				";		
	
		$rchild = mysql_query($qchild);
		$nb_child = mysql_num_rows($rchild);
	
		// ---------------------------------------------
		//		stop condition <=> nb_child = 0
		// ---------------------------------------------
		if ($nb_child == 0) {return;}
		
		else {
				$level++;
	
				// get items and list em
				while ($obj = mysql_fetch_object($rchild)) {
					$value 		= $obj->imgrub_id;
					$texte 		= $obj->imgrub_titre;
		
					if ($value == $rub) {$is_selected = "selected";}
					else {$is_selected = "";}
		
					print "<option $is_selected value=\"$value\">";
					print indent_right(($level) * 4);
					print "$texte</option>\r";
		
					new_arbo_lib ($value, $level, $rub);
		
				} // end of list items and display
				$level--;
			} // end of display
} // end of function new_arbo_lib


// =============================================
//		arbo selector for parent rub (specific)
// =============================================
function arbo_parent_selector ($any_parent, $level, $current_parent_rub = '', $current_rub = '') {
	
	$qchild = "select * from spip_xtra_imgrub
				where imgrub_id_parent = '$any_parent'
				order by imgrub_titre
				";		
	
		$rchild = mysql_query($qchild);
		$nb_child = mysql_num_rows($rchild);
	
		// ---------------------------------------------
		//		stop condition <=> nb_child = 0
		// ---------------------------------------------
		if ($nb_child == 0) {return;}
		
		else {
				$level++;
	
				// get items and list em
				while ($obj = mysql_fetch_object($rchild)) {
					$value 		= $obj->imgrub_id;
					$texte 		= $obj->imgrub_titre;
		
					if ($value == $current_parent_rub) {$is_selected = "selected";}
					else {$is_selected = "";}
		
					// exclude current rub itself avoiding loop
					//if ($value != $current_rub) {
						print "<option $is_selected value=\"$value\">";
						print indent_right(($level) * 4);
						print "$texte</option>\r";
					//}
		
					arbo_parent_selector ($value, $level, $current_parent_rub, $current_rub);
		
				} // end of list items and display
				$level--;
			} // end of display
} // end of function new_arbo_lib




























// =============================================
//		TOOL for delete rub
// =============================================
function scan_for_content($start_rub, $img_or_doc = 'img') {
	
	global $content_scanned, $cs_ctr;
	// ---------------------------------------------
	//		get image in start rub
	// ---------------------------------------------
	
	if ($img_or_doc == 'img') {
		$qim = "select * from spip_documents
			where id_type <= 3
			and id_tek_rub = '$start_rub'
			";
	}
	else {
		$qim = "select * from spip_documents
			where id_type > 3
			and id_tek_rub = '$start_rub'
			";
	}
	
			
	$rim = mysql_query($qim);
	$tim = mysql_numrows($rim);
	
	// print "$tim found for $start_rub<br/>";
	$content_scanned[$cs_ctr][0] = $start_rub;
	$content_scanned[$cs_ctr][1] = $tim;
	$cs_ctr++;
	
	
	// ---------------------------------------------
	//		get child rubs
	// ---------------------------------------------
	$qchild = "select * from spip_xtra_imgrub
			where imgrub_id_parent = '$start_rub'";		

	$rchild = mysql_query($qchild);
	$nb_child = mysql_num_rows($rchild);
	
	while ($obj = mysql_fetch_object($rchild)) {
		$imgrub_id 		= $obj->imgrub_id;
		
		scan_for_content($imgrub_id, $img_or_doc);
	
	}
}

// ===== TAG_POPUP_WINDOW ===========================================
//	left and top = position in screen		
// 	  last modification: 12/08/04 - Alain (title define)
// ===============================================================

function tag_popup_window($page, $word, $width=200, $height=300, $style="",  $title="Ouverture dans une nouvelle fen�tre") {
		
	$str_res = "";
	
	$str_res .= "<span class=\"$style\"><a href=\"$page\"";
	$str_res .= " onClick=\"PopupADroite";
	$str_res .= "('" . $page . "',";
	$str_res .= "'NewWindow','";
	$str_res .= "toolbar=no,";
	$str_res .= "location=no,";
	$str_res .= "directories=no,";
	$str_res .= "statusbar=no,";
	$str_res .= "menubar=no,";
	$str_res .= "scrollbars=yes,";
	$str_res .= "resizable=yes,";
	$str_res .= "width=$width,";
	$str_res .= "height=$height,";
	$str_res .= "left=400,";
	$str_res .= "top=20";
	$str_res .= "'); return false;\"";
	$str_res .= "title=\"$title\">";
	$str_res .= "$word</a></span>";
	
return $str_res;
}



/*
	Function create_thumbnail($name,$filename,$new_w,$new_h)
	creates a resized image
	variables:
	$image_name	Original filename
	$thumb_name	Filename of the resized image
	$new_w		width of resized image
	$new_h		height of resized image
	adapted from Christian Heilmann
*/	
function create_thumbnail($image_name, $thumb_name, $new_w, $new_h, $type_case='') {
	
	// fix type
	if ($type_case == '') {
		$system = explode(".", $image_name);
		if (preg_match("/jpg|jpeg/", $system[1])) 	{$type_case = 'jpg';}
		if (preg_match("/png/", $system[1])) 		{$type_case = 'png';}
		if (preg_match("/gif/", $system[1])) 		{$type_case = 'gif';}
	}
	
	switch ($type_case) {
		case "jpg": $src_img = imagecreatefromjpeg($image_name); break;
		case "png": $src_img = imagecreatefrompng($image_name); break;
		case "gif": $src_img = imagecreatefromgif($image_name); break;
	}
	
	$old_x = imageSX($src_img);
	$old_y = imageSY($src_img);
	
	if ($old_x > $old_y) {
		$thumb_w = $new_w;
		$thumb_h = $old_y * ($new_h / $old_x);
	}
	
	if ($old_x < $old_y) {
		$thumb_w = $old_x * ($new_w / $old_y);
		$thumb_h = $new_h;
	}
	
	if ($old_x == $old_y) {
		$thumb_w = $new_w;
		$thumb_h = $new_h;
	}
	
	$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
	
	switch ($type_case) {
		case "jpg": $src_img = imagejpeg($dst_img, $thumb_name); break;
		case "png": $src_img = imagepng($dst_img, $thumb_name); break;
		case "gif": $src_img = imagegif($dst_img, $thumb_name); break;
	}
	
	// clean
	@imagedestroy($dst_img); 
	@imagedestroy($src_img); 
}



// ---------------------------------------------
//		TOOLS FOR ARBO
// ---------------------------------------------
function get_dir_img() {	
	$dir_img = ereg_replace('\.|/', '', _DIR_IMG);
	return $dir_img;

}



/*
// ---------------------------------------------
//		OBSOLETE TO REMOVE AFTER CHECK
// ---------------------------------------------
// ==============================================================
//		SELECT ARBO LIB
//		make a selector for image add...
// ==============================================================
function print_select_arbo_lib($type_of_doc = '', $rub = '') {

	switch ($type_of_doc) {

		case "img":
			$table_name 		= "spip_xtra_imgrub";
			$field_lib_name 	= "imgrub_titre";
			$field_name	 		= "imgrub_titre";
			$form_name 			= "photo_rubrique";
			$form_var_name 		= $photo_rubrique;
			$form_var_value		= 'imgrub_id';
			$field_code_name 	= "imgrub_id";
			$h_size				= 1;
			$parent_field 		= 'imgrub_id_parent';

			break;

		case "doc":
			inst;
			break;

		default:
			return;
	}

	$query = "SELECT * FROM $table_name
				where $parent_field = 0
				order by '$field_name'";

	$result = spip_query($query);
	print "\r\n\r\n<p>Rubrique<br/><select name=\"$form_name\" size=\"$h_size\">\r\n";

	while ($field = mysql_fetch_object ($result)) {
		$texte = $field->$field_name;
		$value = $field->$form_var_value;

		if ($value == $form_var_name) {$is_selected = "selected";}
		else {$is_selected = "";}


		print "<option $is_selected value=\"$value\">$texte</option>\r\n";
		arbo_lib_child($value, 1, $rub);
	}
	print "</select></p>";
}

// ---------------------------------------------
//		sub function for arbo lib => get child
// ---------------------------------------------
function arbo_lib_child($any_parent, $level, $rub = '') {
	global $type_of_doc;

	$qchild = "select * from spip_xtra_imgrub
			where imgrub_id_parent = '$any_parent'";		

	$rchild = mysql_query($qchild);
	$nb_child = mysql_num_rows($rchild);
	$rstr .=  "($nb_child)"; // for checking

	// ---------------------------------------------
	//		stop condition <=> nb_child = 0
	// ---------------------------------------------
	if ($nb_child == 0) {return;}

	// ---------------------------------------------
	//		display items
	// ---------------------------------------------
	else {
		$level++;

		// get items and list em
		while ($obj = mysql_fetch_object($rchild)) {
			$value 		= $obj->imgrub_id;
			$texte 		= $obj->imgrub_titre;

			if ($value == $rub) {$is_selected = "selected";}
			else {$is_selected = "";}

			print "<option $is_selected value=\"$value\">";
			print indent_right($level * 2);
			print "$texte</option>\r";

			arbo_lib_child ($value, $level, $rub);

		} // end of list items and display
		$level--;
	} // end of display
} // end of function arbo_lib_child
*/

?>

