<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	Updated Alain 9/01/07
// ---------------------------------------------

function exec_image_delete() {

	global $Submit_confirm, $Submit_cancel;
	global $photo_name, $photo_rubrique, $photo_desc, $photo_credit;
	global $connect_statut; // string
	global $auteur_session; // assoc array

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

	// init
	$display = false;
	$writedb = true;
	$id_image = _request('id_image');

	$id_auteur = $auteur_session['id_auteur'];

	$site_root 		= $_SERVER['DOCUMENT_ROOT'];
	$web_root 		= _DIR_RACINE;
	$dir_img 		= get_dir_img();

	$ikono_site_path 	= "$site_root/drass/"; // local version for test
	// $ikono_site_path 	= "$site_root/"; // real version for use
	$ikono_web_path  	= "$web_root" . "$dir_img";

	

	
	// ---------------------------------------------
	//		put header
	// ---------------------------------------------	
	print display_popup_header("Supprimer une image");


	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	print "<H3>Supprimer une image de la phototh&egrave;que</H3>";

	// ---------------------------------------------
	//		GET DATA
	// ---------------------------------------------
	$qim = "select * from spip_documents
				where id_document = '$id_image'
				";

	$rim = spip_query($qim);

	$obj = mysql_fetch_object($rim);
	$photo_name			= $obj -> titre;
	$fichier			= $obj -> fichier;
	$photo_desc			= $obj -> descriptif;
	$id_tek_rub			= $obj -> id_tek_rub;
	$photo_credit		= $obj -> photo_credit;
	$id_owner			= $obj -> id_owner;
	$id_document		= $obj -> id_document;
	$size				= $obj -> taille;
	$width				= $obj -> largeur;
	$height				= $obj -> hauteur;


	$fichier_lib = eregi_replace('IMG/.*/', '', $fichier);
	$max_width = 400; // to adjust if needed ***
	if ($width > $height) {$width_disp = min($max_width, $width);}
	else {$width_disp = min(ceil($width*($max_width/$height)), $width);}

	// ---------------------------------------------
	//	PRE CHECK DATA
	// ---------------------------------------------
	$formcorrect = true;
	$erreur='';


	$str_img = "img$id_image|";
	$str_doc = "doc$id_image|";

	// ---------------------------------------------
	//	pre check if used in articles
	// ---------------------------------------------
	$qart = "select * from spip_articles
				where texte like '%$str_img%'
				or texte like '%$str_doc%'
				";

	$rart = mysql_query($qart);
	$tart = mysql_numrows($rart);

	if ($erreur != '') {print "<p>$erreur</p>";}



	// ---------------------------------------------
	//	TREATMENT, delete image and article insertions
	// ---------------------------------------------
	if ($Submit_confirm) {
		
		// --------------------
		// delete from spip documents
		// --------------------
		$qdel = "DELETE from spip_documents
				WHERE
					id_document = '$id_image'					
				";

		// --------------------
		// remove references from spip articles
		// --------------------
		$str_img = "img$id_image|";
		$str_doc = "doc$id_image|";

		$qart = "select * from spip_articles
				where texte like '%$str_img%'
				or texte like '%$str_doc%'
				";

		$rart = mysql_query($qart);
		$tart = mysql_numrows($rart);

		if ($tart > 0) {
			while ($obj = mysql_fetch_object($rart)) {
				$titre		= $obj -> titre;
				$id_article	= $obj -> id_article;
				$texte		= $obj -> texte;

				print "<li>treat $titre (Num = $id_article)";
				$texte = ereg_replace("<img$id_image\|(left|right|center)>", "", $texte);
				$texte = ereg_replace("<doc$id_image\|(left|right|center)>", "", $texte);

				$qup = "update spip_articles
						set
							texte = '$texte'
						where
							id_article = '$id_article'
						";

				if ($writedb) {$rup = mysql_query($qup);}
				if ($display) {print "$qup<br/><br/>";}
			}
			print "</li></ul>";
		}

		if ($display) {print "delete $ikono_site_path/$fichier<br/><br/>";}
		
		if ($writedb) {
			$rdel = mysql_query($qdel);
			if ($display) {print "delete $ikono_site_path/$fichier<br/><br/>";}
			unlink("$ikono_site_path/$fichier");
		}

		if ($display) {print "$qdel -> $tdel<br><br>";}

		$link_browse = "<a href=\"?exec=mediatheque_img_browser\">Retour &agrave; la phototh&egrave;que</a>";
		print "<p>Suppression effectu&eacute;e. [$link_browse]</p>";

		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>"; 
		if (!$display) {print "<script>window.close();</script>";}
	}
	
	if ($Submit_cancel) {
		print "<script>window.close();</script>";
	}


	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------

	if (!$Submit_confirm) {

		// is owner
		if ($id_auteur == $id_owner or $auteur_session['statut'] == '0minirezo') {

			print "<form name=\"form\" action=\"\" method=\"POST\">\r\n";
			print "<fieldset>\r\n";
			print "<legend>\r\n";
			print "Confirmez";
			print "</legend>\r\n";

			print "<img src=\"../$fichier\" width=\"$width_disp\">";
			print "<p>$fichier_lib <br />";
			print "Image num&eacute;ro $id_document</p>";

			if ($tart > 0) { // this image is used in any article
				
				if ($tart > 1) {
					$any_article = "les articles";
					$any_suivant = "suivants";
					$any_concerne = "concern&eacute;s";
					$any_insertion = "ses insertions";
				}
				else {
					$any_article = "l'article";
					$any_suivant = "suivant";
					$any_concerne = "concern&eacute;";
					$any_insertion = "son insertion";
				}

				print "Cette image est utilis&eacute;e dans $any_article $any_suivant :<ul>";
				while ($obj = mysql_fetch_object($rart)) {
					$titre			= $obj -> titre;
					$id_article		= $obj -> id_article;

					print "<li>$titre (num&eacute;ro = $id_article)";
				}
				print "</li></ul>";

				print "<p>Merci de confirmer que vous voulez supprimer cettte image de la phototh&egrave;que
				Auquel cas, vous en supprimerez aussi l'appel depuis  $any_article $any_concerne</p>";
				$action1 = "<input type=\"submit\" name=\"Submit_confirm\" value=\"Supprimer l'image et $any_insertion\" class=\"boutonBOform\">\r\n";
			}
			else {

				print "<p>Merci de confirmer que vous voulez supprimer cettte image de la phototh&egrave;que<br/>
				Elle n'est utilis&eacute;e dans aucun article</p>";
				$action1 = "<input type=\"submit\" name=\"Submit_confirm\" value=\"Supprimer l'image\" class=\"boutonBOform\">\r\n";
			}

			$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";
			print "<br>$action2 &nbsp;&nbsp;&nbsp; $action1";

			print "</fieldset>\r\n";
			print "</form>\r\n";
		}
		else {
			print "D&eacute;sol&eacute;, seul l'auteur de l'image peut la supprimer<br/>";
			print "Retour &agrave; la phototh&egrave;que";
		}

		
	}
	print display_popup_footer();
}

?>