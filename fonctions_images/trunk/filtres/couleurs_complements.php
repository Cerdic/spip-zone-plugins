<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * http://www.paris-beyrouth.org/Une-palette-de-couleurs
 */
function couleur_chroma ($coul, $num) {
	$pos = substr($num, 0, strpos($num, "/")) -  1;
	$tot = substr($num, strpos($num, "/")+1, strlen($num));
	
	include_spip("filtres/images_complements");
	$couleurs = _couleur_hex_to_dec($coul);
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

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

/*
 * Sature (ou desature) une couleur
 */
function couleur_saturer ($coul, $val=1.2) {
	$couleurs = _couleur_hex_to_dec($coul);
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

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

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
	$couleurs = _couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	include_spip("filtres/images_complements");
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

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

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
	$couleurs = _couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = _couleur_rgb2hsl($r,$g,$b);
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

	$rgb = _couleur_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

function couleur_foncerluminosite($coul,$pourcentage=20) {
	$couleurs = _couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = _couleur_rgb2hsl($r,$g,$b);
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];

	$l = $l*(1-$pourcentage/100);

	$rgb = _couleur_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

function couleur_eclaircirluminosite($coul,$pourcentage=20) {	
	$couleurs = _couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsl = _couleur_rgb2hsl($r,$g,$b);
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];

	$l = $l + (1-$l)*(1-(100-$pourcentage)/100);

	$rgb = _couleur_hsl2rgb($h,$s,$l);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];

	$couleurs = _couleur_dec_to_hex($r, $g, $b);

	return $couleurs;
}

/**
 * Melanger 2 couleurs hexa
 *
 * @param string/array $arg1
 *     tableau couleur 1 & 2, ou string couleur 1
 * @param string $arg2
 *     couleur 2 ou pourcentage : nombre entre 0 et 100 (defaut 50)
 * @param int $arg3
 *     pourcentage : nombre entre 0 et 100 (defaut 50)
 * @return string
 *     nouvelle couleur hexa
 *
 * ex: #VAL{888888}|couleur_melanger{ff0066, 75}
 *     #CONFIG{mon_plugin/ma_couleur}|couleur_melanger{#ffffff, 20}
 *     #LISTE{ff3366,888888}|couleur_melanger{20}
**/
function couleur_melanger($arg1, $arg2, $arg3=50) {

	// 2 cas pour les arguments : tableau des 2 couleurs, pourcentage // couleur1, couleur2, pourcentage 
	if (is_string($arg1)){
		$couleur1 = $arg1;
		$couleur2 = $arg2;
		$pourcentage = $arg3;
	} elseif (is_array($arg1)){
		$couleur1 = $arg1[0];
		$couleur2 = $arg1[1];
		$pourcentage = $arg2;
		if (!isset($pourcentage)) $pourcentage=50;
	}; 

	/* => Desactive !
	/* compatibilité : les pourcentages compris entre 0 et 1 fonctionnent
	   comme pour les filtres de couleurs de Spip.
	   Ainsi couleur_melanger{0.25} = couleur_melanger{25} */
	/*
	if ($pourcentage>=0 AND $pourcentage<=1) {
		$coef = 1;
	} else {
		$coef = 100;
	};
	*/
	$coef = 100;

	// verifications
	if (!$couleur2 OR $pourcentage<=0 OR !is_numeric($pourcentage)) return(preg_replace(",^#,","",$couleur1));
	if ($pourcentage>=$coef) return(preg_replace(",^#,","",$couleur2));

	// definition du pourcentage
	$pourcentage1 = ($coef-$pourcentage)/$coef;
	$pourcentage2 = $pourcentage/$coef;

	// conversion
	$couleurs1 = _couleur_hex_to_dec($couleur1);
	$couleurs2 = _couleur_hex_to_dec($couleur2);
	$red1   = $couleurs1["red"];
	$green1 = $couleurs1["green"];
	$blue1  = $couleurs1["blue"];
	$red2   = $couleurs2["red"];
	$green2 = $couleurs2["green"];
	$blue2  = $couleurs2["blue"];

	// melange
	$red   = round($red1*$pourcentage1 + $red2*$pourcentage2);
	$green = round($green1*$pourcentage1 + $green2*$pourcentage2);
	$blue  = round($blue1*$pourcentage1 + $blue2*$pourcentage2);

	$couleur = _couleur_dec_to_hex($red, $green, $blue);
	return $couleur;
}

function couleur_hexa_to_dec($couleur) {
	include_spip('inc/filtres_images_lib_mini');
	return _couleur_hex_to_dec($couleur);
}

?>
