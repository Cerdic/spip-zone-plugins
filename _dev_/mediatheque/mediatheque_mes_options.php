<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 04/03/2007
// ---------------------------------------------

function mediatheque_texte_parser($texte, $id_article) {
	$doc_in_texte			= array();
	$doc_in_texte_mult		= array();
	
	$doc_in_xtra_table		= array();
	$doc_in_spip_table		= array();
	$doc_in_spip_table_mult	= array();
	
	$in_art_not_table		= array();
	$in_art_not_table_mult	= array();
	$in_table_not_art		= array();
	
	$do_display 			= true;
	$do_writedb				= false;
	
	// scan
	$doc_in_texte 		= scan_img_doc($texte);
	$doc_in_xtra_table 	= scan_xtra_table($id_article);
	$doc_in_spip_table 	= scan_spip_table($id_article);
	
	// count multiples
		$doc_in_texte_mult 		= array_count_values($doc_in_texte);
		$doc_in_spip_table_mult	= array_count_values($doc_in_spip_table);
		
	
		// xtra already get nb copies
	
	if ($do_display) {
		// ctrl
		print "<H3>Doc img in article $id_article</H3>";
		print "<pre>";
		print_r($doc_in_texte);
		print_r($doc_in_texte_mult);
		print "</pre>";

		// ctrl
		print "<H3>Link xtra from article $id_article</H3>";
		print "<pre>";
		print_r($doc_in_xtra_table);
		print "</pre>";

		// ctrl
		print "<H3>Link spip from article $id_article</H3>";
		print "<pre>";
		print_r($doc_in_spip_table);
		print_r($doc_in_spip_table_mult);
		print "</pre>";
	}
	
	// ---------------------------------
	// analyze results
	// ---------------------------------
	
	// 1. In article, not in tables => insert
	$in_art_not_table = array_diff($doc_in_texte, $doc_in_spip_table);
	$in_art_not_table_mult = array_count_values($in_art_not_table);

	for ($i=0; $i < count($in_art_not_table); $i++) {
		$id_document 	= $in_art_not_table[$i];
		$num_copie 		= $in_art_not_table_mult[$in_art_not_table[$i]];

		// keep tracks in spip_documents for classical boucle article
		$qins = "insert into spip_documents_articles (
				id_document,
				id_article
				)
				values (
				'$id_document',
				'$id_article'
				)		
				";
		
		if ($do_writedb) {$rins = mysql_query($qins);}
		if ($do_display) {print "$qins <br><br>";}
		
		// keep tracks in xtra document for mediatheque mechanic
		$qins = "insert into spip_xtra_documents_articles (
				id_document,
				id_article,
				num_copie,
				alt,
				longdesc,
				legende,
				langue,
				position
				)
				values (
				'$id_document',
				'$id_article',
				'$num_copie',
				'$alt',
				'$longdesc',
				'$legende',
				'$langue',
				'$position'
				)		
				";
		
		if ($do_writedb) {$rins = mysql_query($qins);}
		if ($do_display) {print "$qins <br><br>";}
		
	}
	
	// 2. In table, not in article => delete
		$in_table_not_art = array_diff($doc_in_spip_table, $doc_in_texte);
		
		
	
	if ($do_display) {
		// ctrl
		print '<HR><H3>in article not in table => b copie = $doc_in_texte_mult[$in_art_not_table[$id]] ex</H3>';
		print "<pre>";
		print_r($in_art_not_table);
		print "<br>";
		print_r($in_art_not_table_mult);
		print "</pre>";
		
	
		// ctrl
		print "<H3>in table not in article => Delete</H3>";
		print "<pre>";
		print_r($in_table_not_art);
		print "</pre>";
		
		
		

		exit;
	}
}




