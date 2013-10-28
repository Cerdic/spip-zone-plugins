<?php

function image_responsive_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("image_responsive.css")."'>\n";

	return $flux;
}

function image_responsive_insert_head($flux) {
	$type_urls = lire_meta("type_urls");
	$htactif = 0;
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = 1;
	}
	
	$flux .= "<script>htactif=$htactif;</script>";
	$flux .= "
<script type='text/javascript' src='".find_in_path("image_responsive.js")."'></script>
		";
	
	return $flux;
}

function _image_responsive($img, $taille=120, $dpr=1) {
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
		
		if ($dpr == 0) $img = inserer_attribut($img, "data-dpr", "0");


		$img = inserer_attribut($img, "src", $src);
	}
	return $img;
}

function image_responsive($texte, $taille=120, $dpr=1) {
	return preg_replace_callback(",(<img\ [^>]*>),", create_function('$matches', 'return _image_responsive($matches[0],'.$taille.','.$dpr.');'), $texte);

}

function image_proportions($img, $largeur=1, $hauteur=1, $align="center") {
	
	$l_img = largeur ($img);
	$h_img = hauteur($img);
	$r_img = $h_img / $l_img;	
	$r = $hauteur / $largeur;	
	
	if ($r_img < $r) {
		include_spip("filtres/images_transforme");
		$img = image_recadre($img, $h_img/$r, $h_img, $align);
	} else if ($r_img > $r) {
		include_spip("filtres/images_transforme");
		$img = image_recadre($img, $l_img, $l_img*$r, $align);
	}
	
	return $img;
}



?>