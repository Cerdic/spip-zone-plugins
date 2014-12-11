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
<script type='text/javascript' src='".find_in_path("jquery.smartresize.js")."'></script>
<script type='text/javascript' src='".find_in_path("image_responsive.js")."'></script>
		";
	
	return $flux;
}

function image_responsive_header_prive($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("image_responsive.css")."'>\n";
	$flux .= "<script>htactif=false;</script>";

	$flux .= "
<script type='text/javascript' src='".find_in_path("jquery.smartresize.js")."'></script>
<script type='text/javascript' src='".find_in_path("image_responsive.js")."'></script>
		";

	return $flux;
}


function _image_responsive($img, $taille=120, $lazy=0, $vertical = 0) {
	$tailles = explode("/", $taille);
	if (count($tailles) > 1) $taille_defaut = $tailles[0];
	else $taille_defaut = $taille;
	
//	$img = $img[0];
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	$src = extraire_attribut($img, "src");
	$src = preg_replace(",\?[0-9]*$,", "", $src);
	if (file_exists($src)) {
		$l = largeur($src);
		$h = hauteur($src);

		$img = vider_attribut($img, "width");
		$img = vider_attribut($img, "height");
		$img = vider_attribut($img, "style");
	
		//$img = inserer_attribut($img, "src", $src);
		$img = inserer_attribut($img, "data-src", $src);
		$classe = "image_responsive";
		
		if ($vertical == 1) {
			$classe .= " image_responsive_v";
			$v = "v";	
			if ($h < $taille_defaut) $taille_defaut = $h;
		} else {
			$v = "";
			if ($l < $taille_defaut) $taille_defaut = $l;
		}
		
		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $src);
		}
		else {
			$src = "index.php?action=image_responsive&amp;img=$src&amp;taille=$taille_defaut$v";
		}
		
		if ($taille_defaut == 0) $src = "rien.gif";
		if ($lazy == 1) $classe .= " lazy";
		$img = inserer_attribut($img, "data-l", $l);
		$img = inserer_attribut($img, "data-h", $h);
		
		if (count($tailles) > 1) {
			sort($tailles);
			include_spip("inc/json");
			
			$img = inserer_attribut($img, "data-tailles", addslashes(json_encode($tailles)));
		}


		$img = inserer_attribut($img, "src", $src);
		$img = inserer_attribut($img, "class", $classe);
		
		if ($vertical == 0) {
			$r = (($h/$l)*100);
			$img = "<span style='padding:0;padding-bottom:$r%' class='conteneur_image_responsive_h'>$img</span>";
		
		}
	}
	return $img;
}

function image_responsive($texte, $taille=120, $lazy=0, $vertical=0) {
	if (!preg_match("/^<img /i", $texte)) {
		if (strlen($texte) < 256 && file_exists($texte)) $texte = "<img src='$texte'>";
		else return $texte;
	}
	return preg_replace_callback(",(<img\ [^>]*>),", create_function('$matches', 'return _image_responsive($matches[0],"'.$taille.'",'.$lazy.','.$vertical.');'), $texte);
}

function background_responsive($src, $taille=120, $lazy) {
	if (preg_match("/^<img /i", $src)) {
		$src = extraire_attribut($src, "src");
	}
	
		
	$tailles = explode("/", $taille);
	if (count($tailles) > 1) $taille_defaut = $tailles[0];
	else $taille_defaut = $taille;
	
//	$img = $img[0];
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	$src = preg_replace(",\?[0-9]*$,", "", $src);
		
	if (file_exists($src)) {
		$l = largeur($src);
		$h = hauteur($src);
	
		
		//$img = inserer_attribut($img, "src", $src);
		
		if ($l > $h) {
			$ins = "data-italien-src='$src'";
			$ins .= " data-italien-l='$l'";
			$ins .= " data-italien-h='$h'";
			
			$srcp = image_reduire($src, 0, 2400);
			$srcp = image_proportions($srcp, 3, 4);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);
			
			$ins .= "data-portrait-src='$srcp'";
			$ins .= " data-portrait-l='$lp'";
			$ins .= " data-portrait-h='$hp'";
		
		} else {
			$ins = "data-portrait-src='$src'";
			$ins .= " data-portrait-l='$l'";
			$ins .= " data-portrait-h='$h'";


			$srcp = image_reduire($src, 2400, 0);
			$srcp = image_proportions($srcp, 4, 3);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);
			
			$ins .= " data-italien-src='$srcp'";
			$ins .= " data-italien-l='$lp'";
			$ins .= " data-italien-h='$hp'";
		}


		$ins .= " data-responsive='background'";
		

		if ($l < $taille_defaut) $taille_defaut = $l;
		$v = "";
		
		
		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $src);
		}
		else {
			$src = "index.php?action=image_responsive&amp;img=$src&amp;taille=$taille_defaut$v";
		}
		
		
		if ($taille_defaut == 0) $src = "rien.gif";
		if ($lazy == 1) $ins .= " data-lazy='lazy'";

		$ins .= " class='$class'";
		
		if (count($tailles) > 1) {
			sort($tailles);
			include_spip("inc/json");
			
			$ins .= " data-tailles='".addslashes(json_encode($tailles)) ."'";
		}
		
		$ins .= " style='background-image:url($src)'";
		
		return $ins;
	}
	

}


function image_proportions($img, $largeur=16, $hauteur=9, $align="center") {
	
	
	if (!$img OR $hauteur == 0 OR $largeur == 0) return;
	
	$l_img = largeur ($img);
	$h_img = hauteur($img);
	
	if ($l_img == 0 OR $h_img == 0) return $img;
	
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