<?php

function office2spip_affiche_enfants($args) {
	$id_rubrique = $args["args"]["id_rubrique"];


	if ($id_rubrique < 1) return ($args);


	$flag_editable = autoriser('publierdans','rubrique',$id_rubrique);
	if ($flag_editable) {
		
		
		$ret .= "<div style='width: 50%; float: right;'>";
		$ret .= "<div class='cadre cadre-r'>";
		$ret .= "<div class='titrem'>Fabriquer un article à partir d'un fichier Office</div>";
		$ret .= "<div class='cadre_padding'>";
		$ret .= "<form action='index.php' method='post' enctype='multipart/form-data'>";
		$ret .= "<input type='hidden' name='exec' value='traiter_office' />";
		$ret .= "<input type='hidden' name='id_rubrique' value='$id_rubrique' />";
		$ret .= "<div><input type='file' name='fichier' /></div>";
		$ret .= "<div><input type='checkbox' value='oui' name='original' />Joindre le document d’origine à l'article</div>";
		$ret .= "<div><input type='checkbox' value='oui' name='pdf' />Créer et joindre un fichier PDF du document</div>";
		$ret .= "<div style='text-align: right;'><input type='submit' value='Installer' /></div>";
		$ret .= "</form>";
		$ret .= "</div>";
		$ret .= "</div></div><div style='clear: right;'></div>";


		
		$args["data"] = $args["data"].$ret;

	}
	return $args;
}


?>