<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_mediatheque_img_browser() {

//include_spip('inc/mediatheque_time_lib');
//$time_start = getmicrotime(); // to get exe time in any page
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
	include_spip('base/mediatheque_params');
		
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
//	spip format	
// ---------------------------------------------
	debut_page(_L("Tous les Documents"), "documents", "documents");
	
	// empty col
	debut_gauche();
	fin_gauche;
	
	debut_droite();
	print "<div align=\"left\">";



// ---------------------------------------------
//		Decode browser options
// ---------------------------------------------
	$disp_image 	= _request('disp_image'); // easier to use
	$photo_rubrique = _request('photo_rubrique');
	
	switch ($disp_image) {
		case "voir": $light_display = false; break;
		case "": $light_display = true; break;
		default: $light_display = true;
	}


// ---------------------------------------------
//		Prepare buttons & links
// ---------------------------------------------	
	$link_add_image = "?exec=image_add&id=$photo_rubrique";
	$link_add_image = tag_popup_window($link_add_image, "Ajouter une image", 500, 600, "boutonBO", "Ajouter une image");
	
	$link_add_rub = "?exec=imgrub_add&id=$photo_rubrique";
	$link_add_rub = tag_popup_window($link_add_rub, "Ajouter une rubrique", 500, 450, "boutonBO", "Ajouter une rubrique");
	
	// switcher voir images
	$checked_si = ($disp_image == 'voir') ? 'checked' : '';
	$image_switcher = "<input name=\"disp_image\" id=\"disp_image\" type=\"checkbox\" value=\"voir\" $checked_si><label for='disp_image'>Voir images</label>"; 
	
	// Submit
	$submit_button = "<input type=\"submit\" name=\"Submit_switch\" value=\"Go\" class=\"boutonBOform\">\r\n";


// ---------------------------------------------
//		Display phototheque
// ---------------------------------------------
	print "<h2 class='verdana-2' style='font-size:18px;'>La phototh&egrave;que</h2>";
	
	print "<div class='cadre-info'><form action=\"\" name=\"browserselector\" method=\"post\">";
	
	// fieldset + legend
	
	print "<p><label for='photo_rubrique'>Options :";
	
	// rub selector
		if ($photo_rubrique == 0) {$is_selected = "selected";}
		else {$is_selected = "";}
		
		print "\r\n\r\nFiltrer par rubrique</label></p>";
		print "<select name=\"photo_rubrique\" id=\"photo_rubrique\" size=\"$h_size\">\r\n";
			print "<option $is_selected value=\"0\">";
			print "/Racine ---";
			print "</option>\r\n";
			
		new_arbo_lib(0, 0, $photo_rubrique);
		print "</select> $submit_button";
	
	// image <text field> | Tout voir | 
	print "<p>";
	print "$image_switcher";
	print "</p>";
	print "<p>Gestion : $link_add_image | $link_add_rub</p>";
	
	print "</form></div><br/>";

// browse arbo
	mediatheque_get_children($photo_rubrique, 0, $light_display);
	
	
	//display_exe_time($time_start, " processed in  "); // to check
// end spip format
	print "<div>";
	
	fin_page();
} 
?>
