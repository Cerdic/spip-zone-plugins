<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_mediatheque_scandoc() {

	global $Submit_photo, $Submit_cancel;
	global $photo_name, $photo_rubrique, $photo_desc, $photo_credit;
	global $connect_statut; // string
	global $auteur_session; // assoc array

	// init
	$display = false;
	$writedb = true;

	doc_by_article();
	
	

	function article_by_doc() {
		$qse = "select * from spip_documents
		order by id_type, id_document";

		$rse = mysql_query($qse);

		print "<H1>Catalogue des $tse documents</H1>";



		$prev_type = '';
		$number = 1;
		$article_parent = '';

		while ($obj = mysql_fetch_object($rse)) {
			$doc_id_type		= $obj -> id_type;
			$doc_titre			= $obj -> titre;
			$doc_fichier		= $obj -> fichier;
			$id_document		= $obj -> id_document;

			$doc_path = "../../$doc_fichier";


			// ---------------------------------------------
			//		used by ?
			// ---------------------------------------------
			$qart = "select * from spip_documents_articles
				where id_document = '$id_document'
				";

			$rart = mysql_query($qart);

			while ($objd = mysql_fetch_object($rart)) {
				$id_article		= $objd -> id_article;
				$article_parent .= "$id_article, ";
			}


			// ---------------------------------------------
			//		prepare display
			// ---------------------------------------------

			if ($doc_id_type != $prev_type) {

				// close previous table
				if ($prev_type != '') {
					print "</table>\r\r";
					$number = 1;
				}

				//get type lib
				$qty = "select * from spip_types_documents
				where id_type = '$doc_id_type'
				";

				$rty = mysql_query($qty);

				$obj = mysql_fetch_object($rty);
				$file_type			= $obj -> titre;


				print "<h2>$file_type</h2>";
				print "<table  width='90%'>";

				print "<tr>\r";

				print "<th>\r";
				print "id_doc";
				print "</th>\r";

				print "<th width='30%'>\r";
				print "Nom";
				print "</th>\r";

				print "<th>\r";
				print "Fichier";
				print "</th>\r";

				print "<th>\r";
				print "Used In";
				print "</th>\r";

				/*
				print "<th>\r";
				print "Num";
				print "</th>\r";
				*/
				print "</tr>\r";
				$prev_type = $doc_id_type;
			}
			else {
				$number++;
			}

			$link_doc = "<a href=\"$doc_path\">$doc_titre</a>";


			if ($doc_titre == '') {
				$doc_titre = "&nbsp";
			}

			print "<tr>\r";

			print "<td>\r";
			print "$id_document";
			print "</td>\r";

			print "<td>\r";
			print "$link_doc";
			print "</td>\r";

			print "<td>\r";
			print "$doc_fichier";
			print "</td>\r";

			print "<td>\r";
			print "$article_parent";
			print "</td>\r";

			/*
			print "<td>\r";
			print "$number";
			print "</td>\r";
			*/
			print "</tr>\r";

			$article_parent = '';

		} // end while one doc

		print "</table>\r";



	}

}


function doc_by_article() {

	// reverse scan list article then doc

	$qart = "select * from spip_articles order by id_article desc";
	$rart = mysql_query($qart);

	while ($objd = mysql_fetch_object($rart)) {
		$doc_list = '';
		$id_article		= $objd -> id_article;


		$qdoc = "select * from spip_documents_articles
				where id_article = '$id_article'
				order by id_document"; 
		
		$rdoc = mysql_query($qdoc);

		while ($obj = mysql_fetch_object($rdoc)) {
			$doc_list		.= $obj -> id_document . ', ';
		}
	
	print "<H2>article $id_article</H2>";
	print "<p>$doc_list</p>";

	}

}
?>