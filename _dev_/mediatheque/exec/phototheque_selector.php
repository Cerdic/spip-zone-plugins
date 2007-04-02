<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------
function exec_phototheque_selector() {

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
	//		init
	// ---------------------------------------------
	$tag_stack = array();
	$light_display = false; // light = no image


	// ---------------------------------------------
	//		get parent img rub
	// ---------------------------------------------
	print "<H3>G&eacute;rer la phototh&egrave;que</H3>";
	print "<p><a href=\"?exec=image_add\">Ajouter une image</a> | <a href=\"?exec=imgrub_add\">Ajouter une rubrique</a></p>";

	$qparent = "select * from spip_xtra_imgrub
			where imgrub_id_parent = 0";
	$rparent = spip_query($qparent);

	while ($obj = mysql_fetch_object($rparent)) {
		$imgrub_id 		= $obj->imgrub_id;
		$imgrub_titre 	= $obj->imgrub_titre;

		print "<span style=\"
					margin-left:$margin;
					display: block;
					position: left;
					font-family: arial;
					font-size: 128%;
					font-weight: bold;
					color: #000000;
					\"
					>
					$imgrub_titre
				</span>";

		mediatheque_diplay_images($imgrub_id, $margin, $light_display);

		mediatheque_get_children($imgrub_id, 1, $light_display);


	}

	print  $rstr;

} // end of exec_image_insert



?>