/* =============================================
*  FUNCTION SCAN IMAGES
*  search for :
	[doc123]
	[doc123|...]
	[toto->doc123|...]
	<doc123|...>
	and idem with img
// =============================================*/
function scan_img_doc($text) {
	$arr_img_names 		= array();
	$arr_img_id 		= array();
	$arr_doc_id			= array();
	$arr_spip_doc_id 	= array();
	$img_list			= array();
	$doc_list			= array();
	$arr_img_frag 		= array();
	$arr_spip_doc_id	= array();
	$arr_doc_names		= array();
	$arr_full_doc_id	= array();
	$match				= array();
	
	$ctr 				= 0;
	$ctrb 				= 0;
	
	$do_display 		= true;
		
	// normalize 
	$text = stripslashes($text);
	
	if ($do_display) {
		print "<h3>init</h3>$text<br><br><br>";
		$text_html = htmlentities($text);

		print "<H3>Brut</H3>";
		$text_html = str_replace(">", ">\r", $text_html);
		print "<pre>$text_html</pre>";
	}
	
	// ---------------------------------------------
	//		GET IMAGES TAGS
	// ---------------------------------------------
	preg_match_all("/<img.*\>/isU", $text, $match); 
	
	if ($do_display) {print "<HR><H2>Scanning Images tags</H2>";}
	
	for ($i=0; $i< count($match[0]); $i++) {
		$img_list[$i] = $match[0][$i];
		$img_lib = htmlentities($img_list[$i]);
		
		if ($do_display) {print "<H3><b>$i. $img_lib</b></H3>";}
		
		// ---------------------------------------------
		//	 FRAG HTML IMG TAG
		// ---------------------------------------------
		$arr_img_frag = explode(" ", $img_list[$i]);
		
		for ($k = 0; $k < count($arr_img_frag); $k++) {
			if ($do_display) {print "frag $k. " . $arr_img_frag[$k] . "<br>";}
		
			// ---------------------------------------------
			//	look for src
			// --------------------------------------------- 
			if (ereg("src", $arr_img_frag[$k])) {
				$marker = "/(src=\"|\')(.*(jpg|gif))(\"|\')/";
				$name = extract_pattern($arr_img_frag[$k], $marker, 2);
				
				if ($do_display) {print "path = $name<br>";}
				
				// slice name
				$arr_name = explode('/', $name);
				$name = $arr_name[count($arr_name)-1];
				
				$arr_doc_names[$ctr] = $name;
				$ctr++;
				
				if ($do_display) {print "name = $name<br><br>"; }
			}
			
		} // end for k <=> html doc fragged
		
		// ---------------------------------------------
		//	 FRAG SPIP IMG TAG
		//   form : <img123|...>
		//   <doc303|left|alt=alternative|langue=fr>
		// ---------------------------------------------
		if (ereg('\|', $img_list[$i])) {
			$arr_img_frag = explode("|", $img_list[$i]);
			$arr_spip_doc_id[$ctrb] = ereg_replace('img|<', '', $arr_img_frag[0]);
			
			print $arr_spip_doc_id[$ctrb];
			$ctrb++;
			
		}
		
	} // end for i <=> all img captured
	
	
	// ---------------------------------------------
	//		GET HTML DOC TAGS
	// ---------------------------------------------
	$match				= array();
	preg_match_all("/<a href.*\/img\/.*\>/isU", $text, $match); 
	
	if ($do_display) {print "<HR><H2>Scanning HTML DOCS tags</H2>";}
	
	for ($i=0; $i< count($match[0]); $i++) {
		$doc_list[$i] = $match[0][$i];
		$doc_lib = htmlentities($doc_list[$i]);
		
		if ($do_display) {print "<H3><b>$i. $doc_lib</b></H3>";}
		
		// ---------------------------------------------
		//	 FRAG HTML DOC TAG
		//   form : <a href=.../img/...>
		// ---------------------------------------------//
		$arr_doc_frag = explode(" ", $doc_list[$i]);
		
		for ($k = 0; $k < count($arr_doc_frag); $k++) {
			if ($do_display) {print "frag $k. " . $arr_doc_frag[$k] . "<br>";}
		
			// ---------------------------------------------
			//	look for href, usually after a, but sometimes exotc code...
			// --------------------------------------------- 
			if (ereg("href", $arr_doc_frag[$k])) {
				// slice
				$arr_sliced = explode('/', $arr_doc_frag[$k]);
				$name = $arr_sliced[count($arr_sliced)-1];
				
				$arr_doc_names[$ctr] = str_replace('"', '', $name);
				$ctr++;
				
				if ($do_display) {print "name = $name<br><br>"; }
			}
			
		} // end for k <=> html doc fragged

	}	// end for i <=> all html doc captured


	// ---------------------------------------------
	//	 GET SPIP DOC TAG form 1 : <doc123|...>
	//   <doc303|floatleft|alt=alternative|titrelegende=titre|legende=la légende|langue=fr>
	// ---------------------------------------------
	$match				= array();
	preg_match_all("/<doc.*\|.*>/isU", $text, $match); 
	
	if ($do_display) {print "<HR><H2>Scanning SPIP DOCS tags form 1</H2>";}
	
	$ctrc = 0;
	for ($i=0; $i< count($match[0]); $i++) {
		$doc_list[$i] = ereg_replace('<|>', '', $match[0][$i]);
		$doc_lib = htmlentities($doc_list[$i]);
		
		if ($do_display) {print "<H3><b>$i. $doc_lib</b></H3>";}
	
		$arr_doc_frag = explode("|", $doc_list[$i]);
		
		if ($do_display) {
			// ctrl
			print '<HR><H3>frag in </H3>';
			print "<pre>";
			print_r($arr_doc_frag);
			print "<br>";
			print "</pre>";
		}
		
		// detect each fragment
		for ($i=0; $i< count($arr_doc_frag); $i++) {
			$fragment = $arr_doc_frag[$i];
			
			// doc and id
			if (ereg('doc|img', $fragment)) {
				$arr_doc_items[$ctrc]['docid'] = ereg_replace('doc|img', '', $fragment);
			}
			
			// position
			if (ereg('left|right|center', $fragment)) {
				$arr_doc_items[$ctrc]['position'] = $fragment;
			}
			
			// alt
			if (ereg('alt=', $fragment)) {
				$arr_doc_items[$ctrc]['alt'] = addslashes(stripslashes(ereg_replace('alt=', '', $fragment)));
			}
			
			// titrelegende
			if (ereg('titrelegende=', $fragment)) {
				$arr_doc_items[$ctrc]['titre'] = addslashes(stripslashes(ereg_replace('titrelegende=', '', $fragment)));
			}
			
			// legende
			if (ereg('legende=', $fragment)) {
				$arr_doc_items[$ctrc]['legende'] = addslashes(stripslashes(ereg_replace('legende=', '', $fragment)));
			}
			
			// langue
			if (ereg('langue=', $fragment)) {
				$arr_doc_items[$ctrc]['langue'] = ereg_replace('langue=', '', $fragment);
			}
			
			
		}
		
		
		
		$arr_spip_doc_id[$ctrb] = ereg_replace('doc|<', '', $arr_doc_frag[0]);

		print $arr_spip_doc_id[$ctrb];
		$ctrc++;

	}
	
	if ($do_display) {
		// ctrl
		print '<HR><H3>result doc items</H3>';
		print "<pre>";
		print_r($arr_doc_items);
		print "<br>";
		print "</pre>";
	}

	// ---------------------------------------------
	//	 GET SPIP DOC TAG form 2 : [...->doc123]
	// ---------------------------------------------
	$match				= array();
	preg_match_all("/\->(doc|img).*]/isU", $text, $match); 
	
	if ($do_display) {print "<HR><H2>Scanning SPIP IMG DOCS tags form 2</H2>";}
	
	for ($i=0; $i< count($match[0]); $i++) {
		$doc_list[$i] = $match[0][$i];
		$doc_lib = htmlentities($doc_list[$i]);
		
		if ($do_display) {print "<H3><b>$i. $doc_lib</b></H3>";}
	
		$arr_doc_frag = explode("->", $doc_list[$i]);
		$arr_spip_doc_id[$ctrb] = ereg_replace('doc|img|]', '', $arr_doc_frag[1]);
		$arr_spip_doc_id[$ctrb]  = ereg_replace('\|.*', '', $arr_spip_doc_id[$ctrb]);

		print $arr_spip_doc_id[$ctrb];
		$ctrb++;

	}
	
	// ---------------------------------------------
	//	 GET SPIP DOC TAG form 3 : [doc or img|...]
	// ---------------------------------------------
	$match				= array();
	preg_match_all("/\[(doc|img).*]/isU", $text, $match); 
	
	if ($do_display) {print "<HR><H2>Scanning SPIP IMG DOCS tags form 3</H2>";}
	
	for ($i=0; $i< count($match[0]); $i++) {
		$doc_list[$i] = $match[0][$i];
		$doc_lib = htmlentities($doc_list[$i]);
		
		if ($do_display) {print "<H3><b>$i. $doc_lib</b></H3>";}
	
		$arr_doc_frag = explode("|", $doc_list[$i]);
		$arr_spip_doc_id[$ctrb] = ereg_replace('doc|img|]|\[', '', $arr_doc_frag[0]);

		print $arr_spip_doc_id[$ctrb];
		$ctrb++;

	}
	
	
	if ($do_display) {print "<br><HR><br>";}
	
	
	// ---------------------------------------------
	// CONVERT IMG&DOC NAMES FROM HTML SCAN IN IDs
	// ---------------------------------------------
		for ($u = 0; $u < count($arr_doc_names); $u++) {
			$name = $arr_doc_names[$u];
			$qid = "select id_document from spip_documents
					where fichier like '%$name'";
			
			$rid = mysql_query($qid);
			$obj = mysql_fetch_object($rid);
			$arr_doc_id[$u]	= $obj -> id_document;

		}		
	
		if (count($arr_doc_names) > 0) {
			$arr_doc_id = array_filter($arr_doc_id); // remove empty cells
		}
		
	// ---------------------------------------------
	// MERGE IDs
	// ---------------------------------------------
		$arr_full_doc_id = array_merge($arr_spip_doc_id, $arr_doc_id);
		
		
		
	return $arr_full_doc_id;
	
}

