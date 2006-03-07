<?php

	//
	//balise #TABLE_MATIERE
	//
	function balise_TABLE_MATIERE_dist($p) {
		$p->code = "
		AncresIntertitres_compose_table_matiere(
			'\t<li><a href=\"#@url@\">@titre@</a></li>\n',
			'\n<ul>\n@texte@</ul>\n',
			AncresIntertitres_table_matiere(\"retour\")
		)";
		return $p;
	}

?>
