<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------
function exec_image_insert() {
	global $set_wysiwyg;
	$display = true;
	$writedb = false;

	
	// *****
	$id_article = _request('id_article');
	
	
	// general spip includes
	include_spip('inc_version');
	include_spip('inc/presentation');
	// include_spip('inc/article_select');
	// include_spip('inc/rubriques');
	include_spip('inc/actions');
	include_spip('inc/documents');
	// include_spip('inc/barre');
	include_spip('inc/actions');
	include_spip('inc/charsets.php');

	// specific plugin includes
	include_spip('inc/mediatheque_tools');
	
	
	// ---------------------------------------------
	//		put header
	// ---------------------------------------------	
	print display_popup_header("Modifier la rubrique");	
	

// ---------------------------------------------
//		Decode options
// ---------------------------------------------
	$disp_image 	= _request('disp_image'); // easier to use
	$photo_rubrique = _request('photo_rubrique');
	
	switch ($disp_image) {

	case "voir": $light_display = false; break;
	case "": $light_display = true; break;
	default: $light_display = false;
}


// ---------------------------------------------
//		prepare buttons & links
// ---------------------------------------------
	$link_add_image = "<a href=\"?exec=image_add\">Ajouter une image</a>";
//	$link_add_rub = "<a href=\"?exec=imgrub_add\">Ajouter une rubrique</a>";
	
	// switcher voir images
	$checked_si = ($disp_image == 'voir') ? 'checked' : '';
	$image_switcher = "<input name=\"disp_image\" type=\"checkbox\" value=\"voir\" $checked_si>Voir images"; 
	
	// Submit
	$submit_button = "<input type=\"submit\" name=\"Submit_switch\" value=\"Go\" class=\"boutonBOform\">\r\n";


// ---------------------------------------------
//		Display phototheque
// ---------------------------------------------

print "<div class='cadre-r'><h2>Ins&eacute;rer une image <br/>de la phototh&egrave;que dans l'article $id_article</h2>";

	print "<div class='cadre-couleur cadre-padding'><form action=\"\" name=\"browserselector\" method=\"post\" class='cadre-couleur'>";
	
	// fieldset + legend
		
	// rub selector
		if ($photo_rubrique == 0) {$is_selected = "selected";}
		else {$is_selected = "";}
		
		print "<p><label for=\"photo_rubrique\">De la rubrique<br/></label>";
		print "<select id=\"photo_rubrique\" name=\"photo_rubrique\" size=\"$h_size\">\r\n";
			print "<option $is_selected value=\"0\">";
			print "/Racine ---";
			print "</option>\r\n";
			
		new_arbo_lib(0, 0, $photo_rubrique);
		print "</select>";
	
	// image <text field> | Tout voir | 
	print " ";
	print "$submit_button</p>";
	//print "<p>Gestion : $link_add_image | $link_add_rub</p>";
	
	print "</form></div>";

	// ---------------------------------------------
	//		get current rub data & images
	// ---------------------------------------------
	if ($photo_rubrique != 0) {
		$qru = "select * from spip_xtra_imgrub
			where imgrub_id = '$photo_rubrique'
			";		

		$rru = mysql_query($qru);
		$obj = mysql_fetch_object($rru);
				$imgrub_id 		= $obj->imgrub_id;
				$imgrub_titre 	= $obj->imgrub_titre;
		

		$imgrub_link = "Images de la rubrique <em>$imgrub_titre</em>";
	}
	else {
		$imgrub_link = "/Racine de la phototh&egrave;que";
	}
	
	print "<H3>$imgrub_link</H3>";
	mediatheque_diplay_images_light($photo_rubrique, $id_article,$set_wysiwyg);
	print "</div>";
	print display_popup_footer();	
} 

?>