// =============================================
//  FUNCTION scan_xtra_table
// =============================================
function scan_xtra_table($id_article) {
	$array_res = array();

	$qart = "select * from spip_xtra_documents_articles
			where id_article = '$id_article'";

	$rart = mysql_query($qart);

	$ctr = 0;
	while ($obj = mysql_fetch_object($rart)) {
		$array_res[$ctr][0]		= $obj -> id_document;
		$array_res[$ctr][1]		= $obj -> nb_copie;
		$ctr++;
	}
	return $array_res;
}

// =============================================
//  FUNCTION scan_spip_table
// =============================================
function scan_spip_table($id_article) {
	$array_res = array();

	$qart = "select * from spip_documents_articles
			where id_article = '$id_article'";

	$rart = mysql_query($qart);

	$ctr = 0;
	while ($obj = mysql_fetch_object($rart)) {
		$array_res[$ctr]		= $obj -> id_document;
		$ctr++;
	}
	return $array_res;
}


// =============================================
//		extract pattern
// =============================================
function extract_pattern($any_text, $pattern, $rank = 0) {
	if (preg_match($pattern, $any_text, $matches)) {
		return $matches[$rank];
	}
}

// =============================================
//		filters for array
// =============================================
function not_empty_cell ($var) {
	return ($var != '' and isset($var));
}

/*
		// export to spip document *********
		$qim = "insert into spip_documents (
				id_document,
				id_type,
				titre,
				date,
				descriptif,
				fichier,
				taille,
				largeur,
				hauteur,
				mode
					)
				values (
				'',
				'$type',
				'',
				'$datetime',
				'$alt',
				'$fichier',
				'$taille',
				'$width',
				'$height',
				'vignette'
				)
				";
		if ($do_write_db) {		
			process_sql($qim, $rim, $tim, "maes");
			
			$last_id = mysql_insert_id();
			//$last_id = 1; // test
			
			$qda = "insert into spip_documents_articles (
				id_document,
				id_article
			)
			values (
				$last_id,
				$ah_spip_id
			)
			";
			process_sql($qda, $rda, $tda, "maes");
			
			if($do_display) {
				print "$qim -> $tim<br> $qda -> $tda<br><br>";
			}
			
		}	// end do write db
		*/
		
		//$img_num = $i + 1; // nb images inserted
?>
