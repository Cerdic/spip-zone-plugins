<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * http://www.paris-beyrouth.org/Une-palette-de-couleurs
 */
function couleur_chroma ($coul, $num) {

	$pos = substr($num, 0, strpos($num, "/")) -  1;
	$tot = substr($num, strpos($num, "/")+1, strlen($num));

	include_spip("inc/filtres_images");
	include_spip("filtres/images_complements");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsv = image_rgb2hsv($r,$g,$b);
	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];

	$h = $h + (1/$tot)*$pos;
	if ($h > 1) $h = $h - 1;

	$rgb = image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

/*
 * Sature (ou desature) une couleur
 */
function couleur_saturer ($coul, $val=1.2) {

	include_spip("inc/filtres_images");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	include_spip("filtres/images_complements");
	$hsv = image_rgb2hsv($r,$g,$b);

	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];

	$s = $s * $val;
	if ($s > 1) $s = 1;

	$rgb = image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

/**
 * Affiche un tableau avec les couleurs de l'arbre chromatique
 * dont le d&eacute;coupage est pass&eacute; en param&egrave;tre.
 */
function couleur_tableau_chroma($coul, $tot=6, $debut=1, $fin=0){

	include_spip("inc/filtres");
	if($fin==0) $fin = $tot;

	$retour = "<div style='width:300px;'>\n";
	$retour .= "<div style='background-color:#$coul; width:50px;float:left;'>$coul</div>\n";
	for($pos = $debut; $pos<=$fin; $pos++){
		$cc = couleur_chroma($coul, "$pos/$tot");
		$retour .= "<div style='background-color:#$cc; width:50px;float:left;'>$cc</div>\n";
	}
	$retour .= "</div>\n";

	return $retour;
}

/*
 * Change la teinte d'une couleur
 *
 * $val entre +/- 0 à 360
 */
function couleur_teinter ($coul, $val=30) {

	include_spip("inc/filtres_images");
	include_spip("filtres/images_complements");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsv = image_rgb2hsv($r,$g,$b);
	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];

	$h = $h*360 + $val;
	$h = ($h<0)?$h+360:$h;
	$h = ($h % 360);
	$h = ($h == 0)?$h:$h/360;

	$rgb = image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

/*
 *
 * Permet d'éclaircir une couleur si elle est foncee
 * ou de la foncer si elle est claire.
 * La valeur par defaut est 20% (sur une echelle de 0 à 100%).
 * Le troisieme parametre permet de rendre plus lumineux ou plus sombre ce qui l'est deja
 */
function couleur_inverserluminosite($coul,$pourcentage=20, $intensifier=false) {
	include_spip("inc/filtres_images");
	include_spip('filtres/images_complements');
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = image_rgb2hsl($r,$g,$b);
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];

	if (!$intensifier) {
		if ($l < 0.5) {
			$l = $l + (1-$l)*(1-(100-$pourcentage)/100);
		} else {
			$l = $l*(1-$pourcentage/100);
		}
	} else {
		if ($l >= 0.5) {
			$l = $l + (1-$l)*(1-(100-$pourcentage)/100);
		} else {
			$l = $l*(1-$pourcentage/100);
		}
	}

	$rgb = image_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

function couleur_foncerluminosite($coul,$pourcentage=20) {
	include_spip("inc/filtres_images");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = image_rgb2hsl($r,$g,$b);
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];

	$l = $l*(1-$pourcentage/100);

	$rgb = image_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

function couleur_eclaircirluminosite($coul,$pourcentage=20) {
	include_spip("inc/filtres_images");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = image_rgb2hsl($r,$g,$b);
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];

	$l = $l + (1-$l)*(1-(100-$pourcentage)/100);

	$rgb = image_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

?>