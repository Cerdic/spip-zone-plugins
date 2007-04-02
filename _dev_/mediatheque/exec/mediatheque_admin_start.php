<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_mediatheque_admin_start() {

	global $Submit_photo, $photo_to_load, $photo_to_load_name, $photo_to_load_size;
	global $imgrub_titre, $parent_rubrique, $imgrub_descriptif;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	
	if ($auteur_session['statut'] != '0minirezo') {
		print "R&eacute;servé aux administrateurs du site";
		exit;
	}
	
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
	
	// init
	$display = true;
	$writedb = true;
	$imgrub_id = _request('id');
	
	
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
	//	spip format	
	// ---------------------------------------------
		debut_page(_L("Tous les Documents"), "documents", "documents");
		
		// empty col
		debut_gauche();
		fin_gauche;
		
		debut_droite();
		print "<div align=\"left\">";
		
	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	print "<H3>Administration de la mediat&egrave;que</H3>";
	
	$link_make_thumbnail = "?exec=mediatheque_admin_make_thumbnail";
	$link_make_thumbnail = tag_popup_window($link_make_thumbnail, "G&eacute;n&eacute;rer les vignettes images", 500, 600, "boutonBO", "Ajouter une image");
	
	$link_unsused_image = "?exec=mediatheque_admin_cleaner";
	$link_unsused_image = tag_popup_window($link_unsused_image, "D&eacute;tecter les images<br/> et documents non utilis&eacute;s", 500, 600, "boutonBO", "Ajouter une image");
	
	$link_init_rub = "?exec=mediatheque_admin_init_arbo";
	$link_init_rub = tag_popup_window($link_init_rub, "Initialiser le rubriquage", 500, 600, "boutonBO", "Ajouter une image");
	
	$explain_thumbnail = "Inde ubi prima fides pelago, placataque uenti dant maria et lenis crepitans uocat Auster in altum, deducunt socii nauis et litora complent; prouehimur portu terraeque urbesque recedunt. sacra mari colitur medio gratissima tellus Nereidum matri et Neptuno Aegaeo, quam pius arquitenens oras et litora circum errantem Mycono e celsa Gyaroque reuinxit, immotamque coli dedit et contemnere uentos. huc feror, haec fessos tuto placidissima portu accipit; egressi ueneramur Apollinis urbem. rex Anius, rex idem hominum Phoebique sacerdos, uittis et sacra redimitus tempora lauro occurrit; ueterem Anchisen agnouit amicum. iungimus hospitio dextras et tecta subimus.";
	$explain_unused = "Inde ubi prima fides pelago, placataque uenti dant maria et lenis crepitans uocat Auster in altum, deducunt socii nauis et litora complent; prouehimur portu terraeque urbesque recedunt. sacra mari colitur medio gratissima tellus Nereidum matri et Neptuno Aegaeo, quam pius arquitenens oras et litora circum errantem Mycono e celsa Gyaroque reuinxit, immotamque coli dedit et contemnere uentos. huc feror, haec fessos tuto placidissima portu accipit; egressi ueneramur Apollinis urbem. rex Anius, rex idem hominum Phoebique sacerdos, uittis et sacra redimitus tempora lauro occurrit; ueterem Anchisen agnouit amicum. iungimus hospitio dextras et tecta subimus.";
	$explain_init = "Inde ubi prima fides pelago, placataque uenti dant maria et lenis crepitans uocat Auster in altum, deducunt socii nauis et litora complent; prouehimur portu terraeque urbesque recedunt. sacra mari colitur medio gratissima tellus Nereidum matri et Neptuno Aegaeo, quam pius arquitenens oras et litora circum errantem Mycono e celsa Gyaroque reuinxit, immotamque coli dedit et contemnere uentos. huc feror, haec fessos tuto placidissima portu accipit; egressi ueneramur Apollinis urbem. rex Anius, rex idem hominum Phoebique sacerdos, uittis et sacra redimitus tempora lauro occurrit; ueterem Anchisen agnouit amicum. iungimus hospitio dextras et tecta subimus.";
	
	
	print "<table width=\"80%\" cellspacing=\"25\">";
	print "<tr>";
	print "<td width=\"30%\" valign=\"top\" align=\"right\">$link_make_thumbnail</td>";
	print "<td>$explain_thumbnail</td>";
	print "</tr>";
	
	print "<tr>";
	print "<td colspan=\2\">&nbsp;</td>";
	print "</tr>";
	
	print "<tr>";
	print "<td  valign=\"top\" align=\"right\">$link_unsused_image</td>";
	print "<td>$explain_unused</td>";
	print "</tr>";
	
	print "<tr>";
	print "<td colspan=\2\">&nbsp;</td>";
	print "</tr>";
	
	print "<tr>";
	print "<td  valign=\"top\" align=\"right\">$link_init_rub</td>";
	print "<td>$explain_init</td>";
	print "</tr>";
	  
	// end spip format
	print "<div>";
	
	fin_page();
}


?>