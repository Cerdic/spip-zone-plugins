<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_mediatheque_admin_cleaner() {

	global $Submit_confirm, $Submit_cancel;
	global $connect_statut; // string
	global $auteur_session; // assoc array
	
	// init
	$user_display = true;
	$display = false;
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
	//		page title
	// ---------------------------------------------
	print "<H3>Inititalisation des rubriques de la phototek</H3>";
	
	
	if ($Submit_confirm) {
	
		// ---------------------------------------------
		//	 PURGE IMGRUB & DOCRUB	
		// ---------------------------------------------
		$qemp = "TRUNCATE TABLE spip_xtra_docrub";
		$remp = mysql_query($qemp);
		if ($user_display) {print "<p>Effacement des rubriques phototh&eagrave;que : OK<br/>";}
				
		$qemp = "TRUNCATE TABLE spip_xtra_imgrub";
		$remp = mysql_query($qemp);
		if ($user_display) {print "Efffacement des rubriques documenth&eagrave;que : OK</p>";}
		
		// ---------------------------------------------
		//	SCAN ALL SPIP RUBRIQUES AND DUPLICATE IN IMGRUB & DOCRUB 
		// ---------------------------------------------
		if ($user_display) {print "<p><strong>Copie des rubriques</strong><br/>";}
		
		$qrub = "select * from spip_rubriques";
		$rrub = mysql_query($qrub);
		
		while ($obj = mysql_fetch_object($rrub)) {
			$id_rubrique	= $obj -> id_rubrique;
			$id_parent		= $obj -> id_parent;
			$titre			= $obj -> titre;
	
		
		// clean num titre
		$titre = ereg_replace('[0-9]*\. ', '', $titre);
		
		if ($user_display) {print "Copie de la rubrique $id_rubrique => <em>$titre</em><br/>";}
		
		// duplicate arbo for images
			$qins = "insert into spip_xtra_imgrub (
						imgrub_id, 
						imgrub_id_parent,
						imgrub_titre
						)
					values (
						'$id_rubrique',
						'$id_parent',
						'$titre'
					)
					";
					
			if ($writedb) {$rins = mysql_query($qins);}
			if ($display) {print "$qins<br/><br/>";}
			
		// duplicate arbo for docs
			$qins = "insert into spip_xtra_docrub (
						docrub_id, 
						docrub_id_parent,
						docrub_titre
						)
					values (
						'$id_rubrique',
						'$id_parent',
						'$titre'
					)
					";
					
			if ($writedb) {$rins = mysql_query($qins);}
			if ($display) {print "$qins<br/><br/>";}
		}
				
		
		
		
		
		// ---------------------------------------------
		//		SCAN SPIP DOCS AND DISPATCH
		// ---------------------------------------------
		if ($user_display) {print "</p><p><strong>R&eacute;partition des images et documents</strong><br/>";}
		
		$qrub = "select * from spip_documents_articles
				";
		$rrub = mysql_query($qrub);
		
		while ($obj = mysql_fetch_object($rrub)) {
			$id_article		= $obj -> id_article;
			$id_document	= $obj -> id_document;
			
			if ($display) {
				print "<H3>Assign doc $id_document for Article $id_article</H3>";
			}
			
			// get rub of article
			$qrd = "select id_rubrique from spip_articles
					where id_article = '$id_article'
					";
			$rrd = mysql_query($qrd);
			$obj = mysql_fetch_object($rrd);
			$id_rubrique	= $obj -> id_rubrique;
			
			
			if ($display) {
				print "<H3>Assign doc $id_document for Article $id_article in Rubrique $id_rubrique</H3>";
			}
			
			// write in xtra
			$qupd = "update spip_documents
					set id_tek_rub = '$id_rubrique'
					where id_document = '$id_document'
					";
					
			if ($writedb) {$rupd = mysql_query($qupd);}
			if ($display) {print "$qupd<br/><br/>";}
			
			if ($user_display) {print "Affecte Objet $id_document de l'Article $id_article dans la Rubrique $id_rubrique<br/>";}
			
		}
		if ($user_display) {print "<p>";}
		
		// ---------------------------------------------
		//		update parent windoW, IF ANY !
		// ---------------------------------------------
		print "<script>opener.location.reload(true);</script>"; 
		print "<script>window.close();</script>";
		
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
		print "Confirmer l'initialisation des rubriques";
		print "</legend>\r\n";
		
		
		$action2 = "<input type=\"submit\" name=\"Submit_cancel\" value=\"Annuler\" class=\"boutonBOform\" onClick=\"window.close()\">\r\n";

		$action1 = "<input type=\"submit\" name=\"Submit_confirm\" value=\"Confirmer\" class=\"boutonBOform\">\r\n";
		
		print "<br>$action2 &nbsp;&nbsp;&nbsp; $action1 ";
		
		print "</fieldset>\r\n";
		print "</form>\r\n";
		
	}
	
	
	

	
		
	  
	//  fin_page();
}


?>