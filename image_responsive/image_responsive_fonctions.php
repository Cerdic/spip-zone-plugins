<?php

function image_responsive_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("image_responsive.css")."'>\n";

	return $flux;
}

function image_responsive_insert_head($flux) {
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	
	$flux .= "<script>htactif=1;</script>";
	$flux .= "
<script type='text/javascript' src='".find_in_path("image_responsive.js")."'></script>
		";
	
	return $flux;
}

function _image_responsive($img, $taille=120) {
//	$img = $img[0];
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	$src = extraire_attribut($img, "src");
	if (file_exists($src)) {

		$img = vider_attribut($img, "width");
		$img = vider_attribut($img, "height");
		$img = vider_attribut($img, "style");
	
		$img = inserer_attribut($img, "data-src", $src);
		$img = inserer_attribut($img, "class", "image_responsive");
		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille.$1", $src);
		}
		else {
			$src = "index.php?action=image_responsive&amp;img=$src&amp;taille=$taille";
		}
		
		if ($taille == 0) $src = "rien.gif";
		
		


		$img = inserer_attribut($img, "src", $src);
	}
	return $img;
}

function image_responsive($texte, $taille=120) {
	return preg_replace_callback(",(<img\ [^>]*>),", create_function('$matches', 'return _image_responsive($matches[0],'.$taille.');'), $texte);

}

?>